<?php
// On PHP 8.1+, mysqli throws exceptions on errors by default instead of
// returning false. This project's error checks (if (!mysqli_query(...)))
// rely on the classic false-return behavior, so we switch it back here.
mysqli_report(MYSQLI_REPORT_OFF);

$host = "localhost";
$user = "root";
$pass = "";
$database = "car_booking";

$connect = mysqli_connect($host, $user, $pass);

if (!$connect) {
    die("Connection Failed: " . mysqli_connect_error());
}

if (!mysqli_select_db($connect, $database)) {
    if (!mysqli_query($connect, "CREATE DATABASE IF NOT EXISTS `$database`")) {
        die("Database '$database' could not be created. Please check your MySQL server.");
    }
    if (!mysqli_select_db($connect, $database)) {
        die("Database '$database' could not be selected.");
    }
}

mysqli_set_charset($connect, "utf8mb4");

$schemas = array(
    "admin" => "CREATE TABLE IF NOT EXISTS `admin` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    "users" => "CREATE TABLE IF NOT EXISTS `users` (
        `user_id` INT(11) NOT NULL AUTO_INCREMENT,
        `fullname` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `username` VARCHAR(50) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `is_verified` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`user_id`),
        UNIQUE KEY `email` (`email`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    "cars" => "CREATE TABLE IF NOT EXISTS `cars` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `price` DECIMAL(10,2) NOT NULL,
        `image` VARCHAR(255) NOT NULL,
        `status` VARCHAR(50) DEFAULT 'Available',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    "bookings" => "CREATE TABLE IF NOT EXISTS `bookings` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) DEFAULT NULL,
        `car_id` INT(11) DEFAULT NULL,
        `fullname` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(30) DEFAULT NULL,
        `address` VARCHAR(255) DEFAULT NULL,
        `city` VARCHAR(100) DEFAULT NULL,
        `pickup_datetime` DATETIME NOT NULL,
        `return_datetime` DATETIME NOT NULL,
        `car_type` VARCHAR(50) NOT NULL,
        `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `order_id` VARCHAR(50) DEFAULT NULL,
        `payment_status` VARCHAR(20) NOT NULL DEFAULT 'Unpaid',
        `payment_id` VARCHAR(100) DEFAULT NULL,
        `status` VARCHAR(20) NOT NULL DEFAULT 'Pending',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `order_id` (`order_id`),
        KEY `user_id` (`user_id`),
        KEY `car_id` (`car_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    "contact" => "CREATE TABLE IF NOT EXISTS `contact` (
        `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `subject` VARCHAR(150) NOT NULL,
        `message` TEXT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

    "otps" => "CREATE TABLE IF NOT EXISTS `otps` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `email` VARCHAR(100) NOT NULL,
        `otp_code` VARCHAR(10) NOT NULL,
        `purpose` VARCHAR(20) NOT NULL,
        `attempts` INT(11) NOT NULL DEFAULT 0,
        `used` TINYINT(1) NOT NULL DEFAULT 0,
        `expires_at` DATETIME NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `email_purpose` (`email`, `purpose`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
);

foreach ($schemas as $sql) {
    if (!mysqli_query($connect, $sql)) {
        die("Database schema could not be initialized: " . mysqli_error($connect));
    }
}

// Lightweight auto-migration for anyone importing an older copy of this
// database that already has these tables but is missing the newer columns.
$migrations = array(
    "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `is_verified` TINYINT(1) NOT NULL DEFAULT 0",
    "ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `phone` VARCHAR(30) DEFAULT NULL",
    "ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `address` VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `city` VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00",
    "ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `order_id` VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `payment_status` VARCHAR(20) NOT NULL DEFAULT 'Unpaid'",
    "ALTER TABLE `bookings` ADD COLUMN IF NOT EXISTS `payment_id` VARCHAR(100) DEFAULT NULL",
);
foreach ($migrations as $sql) {
    // Silently ignore errors here (e.g. on older MySQL/MariaDB versions
    // without "ADD COLUMN IF NOT EXISTS" support) - a fresh install
    // never needs these anyway since CREATE TABLE above already has them.
    @mysqli_query($connect, $sql);
}

$carCheck = mysqli_query($connect, "SELECT COUNT(*) AS total FROM cars");
if ($carCheck) {
    $carRow = mysqli_fetch_assoc($carCheck);
    if ($carRow && (int) $carRow['total'] === 0) {
        mysqli_query($connect, "INSERT INTO cars (name, price, image, status) VALUES
            ('Hyundai i20', 10000.00, 'Hyundai i20 Car.jpg', 'Available'),
            ('Maruti Swift', 15000.00, 'Maruti Swift Car.jpg', 'Available'),
            ('Toyota Innova', 20000.00, 'Toyota Innova Car.jpg', 'Available'),
            ('Honda City', 25000.00, 'Honda City Car.jpg', 'Available')");
    }
}

// Seed the default admin account (username: Farhan / password: Farhan1234)
// if no admin account exists yet. Change this password after first login
// if this project will be publicly accessible.
$adminCheck = mysqli_query($connect, "SELECT COUNT(*) AS total FROM admin");
if ($adminCheck) {
    $adminRow = mysqli_fetch_assoc($adminCheck);
    if ($adminRow && (int) $adminRow['total'] === 0) {
        $defaultHash = password_hash('Farhan1234', PASSWORD_DEFAULT);
        mysqli_query($connect, "INSERT INTO admin (username, password) VALUES ('Farhan', '$defaultHash')");
    }
}
?>
