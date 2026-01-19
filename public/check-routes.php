<?php
/**
 * Check Route Configuration
 * Access: https://gzlpro.com/rms-dev/public/check-routes.php
 * 
 * This will show route information and help diagnose routing issues
 * DELETE THIS FILE after use for security!
 */

echo "<h1>Laravel Route Configuration Check</h1>";

// Check if Laravel is accessible
$bootstrapPath = dirname(__DIR__) . '/bootstrap/app.php';
if (!file_exists($bootstrapPath)) {
    die('<p style="color: red;">❌ Laravel bootstrap file not found!</p>');
}

// Load Laravel
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require_once $bootstrapPath;
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Get request info
$request = Illuminate\Http\Request::capture();
$basePath = $request->getBasePath();
$pathInfo = $request->getPathInfo();
$requestUri = $request->getRequestUri();

echo "<h2>Request Information</h2>";
echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr><th>Property</th><th>Value</th></tr>";
echo "<tr><td><strong>Base Path</strong></td><td>" . htmlspecialchars($basePath) . "</td></tr>";
echo "<tr><td><strong>Path Info</strong></td><td>" . htmlspecialchars($pathInfo) . "</td></tr>";
echo "<tr><td><strong>Request URI</strong></td><td>" . htmlspecialchars($requestUri) . "</td></tr>";
echo "<tr><td><strong>Script Name</strong></td><td>" . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "</td></tr>";
echo "<tr><td><strong>Document Root</strong></td><td>" . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</td></tr>";
echo "</table>";

echo "<h2>Environment Configuration</h2>";
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    preg_match('/APP_URL=(.+)/', $envContent, $appUrlMatch);
    preg_match('/ASSET_URL=(.+)/', $envContent, $assetUrlMatch);
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Setting</th><th>Value</th></tr>";
    echo "<tr><td><strong>APP_URL</strong></td><td>" . htmlspecialchars($appUrlMatch[1] ?? 'Not set') . "</td></tr>";
    echo "<tr><td><strong>ASSET_URL</strong></td><td>" . htmlspecialchars($assetUrlMatch[1] ?? 'Not set') . "</td></tr>";
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ .env file not found!</p>";
}

echo "<h2>Route Test</h2>";
echo "<p>Try these URLs:</p>";
echo "<ul>";
echo "<li><a href='index.php'>Home (index.php)</a></li>";
echo "<li><a href='index.php/publications'>Publications List</a></li>";
echo "<li><a href='index.php/publications/1'>Publication #1</a></li>";
echo "</ul>";

echo "<h2>Diagnosis</h2>";
if ($basePath === '' || $basePath === '/') {
    echo "<p style='color: green;'>✅ Base path is root - domain is pointing directly to public folder (GOOD)</p>";
    echo "<p>Your routes should work at: <code>https://gzlpro.com/publications/1</code></p>";
} else {
    echo "<p style='color: orange;'>⚠️ Base path detected: <code>" . htmlspecialchars($basePath) . "</code></p>";
    echo "<p>Your routes should work at: <code>https://gzlpro.com" . htmlspecialchars($basePath) . "/publications/1</code></p>";
}

echo "<hr>";
echo "<p style='color: red; font-weight: bold;'>⚠️ IMPORTANT: Delete this file (check-routes.php) after use for security!</p>";
