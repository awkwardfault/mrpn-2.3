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

// Retrieve user ID based on the email
try {
   $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
   $stmt->bindParam(':email', $email, PDO::PARAM_STR);
   $stmt->execute();
   $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if (!$user) {
      echo json_encode(["status" => "error", "message" => "User not found"]);
      exit();
   }

   $userId = $user['user_id'];

   // Decode the JSON input
   $data = json_decode(file_get_contents('php://input'), true);
   $friendId = isset($data['user_id']) ? trim($data['user_id']) : '';
   $action = isset($data['action']) ? trim($data['action']) : '';

   if (empty($friendId) || !in_array($action, ['accept', 'reject'])) {
      echo json_encode(["status" => "error", "message" => "Invalid parameters"]);
      exit();
   }

   if ($action === 'accept') {
      // Add to friends table
      $stmt = $conn->prepare("
            INSERT INTO friends (user_user_id, friend_user_id) VALUES (:user_id, :friend_id), (:friend_id, :user_id)
        ");
      $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
      $stmt->bindParam(':friend_id', $friendId, PDO::PARAM_STR);
      $stmt->execute();

      // Remove from requests table
      $stmt = $conn->prepare("
            DELETE FROM friend_requests WHERE sender_user_id = :sender_id AND receiver_user_id = :receiver_id
        ");
      $stmt->bindParam(':sender_id', $friendId, PDO::PARAM_STR);
      $stmt->bindParam(':receiver_id', $userId, PDO::PARAM_STR);
      $stmt->execute();
   } else if ($action === 'reject') {
      // Just remove from requests table
      $stmt = $conn->prepare("
            DELETE FROM friend_requests WHERE sender_user_id = :sender_id AND receiver_user_id = :receiver_id
        ");
      $stmt->bindParam(':sender_id', $friendId, PDO::PARAM_STR);
      $stmt->bindParam(':receiver_id', $userId, PDO::PARAM_STR);
      $stmt->execute();
   }

   echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
   echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
}
error_log("data: " . print_r($data, true));
