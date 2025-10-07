<?php

echo "Testing CSRF Exemption\n";
echo "======================\n\n";

// Test 1: Simple POST to TUS endpoint
echo "1. Testing simple POST to TUS endpoint...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/movies/tus-upload');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Upload-Length: 100',
    'Upload-Metadata: filename dGVzdC5tcDQ=,movie_id MQ==',
    'Content-Type: application/offset+octet-stream'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 419) {
    echo "✓ No 419 error (HTTP $httpCode)\n";
    if ($httpCode === 201) {
        echo "✓ Upload creation successful\n";
    } else {
        echo "ℹ️  Got HTTP $httpCode (may need authentication)\n";
    }
} else {
    echo "✗ Still getting 419 Page Expired error\n";
    echo "Response: " . substr($response, 0, 200) . "...\n";
}

// Test 2: Test with a different route pattern
echo "\n2. Testing exact route match...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/admin/movies/tus-upload');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 419) {
    echo "✓ No 419 error (HTTP $httpCode)\n";
} else {
    echo "✗ Still getting 419 Page Expired error\n";
}

// Test 3: Check if VerifyCsrfToken middleware is being used
echo "\n3. Testing a regular form route (should get 419)...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'email=test&password=test');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 419) {
    echo "✓ Regular routes still protected by CSRF (HTTP $httpCode)\n";
} else {
    echo "ℹ️  Regular routes not protected (HTTP $httpCode)\n";
}

echo "\nCSRF Exemption Test Complete!\n";
echo "=============================\n";
