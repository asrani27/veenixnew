<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;

echo "🧪 Testing File Cleanup Feature\n";
echo "================================\n\n";

// Test the cleanup method directly
echo "1. Testing cleanupOriginalUploadFile method...\n";

// Create a test job instance
$job = new \App\Jobs\ConvertVideoToHlsJob(
    'uploads/sample-30s.mp4',  // This should match the existing file
    'hls_videos',
    'test-video',
    '123'
);

$reflection = new ReflectionClass($job);
$inputPathProperty = $reflection->getProperty('inputPath');
$inputPathProperty->setAccessible(true);
echo "✅ Job created with inputPath: " . $inputPathProperty->getValue($job) . "\n";

// Check if the file exists before cleanup
$testFile = storage_path('app/uploads/sample-30s.mp4');
if (file_exists($testFile)) {
    echo "✅ Test file exists: " . basename($testFile) . " (" . number_format(filesize($testFile) / 1024 / 1024, 2) . " MB)\n";
} else {
    echo "❌ Test file not found: {$testFile}\n";
}

// Call the cleanup method using reflection to test it
$reflection = new ReflectionClass($job);
$method = $reflection->getMethod('cleanupOriginalUploadFile');
$method->setAccessible(true);

echo "\n2. Calling cleanupOriginalUploadFile method...\n";
$method->invoke($job);

// Check if the file was deleted
echo "\n3. Checking if file was deleted...\n";
if (file_exists($testFile)) {
    echo "❌ File still exists after cleanup\n";
} else {
    echo "✅ File successfully deleted after cleanup\n";
}

echo "\n🎉 Test completed!\n";
