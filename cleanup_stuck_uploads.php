<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Episode;

echo "=== Cleaning Up Stuck Uploads ===\n\n";

$stuckUploadsDir = storage_path('app/private/uploads/resumable/');
$targetDir = storage_path('app/videos/episodes/');

// Ensure target directory exists
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
    echo "Created target directory: {$targetDir}\n";
}

if (!is_dir($stuckUploadsDir)) {
    echo "No stuck uploads directory found.\n";
    exit(0);
}

$directories = glob($stuckUploadsDir . '*', GLOB_ONLYDIR);

if (empty($directories)) {
    echo "No stuck upload directories found.\n";
    exit(0);
}

echo "Found " . count($directories) . " stuck upload directories.\n\n";

foreach ($directories as $uploadDir) {
    $dirName = basename($uploadDir);
    echo "Processing directory: {$dirName}\n";
    
    // Find the video file in the directory
    $files = glob($uploadDir . '/*');
    $videoFiles = array_filter($files, function($file) {
        return is_file($file) && preg_match('/\.(mp4|avi|mkv|mov|wmv|flv|webm|m4v|3gp)$/i', $file);
    });
    
    if (empty($videoFiles)) {
        echo "  - No video files found, skipping...\n";
        continue;
    }
    
    $videoFile = reset($videoFiles);
    $filename = basename($videoFile);
    $filesize = filesize($videoFile);
    
    echo "  - Found video file: {$filename} (" . number_format($filesize) . " bytes)\n";
    
    // Generate new filename with timestamp
    $newFilename = time() . '_stuck_' . $filename;
    $newPath = $targetDir . $newFilename;
    
    // Move the file
    if (rename($videoFile, $newPath)) {
        echo "  - Moved file to: {$newFilename}\n";
        
        // Clean up the directory
        $remainingFiles = glob($uploadDir . '/*');
        foreach ($remainingFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($uploadDir);
        echo "  - Cleaned up directory\n";
        
        // Log the recovery
        Log::info('Stuck upload recovered', [
            'original_filename' => $filename,
            'new_filename' => $newFilename,
            'file_size' => $filesize,
            'new_path' => 'videos/episodes/' . $newFilename
        ]);
        
    } else {
        echo "  - ERROR: Failed to move file\n";
    }
    
    echo "\n";
}

echo "=== Cleanup Complete ===\n";
echo "Moved files are now available in: {$targetDir}\n";
echo "You can manually assign these files to episodes in the admin panel.\n";
