<?php
/**
 * Generate APP_KEY for Laravel
 * Access: https://gzlpro.com/rms/public/generate-key.php
 * 
 * This will generate a new APP_KEY and update your .env file
 * DELETE THIS FILE after use for security!
 */

// Security: Only allow if .env doesn't have APP_KEY set
$envPath = dirname(__DIR__) . '/.env';

if (!file_exists($envPath)) {
    die('❌ .env file not found! Please create it first.');
}

// Read current .env
$envContent = file_get_contents($envPath);

// Check if APP_KEY already exists
if (strpos($envContent, 'APP_KEY=') !== false && strpos($envContent, 'APP_KEY=base64:') !== false && strpos($envContent, 'APP_KEY=base64:') === strpos($envContent, 'APP_KEY=')) {
    // Check if it's not empty
    preg_match('/APP_KEY=base64:([^\s]+)/', $envContent, $matches);
    if (!empty($matches[1]) && strlen($matches[1]) > 20) {
        die('✅ APP_KEY already exists in .env file. Delete this file for security!');
    }
}

// Generate new APP_KEY
$key = 'base64:' . base64_encode(random_bytes(32));

// Update .env file
if (strpos($envContent, 'APP_KEY=') !== false) {
    // Replace existing APP_KEY
    $envContent = preg_replace('/APP_KEY=.*/', 'APP_KEY=' . $key, $envContent);
} else {
    // Add APP_KEY if it doesn't exist
    $envContent .= "\nAPP_KEY=" . $key . "\n";
}

// Write back to .env
if (file_put_contents($envPath, $envContent)) {
    echo "<h1>✅ APP_KEY Generated Successfully!</h1>";
    echo "<p><strong>Generated Key:</strong> <code>" . htmlspecialchars($key) . "</code></p>";
    echo "<p style='color: green;'>✅ .env file has been updated.</p>";
    echo "<hr>";
    echo "<p style='color: red; font-weight: bold;'>⚠️ IMPORTANT: Delete this file (generate-key.php) immediately for security!</p>";
    echo "<p><a href='index.php'>Go to Laravel App →</a></p>";
} else {
    echo "<h1>❌ Error</h1>";
    echo "<p>Could not write to .env file. Check file permissions.</p>";
    echo "<p><strong>Generated Key (copy this manually to .env):</strong></p>";
    echo "<code style='background: #f0f0f0; padding: 10px; display: block;'>APP_KEY=" . htmlspecialchars($key) . "</code>";
}
