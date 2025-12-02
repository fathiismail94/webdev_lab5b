<?php
// Database credentials (Adjust these if your MySQL setup is different)
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Default XAMPP/WAMP username
define('DB_PASSWORD', '');     // Default XAMPP/WAMP password
define('DB_NAME', 'Lab_5b');   // Database name as created in Q1

// Attempt to connect to MySQL database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session (Question 8)
session_start();
?>