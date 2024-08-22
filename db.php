<?php
$servername = "localhost";
$username = "root";
$password = "password"; // your MySQL password
// $dbname = "chat_app";
$dbname = "chat_app2";


try {
   $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
   $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
   echo "Connection failed: " . $e->getMessage();
}