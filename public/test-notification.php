<?php
// This is a test script to add a notification to the database
require __DIR__ . '/../vendor/autoload.php';

use CodeIgniter\Database\Config;

// Path to the front controller
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);

// Load environment settings from .env file
$env = parse_ini_file(__DIR__ . '/../.env');

// Set the environment
$environment = $env['CI_ENVIRONMENT'] ?? 'production';
define('ENVIRONMENT', $environment);

// Load the database configuration
$dbConfig = new \Config\Database();

// Get the default database connection
$db = \Config\Database::connect($dbConfig->default);

// Get the first user ID from the database
$userId = $db->table('users')->select('id')->orderBy('id', 'ASC')->get(1)->getRow();

if ($userId) {
    $userId = $userId->id;
    
    // Insert a test notification
    $data = [
        'user_id' => $userId,
        'message' => 'This is a test notification - ' . date('Y-m-d H:i:s'),
        'is_read' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $db->table('notifications')->insert($data);
    $insertId = $db->insertID();
    
    if ($insertId) {
        echo "Test notification added successfully for user ID: " . $userId . "\n";
        echo "Notification ID: " . $insertId . "\n";
    } else {
        echo "Failed to insert notification. Error: " . json_encode($db->error()) . "\n";
    }
} else {
    echo "No users found in the database. Please create a user first.\n";
}

echo "\n";
?>
