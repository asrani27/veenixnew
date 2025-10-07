<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class VideoConversionService
{
    /**
     * Convert video to HLS format and upload to Wasabi
     *
     * @param string $videoPath
     * @param string $movieSlug
     * @param string $movieId
     * @return array
     * @throws \Exception
     */
    public function convertToHlsAndUpload(string $videoPath, string $movieSlug, string $movieId): array
    {
        try {
            // Create temporary directory
            $tempDir = storage_path('app/temp/hls_' . $movieId . '_' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Get video info
            $videoInfo = $this->getVideoInfo($videoPath);

            // Convert to HLS
            $this->convertToHls($videoPath, $tempDir, $videoInfo);

            // Upload HLS files to Wasabi
            $uploadedFiles = $this->uploadHlsFiles($tempDir, $movieSlug);

            // Clean up temporary files
            $this->cleanupTempFiles($tempDir);

            return $uploadedFiles;

        } catch (\Exception $e) {
            // Clean up on error
            if (isset($tempDir) && file_exists($tempDir)) {
                $this->cleanupTempFiles($tempDir);
            }
            throw $e;
        }
    }

    /**
     * Convert video to HLS format using FFmpeg
     *
     * @param string $inputPath
     * @param string $outputDir
     * @param array $videoInfo
     * @return void
     */
    private function convertToHls(string $inputPath, string $outputDir, array $videoInfo): void
    {
        $outputFile = $outputDir . '/playlist.m3u8';
        $segmentPattern = $outputDir . '/segment_%03d.ts';

        // Build FFmpeg command for HLS conversion
        $ffmpegCmd = [
            'ffmpeg',
            '-i', $inputPath,
            '-c:v', 'libx264',
            '-c:a', 'aac',
            '-profile:v', 'main',
            '-crf', '23',
            '-preset', 'medium',
            '-movflags', '+faststart',
            '-hls_time', '10',
            '-hls_list_size', '0',
            '-hls_segment_filename', $segmentPattern,
            '-f', 'hls',
            '-y',
            $outputFile
        ];

        $process = new Process($ffmpegCmd);
        $process->setTimeout(3600); // 1 hour timeout
        $process->run();

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            $errorOutput = $process->getErrorOutput();
            Log::error('FFmpeg conversion failed', [
                'error' => $errorOutput,
                'output' => $process->getOutput()
            ]);
            throw new \Exception('FFmpeg conversion failed: ' . $errorOutput);
        }

        // Verify output files exist
        if (!file_exists($outputFile)) {
            throw new \Exception('HLS playlist file was not created');
        }

        Log::info('HLS conversion completed', [
            'input' => $inputPath,
            'output' => $outputFile
        ]);
    }

    /**
     * Upload HLS files to Wasabi storage
     *
     * @param string $tempDir
     * @param string $movieSlug
     * @return array
     */
    private function uploadHlsFiles(string $tempDir, string $movieSlug): array
    {
        $uploadedFiles = [];
        $files = scandir($tempDir);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $localPath = $tempDir . '/' . $file;
            $wasabiPath = $movieSlug . '/' . $file;

            // Upload file to Wasabi
            $contents = file_get_contents($localPath);
            Storage::disk('wasabi')->put($wasabiPath, $contents);

            // Get file info
            $fileInfo = [
                'type' => $this->getFileType($file),
                'local_path' => $localPath,
                'wasabi_path' => $wasabiPath,
                'filename' => $file,
                'size' => filesize($localPath),
                'url' => $this->getWasabiUrl($wasabiPath)
            ];

            $uploadedFiles[] = $fileInfo;

            Log::info('File uploaded to Wasabi', [
                'file' => $file,
                'path' => $wasabiPath,
                'size' => $fileInfo['size']
            ]);
        }

        return $uploadedFiles;
    }

    /**
     * Determine file type based on extension
     *
     * @param string $fileName
     * @return string
     */
    private function getFileType(string $fileName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'm3u8':
                return 'playlist';
            case 'ts':
                return 'segment';
            default:
                return 'other';
        }
    }

    /**
     * Clean up temporary files and directory
     *
     * @param string $tempDir
     * @return void
     */
    private function cleanupTempFiles(string $tempDir): void
    {
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($tempDir);
        }

        Log::info('Temporary files cleaned up', ['directory' => $tempDir]);
    }

    /**
     * Get Wasabi URL for file
     *
     * @param string $path
     * @return string
     */
    private function getWasabiUrl(string $path): string
    {
        $wasabiConfig = config('filesystems.disks.wasabi');
        $bucket = $wasabiConfig['bucket'] ?? '';
        $endpoint = $wasabiConfig['endpoint'] ?? 'https://s3.us-east-1.wasabisys.com';
        
        return $endpoint . '/' . $bucket . '/' . $path;
    }

    /**
     * Get video information using FFprobe
     *
     * @param string $inputPath
     * @return array
     */
    public function getVideoInfo(string $inputPath): array
    {
        $ffmpegCmd = [
            'ffprobe',
            '-v', 'quiet',
            '-print_format', 'json',
            '-show_format',
            '-show_streams',
            $inputPath
        ];

        $process = new Process($ffmpegCmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = json_decode($process->getOutput(), true);

        $videoStream = null;
        $audioStream = null;

        foreach ($output['streams'] as $stream) {
            if ($stream['codec_type'] === 'video' && !$videoStream) {
                $videoStream = $stream;
            } elseif ($stream['codec_type'] === 'audio' && !$audioStream) {
                $audioStream = $stream;
            }
        }

        return [
            'duration' => $output['format']['duration'] ?? 0,
            'size' => $output['format']['size'] ?? 0,
            'bitrate' => $output['format']['bit_rate'] ?? 0,
            'video' => $videoStream,
            'audio' => $audioStream
        ];
    }
}
