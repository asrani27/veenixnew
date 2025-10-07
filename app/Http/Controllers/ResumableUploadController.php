<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Movie;
use App\Jobs\ConvertVideoToHlsJob;

class ResumableUploadController extends Controller
{
    /**
     * Handle Resumable.js upload requests
     */
    public function upload(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'resumableIdentifier' => 'required|string',
            'resumableFilename' => 'required|string',
            'resumableTotalSize' => 'required|integer|min:1',
            'resumableTotalChunks' => 'required|integer|min:1',
            'resumableChunkNumber' => 'required|integer|min:1',
            'resumableChunkSize' => 'required|integer|min:1',
            'resumableCurrentChunkSize' => 'required|integer|min:1',
            'file' => 'required|file',
            'movie_id' => 'required|string|exists:movie,id'
        ]);

        if ($validator->fails()) {
            Log::error('Resumable upload validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Invalid request',
                'details' => $validator->errors()->toArray()
            ], 400);
        }

        $identifier = $request->input('resumableIdentifier');
        $filename = $request->input('resumableFilename');
        $totalChunks = $request->input('resumableTotalChunks');
        $chunkNumber = $request->input('resumableChunkNumber');
        $chunkSize = $request->input('resumableChunkSize');
        $totalSize = $request->input('resumableTotalSize');
        $movieId = $request->input('movie_id');

        // Create upload directory if it doesn't exist
        $uploadDir = 'uploads/resumable/' . $identifier;
        $tempDir = 'temp/resumable/' . $identifier;

        // Check if this is the first chunk
        if ($chunkNumber == 1) {
            // Validate file type and size
            $this->validateFile($request->file('file'), $filename, $totalSize);
        }

        // Store the chunk
        $chunk = $request->file('file');
        $chunkPath = $tempDir . '/chunk_' . str_pad($chunkNumber, 6, '0', STR_PAD_LEFT) . '.part';
        
        Storage::disk('local')->put($chunkPath, file_get_contents($chunk->getPathname()));

        // Log chunk upload progress
        Log::info('Chunk uploaded', [
            'movie_id' => $movieId,
            'chunk_number' => $chunkNumber,
            'total_chunks' => $totalChunks,
            'identifier' => $identifier
        ]);

        // Check if all chunks have been uploaded
        $uploadedChunks = Storage::disk('local')->files($tempDir);
        
        if (count($uploadedChunks) == $totalChunks) {
            Log::info('All chunks uploaded, starting merge', [
                'movie_id' => $movieId,
                'total_chunks' => $totalChunks,
                'filename' => $filename
            ]);

            // Merge chunks into final file
            $finalPath = $uploadDir . '/' . $this->sanitizeFilename($filename);
            $this->mergeChunks($tempDir, $finalPath, $totalChunks, $chunkSize);
            
            // Clean up temp directory
            Storage::disk('local')->deleteDirectory($tempDir);

            Log::info('File merged successfully, starting HLS conversion', [
                'movie_id' => $movieId,
                'file_path' => $finalPath,
                'file_size' => $totalSize
            ]);
            
            // Get movie and trigger HLS conversion
            $movie = Movie::find($movieId);
            if ($movie) {
                // Initialize HLS conversion status
                $movie->initializeHlsConversion();
                
                // Get the full file path for conversion
                $fullFilePath = Storage::disk('local')->path($finalPath);
                
                // Dispatch HLS conversion job
                ConvertVideoToHlsJob::dispatch($fullFilePath, $movie);
            }
            
            // Return success response with file info
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'path' => $finalPath,
                'size' => $totalSize,
                'url' => Storage::url($finalPath),
                'movie_id' => $movieId,
                'hls_conversion_started' => $movie ? true : false
            ]);
        }

        // Return success for this chunk
        return response()->json(['success' => true]);
    }

    /**
     * Check if chunk already exists
     */
    public function checkChunk(Request $request)
    {
        $identifier = $request->input('resumableIdentifier');
        $chunkNumber = $request->input('resumableChunkNumber');
        $chunkSize = $request->input('resumableChunkSize');
        
        $chunkPath = 'temp/resumable/' . $identifier . '/chunk_' . str_pad($chunkNumber, 6, '0', STR_PAD_LEFT) . '.part';
        
        if (Storage::disk('local')->exists($chunkPath)) {
            return response()->json(['success' => true], 200);
        }
        
        return response()->json(['success' => false], 204);
    }

    /**
     * Validate file type and size
     */
    private function validateFile($file, $filename, $totalSize)
    {
        // Check file size (max 2GB)
        $maxSize = 2 * 1024 * 1024 * 1024; // 2GB in bytes
        if ($totalSize > $maxSize) {
            abort(413, 'File size too large. Maximum allowed size is 2GB.');
        }

        // Check file extension
        $allowedExtensions = ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'webm', 'm4v', '3gp'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowedExtensions)) {
            abort(422, 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions));
        }

        // Check MIME type
        $allowedMimeTypes = [
            'video/mp4', 'video/avi', 'video/x-matroska', 'video/quicktime',
            'video/x-ms-wmv', 'video/x-flv', 'video/webm', 'video/x-m4v',
            'video/3gpp', 'video/3gpp2'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            abort(422, 'File MIME type not allowed.');
        }
    }

    /**
     * Merge chunks into final file
     */
    private function mergeChunks($tempDir, $finalPath, $totalChunks, $chunkSize)
    {
        $finalFilePath = Storage::disk('local')->path($finalPath);
        $finalDir = dirname($finalFilePath);
        
        if (!is_dir($finalDir)) {
            mkdir($finalDir, 0755, true);
        }

        $finalFile = fopen($finalFilePath, 'wb');
        
        for ($i = 1; $i <= $totalChunks; $i++) {
            $chunkPath = $tempDir . '/chunk_' . str_pad($i, 6, '0', STR_PAD_LEFT) . '.part';
            $chunkFilePath = Storage::disk('local')->path($chunkPath);
            
            if (file_exists($chunkFilePath)) {
                $chunkData = file_get_contents($chunkFilePath);
                fwrite($finalFile, $chunkData);
            } else {
                fclose($finalFile);
                unlink($finalFilePath);
                abort(500, 'Missing chunk ' . $i . '. Upload failed.');
            }
        }
        
        fclose($finalFile);
    }

    /**
     * Sanitize filename
     */
    private function sanitizeFilename($filename)
    {
        // Remove any directory traversal attempts
        $filename = basename($filename);
        
        // Replace spaces and special characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Ensure filename is not empty
        if (empty($filename)) {
            $filename = 'video_' . time() . '.mp4';
        }
        
        return $filename;
    }

    /**
     * Test endpoint to verify Resumable.js is working
     */
    public function test()
    {
        return response()->json([
            'status' => 'Resumable.js upload endpoint is working',
            'timestamp' => now()->toISOString(),
            'max_file_size' => '2GB',
            'allowed_types' => ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'webm', 'm4v', '3gp']
        ]);
    }
}
