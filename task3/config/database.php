<?php
// ============================================================
// config/database.php
// Database connection — connects PHP to MySQL via XAMPP.
// Every model file uses this to talk to the database.
// ============================================================

$host    = 'localhost';     // XAMPP MySQL is always localhost
$dbname  = 'quiz_platform'; // Your database name in phpMyAdmin
$user    = 'root';          // XAMPP default username
$pass    = '';              // XAMPP default password = empty string
$charset = 'utf8mb4';       // Supports all characters + emojis

// ─────────────────────────────────────────────────────────────
// getDB() — Call this anywhere to get a PDO database connection
// Example: $db = getDB();
// ─────────────────────────────────────────────────────────────
function getDB()
{
    global $host, $dbname, $user, $pass, $charset;

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
