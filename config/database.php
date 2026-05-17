<?php


$host    = 'localhost';     
$dbname  = 'quiz_platform';  
$user    = 'root';          
$pass    = '';              
$charset = 'utf8mb4';       


function getDB() {
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