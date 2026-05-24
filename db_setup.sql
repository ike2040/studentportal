-- Create Database if not exists
CREATE DATABASE IF NOT EXISTS `student_portal` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `student_portal`;

-- Create Students Table
CREATE TABLE IF NOT EXISTS `students` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
