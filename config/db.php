<?php
// Database Connection
$host = 'localhost';
$db_name = 'OnlineMedicineStore';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>