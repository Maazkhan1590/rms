<?php
/**
 * Fix Asset URLs for Subdirectory Deployment
 * Access: https://gzlpro.com/rms/public/fix-assets.php
 * 
 * This will update your .env file with correct ASSET_URL
 * DELETE THIS FILE after use for security!
 */

$envPath = dirname(__DIR__) . '/.env';

if (!file_exists($envPath)) {
    die('❌ .env file not found! Please create it first.');
}

// Get current URL to determine subdirectory
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName);

// Calculate the public path
// If script is at /rms/public/fix-assets.php, basePath is /rms/public
$assetUrl = rtrim($basePath, '/');

// Read current .env
$envContent = file_get_contents($envPath);

// Update or add ASSET_URL
if (strpos($envContent, 'ASSET_URL=') !== false) {
    $envContent = preg_replace('/ASSET_URL=.*/', 'ASSET_URL=' . $assetUrl, $envContent);
} else {
    // Add ASSET_URL after APP_URL
    if (strpos($envContent, 'APP_URL=') !== false) {
        $envContent = preg_replace('/(APP_URL=.*)/', '$1' . "\nASSET_URL=" . $assetUrl, $envContent);
    } else {
        $envContent .= "\nASSET_URL=" . $assetUrl . "\n";
    }
}

// Write back to .env
if (file_put_contents($envPath, $envContent)) {
    echo "<h1>✅ ASSET_URL Updated Successfully!</h1>";
    echo "<p><strong>Asset URL set to:</strong> <code>" . htmlspecialchars($assetUrl) . "</code></p>";
    echo "<p style='color: green;'>✅ .env file has been updated.</p>";
    echo "<hr>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Clear Laravel config cache (if you have SSH access): <code>php artisan config:clear</code></li>";
    echo "<li>Or manually delete: <code>bootstrap/cache/config.php</code> via File Manager</li>";
    echo "<li>Refresh your website</li>";
    echo "</ol>";
    echo "<hr>";
    echo "<p style='color: red; font-weight: bold;'>⚠️ IMPORTANT: Delete this file (fix-assets.php) immediately for security!</p>";
    echo "<p><a href='index.php'>Go to Laravel App →</a></p>";
} else {
    echo "<h1>❌ Error</h1>";
    echo "<p>Could not write to .env file. Check file permissions.</p>";
    echo "<p><strong>Add this to your .env file manually:</strong></p>";
    echo "<code style='background: #f0f0f0; padding: 10px; display: block;'>ASSET_URL=" . htmlspecialchars($assetUrl) . "</code>";
}
