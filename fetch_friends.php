<?php
session_start();
header('Content-Type: application/json');

include 'db.php';

// Check if the email session variable is set
if (!isset($_SESSION['email'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in"]);
   exit();
}

$email = $_SESSION['email'];

try {
   // Retrieve user ID based on the email
   $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
   $stmt->bindParam(':email', $email, PDO::PARAM_STR);
   $stmt->execute();
   $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if (!$user) {
      echo json_encode(["status" => "error", "message" => "User not found"]);
      exit();
   }

   $userId = $user['user_id'];

   // Fetch friends based on user ID
   $stmt = $conn->prepare("
        SELECT users.user_id, users.email, users.first_name, users.last_name
        FROM users
        JOIN friends ON users.user_id = friends.friend_user_id
        WHERE friends.user_user_id = :user_id
    ");
   $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
   $stmt->execute();
   $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

   echo json_encode($friends);
} catch (PDOException $e) {
   echo json_encode(["status" => "error", "message" => "Query failed: " . $e->getMessage()]);
}

// Debugging: Log email and user ID
error_log("email: " . print_r($email, true));
error_log("userId: " . print_r($userId, true));
