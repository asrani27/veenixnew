<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Models\ApiSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ConvertVideoToHlsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $inputPath;
    protected string $outputDir;
    protected string $filename;
    protected ?string $movieId;

    public function __construct(string $inputPath, string $outputDir, string $filename, ?string $movieId = null)
    {
        $this->inputPath = $inputPath;
        $this->outputDir = $outputDir;
        $this->filename = pathinfo($filename, PATHINFO_FILENAME);
        $this->movieId = $movieId;
    }

    /**
     * Jalankan proses konversi.
     */
    public function handle(): void
    {
        try {
            Log::info("🎬 Memulai konversi HLS untuk: {$this->filename}");

            // Jika movieId ada, ambil slug dari tabel movie untuk outputDir
            if ($this->movieId) {
                $movie = Movie::find($this->movieId);
                if ($movie && $movie->slug) {
                    // Gunakan slug dari movie sebagai outputDir
                    $this->outputDir = 'hls_videos/' . $movie->slug;
                    Log::info("📂 Menggunakan slug dari movie: {$movie->slug} untuk output directory");
                } else {
                    Log::warning("⚠️ Movie dengan ID {$this->movieId} tidak ditemukan atau tidak memiliki slug, menggunakan outputDir default");
                }
            }

            // Path absolut
            $inputFile = storage_path("app/{$this->inputPath}");
            $outputDir = storage_path("app/{$this->outputDir}");

            if (!file_exists($inputFile)) {
                throw new \Exception("Video file not found: {$inputFile}");
            }

            // Buat folder output untuk HLS
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0777, true);
            }

            $outputM3u8 = "{$outputDir}/index.m3u8";

            // Jalankan FFmpeg langsung via shell
            $cmd = sprintf(
                'ffmpeg -i "%s" -profile:v baseline -level 3.0 -start_number 0 -hls_time 6 -hls_list_size 0 -f hls "%s" -y 2>&1',
                $inputFile,
                $outputM3u8
            );

            Log::info("🔧 Menjalankan FFmpeg: {$cmd}");
            exec($cmd, $output, $returnVar);

            if ($returnVar !== 0) {
                Log::error("❌ Gagal konversi video: " . implode("\n", $output));
                throw new \Exception("FFmpeg gagal menjalankan konversi.");
            }

            Log::info("✅ Konversi HLS selesai: {$outputM3u8}");

            // Upload hasil konversi ke Wasabi storage
            $this->uploadToWasabi($outputDir, $outputM3u8);
        } catch (\Throwable $e) {
            Log::error("🚨 Error ConvertVideoToHlsJob: " . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Upload hasil konversi HLS ke Wasabi storage
     */
    private function uploadToWasabi(string $localDir, string $m3u8Path): void
    {
        try {
            Log::info("🚀 Memulai upload ke Wasabi storage...");

            // Dapatkan semua file HLS dalam direktori
            $files = glob($localDir . '/*');
            $uploadedFiles = [];

            if (empty($files)) {
                Log::warning("⚠️ Tidak ada file yang ditemukan di: {$localDir}");
                return;
            }

            // Tentukan path di Wasabi berdasarkan movie slug
            $wasabiPath = '';
            if ($this->movieId) {
                $movie = Movie::find($this->movieId);
                if ($movie && $movie->slug) {
                    $wasabiPath .= '/' . $movie->slug;
                }
            }

            Log::info("📂 Upload path di Wasabi: {$wasabiPath}");

            // Upload setiap file ke Wasabi menggunakan dynamic disk
            foreach ($files as $file) {
                $filename = basename($file);
                $remotePath = $wasabiPath . '/' . $filename;

                // Upload file ke Wasabi menggunakan disk yang dikonfigurasi secara dinamis
                $uploaded = Storage::disk('wasabi')->put($remotePath, file_get_contents($file));

                if ($uploaded) {
                    $uploadedFiles[] = $filename;
                    Log::info("✅ File uploaded: {$filename} -> {$remotePath}");
                } else {
                    Log::error("❌ Gagal upload file: {$filename}");
                }
            }

            // Hapus file lokal setelah upload berhasil
            $this->cleanupLocalFiles($localDir);

            // Hapus file original dari uploads setelah proses selesai
            $this->cleanupOriginalUploadFile();

            Log::info("🎉 Upload ke Wasabi selesai! Total file: " . count($uploadedFiles));
        } catch (\Throwable $e) {
            Log::error("🚨 Error upload ke Wasabi: " . $e->getMessage());

            // Update status movie jika ada error
            if ($this->movieId) {
                $movie = Movie::find($this->movieId);
                if ($movie) {
                    $movie->update([
                        'hls_status' => 'failed',
                        'hls_error' => $e->getMessage()
                    ]);
                }
            }

            throw $e;
        }
    }

    /**
     * Hapus file lokal setelah upload berhasil
     */
    private function cleanupLocalFiles(string $localDir): void
    {
        try {
            $files = glob($localDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    Log::info("🗑️ File lokal dihapus: " . basename($file));
                }
            }

            // Hapus direktori jika kosong
            if (is_dir($localDir)) {
                rmdir($localDir);
                Log::info("🗑️ Direktori lokal dihapus: {$localDir}");
            }
        } catch (\Throwable $e) {
            Log::warning("⚠️ Gagal menghapus file lokal: " . $e->getMessage());
        }
    }

    /**
     * Hapus file original dari folder uploads setelah proses konversi dan upload selesai
     */
    private function cleanupOriginalUploadFile(): void
    {
        try {
            Log::info("🔍 Mulai cleanup file original, inputPath: {$this->inputPath}");

            $originalFilePath = storage_path("app/{$this->inputPath}");
            Log::info("🔍 Path lengkap file original: {$originalFilePath}");

            if (file_exists($originalFilePath)) {
                $fileSize = filesize($originalFilePath);
                Log::info("📁 File ditemukan, ukuran: " . number_format($fileSize / 1024 / 1024, 2) . " MB");

                if (unlink($originalFilePath)) {
                    Log::info("🗑️ File original dari uploads dihapus: " . basename($originalFilePath));
                } else {
                    Log::error("❌ Gagal menghapus file original: " . basename($originalFilePath));
                }
            } else {
                Log::warning("⚠️ File original tidak ditemukan: {$originalFilePath}");

                // Cek jika file ada di uploads langsung
                $alternativePath = storage_path("app/uploads/" . basename($this->inputPath));
                Log::info("🔍 Cek path alternatif: {$alternativePath}");

                if (file_exists($alternativePath)) {
                    if (unlink($alternativePath)) {
                        Log::info("🗑️ File original dari uploads (alternatif) dihapus: " . basename($alternativePath));
                    } else {
                        Log::error("❌ Gagal menghapus file original (alternatif): " . basename($alternativePath));
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("🚨 Error cleanupOriginalUploadFile: " . $e->getMessage());
            Log::error("🚨 Stack trace: " . $e->getTraceAsString());
        }
    }
}
