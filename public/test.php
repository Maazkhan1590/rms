<?php
/**
 * Quick Test File - Delete after verifying Laravel works
 * Access: https://gzlpro.com/rms/public/test.php
 */

echo "<h1>Laravel RMS - Server Test</h1>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Path:</strong> " . __FILE__ . "</p>";

// Test Laravel Bootstrap
$laravelPath = dirname(__DIR__) . '/bootstrap/app.php';
if (file_exists($laravelPath)) {
    echo "<p style='color: green;'>✅ Laravel bootstrap file found</p>";
} else {
    echo "<p style='color: red;'>❌ Laravel bootstrap file NOT found at: $laravelPath</p>";
}

// Test vendor autoload
$vendorPath = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($vendorPath)) {
    echo "<p style='color: green;'>✅ Composer vendor autoload found</p>";
} else {
    echo "<p style='color: red;'>❌ Composer vendor autoload NOT found. Run: composer install</p>";
}

// Test .env file
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    echo "<p style='color: green;'>✅ .env file found</p>";
} else {
    echo "<p style='color: orange;'>⚠️ .env file NOT found. Create it from .env.example</p>";
}

// Test storage permissions
$storagePath = dirname(__DIR__) . '/storage';
if (is_writable($storagePath)) {
    echo "<p style='color: green;'>✅ Storage folder is writable</p>";
} else {
    echo "<p style='color: red;'>❌ Storage folder is NOT writable. Set permissions: chmod -R 755 storage</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Try Laravel App →</a></p>";
echo "<p><small>Delete this file after testing</small></p>";
