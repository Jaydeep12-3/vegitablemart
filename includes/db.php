<?php
// Database Configuration
// Use Environment Variables for live server (Render), fallback to local settings for WAMP
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306'; // Aiven and other cloud databases often use custom ports
$db_name = getenv('DB_NAME') ?: 'vegi_store';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'root'; 

try {
    // Added port to connection string
    $conn = new PDO("mysql:host=$host;port=$port", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // In production (Render), we usually connect directly to an existing database, 
    // so we only try to create the DB if we are on localhost.
    if ($host === 'localhost') {
        $conn->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    }
    $conn->exec("USE `$db_name`");
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
