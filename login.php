<?php
session_start();
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Database connection
   $servername = "localhost";
   $username = "root"; // your DB username
   $password = "password"; // your DB password
   $dbname = "chat_app2";

   // Create a new PDO instance
   try {
      $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   } catch (PDOException $e) {
      echo json_encode(["status" => "error", "message" => "Database connection failed: " . $e->getMessage()]);
      exit();
   }

   // Get POST data
   $email = $_POST['email'];
   $password = $_POST['password'];

   // Validate input
   if (empty($email) || empty($password)) {
      echo json_encode(["status" => "error", "message" => "Email and password are required"]);
      exit();
   }

   // Prepare and execute SQL query
   $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE email = :email");
   $stmt->bindParam(':email', $email);
   $stmt->execute();
   $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($user && password_verify($password, $user['password'])) {
      // User found and password matches
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['email'] = $user['email'];
      echo json_encode(["status" => "success"]);
      header('Location: index.php');
   } else {
      // Invalid credentials
      echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
   }

   $conn = null;
} else {
   echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
