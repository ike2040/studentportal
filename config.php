<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'student_portal';

try {
    // 1. Connect to MySQL server first (without database selected)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // 2. Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // 3. Select the database
    $pdo->exec("USE `$dbname`");
    
    // 4. Create the students table if it doesn't exist
    $tableQuery = "CREATE TABLE IF NOT EXISTS `students` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `first_name` VARCHAR(100) NOT NULL,
        `middle_name` VARCHAR(100) DEFAULT NULL,
        `last_name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `date_of_birth` DATE NOT NULL,
        `gender` VARCHAR(20) NOT NULL,
        `phone_number` VARCHAR(30) NOT NULL,
        `address` TEXT NOT NULL,
        `state_of_origin` VARCHAR(100) NOT NULL,
        `lga` VARCHAR(100) NOT NULL,
        `next_of_kin` VARCHAR(200) NOT NULL,
        `jamb_score` INT NOT NULL,
        `profile_image` VARCHAR(255) NOT NULL,
        `admission_status` VARCHAR(20) DEFAULT 'Undecided',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($tableQuery);
    
} catch (PDOException $e) {
    die("Database Connection / Initialization Failed: " . htmlspecialchars($e->getMessage()));
}

// 5. Ensure the uploads directory exists for student images
$uploadsDir = __DIR__ . '/uploads';
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

// 5b. Ensure the images directory exists
$imagesDir = __DIR__ . '/images';
if (!file_exists($imagesDir)) {
    mkdir($imagesDir, 0777, true);
}

// 6. Security Helper: Escape output to prevent XSS
if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}
?>
