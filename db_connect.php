<?php
// db_connect.php

$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password is blank
$dbname = "nakawa_ascm_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Uncomment the line below to test if connection works, then comment it back
// echo "Connected successfully to Nakawa ASCM Database!";
?>