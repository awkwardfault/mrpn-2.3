<?php
session_start();
header('Content-Type: application/json');

include 'db.php';

if (!isset($_SESSION['email'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in"]);
   exit();
}

$userEmail = $_SESSION['email'];

try {
   // Fetch user_id from email
   $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
   $stmt->bindParam(':email', $userEmail, PDO::PARAM_STR);
   $stmt->execute();
   $userId = $stmt->fetchColumn();

   if (!$userId) {
      echo json_encode(["status" => "error", "message" => "User not found"]);
      exit();
   }

   // Get the friendId from the request data
   $data = json_decode(file_get_contents('php://input'), true);
   error_log("Raw input data: " . file_get_contents('php://input'));
   error_log("Decoded data: " . print_r($data, true));

   $friendId = isset($data['user_id']) ? trim($data['user_id']) : '';

   if (empty($friendId)) {
      echo json_encode(["status" => "error", "message" => "Friend ID is required"]);
      exit();
   }

   if ($userId === $friendId) {
      echo json_encode(["status" => "error", "message" => "Cannot add yourself as a friend"]);
      exit();
   }

   // Check if both users exist
   $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = :user_id OR user_id = :friend_id");
   $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
   $stmt->bindParam(':friend_id', $friendId, PDO::PARAM_STR);
   $stmt->execute();
   $users = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

   // Debugging: Log fetched user_ids
   error_log("Fetched user_ids: " . implode(', ', $users));

   // Check if both users exist
   if (count($users) < 2) {
      echo json_encode(["status" => "error", "message" => "One or both users do not exist"]);
      exit();
   }

   // Check if a friend request already exists
   $stmt = $conn->prepare("
        SELECT COUNT(*) FROM friend_requests 
        WHERE (sender_user_id = :user_id AND receiver_user_id = :friend_id) 
           OR (sender_user_id = :friend_id AND receiver_user_id = :user_id)
    ");
   $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
   $stmt->bindParam(':friend_id', $friendId, PDO::PARAM_STR);
   $stmt->execute();
   $count = $stmt->fetchColumn();

   if ($count > 0) {
      echo json_encode(["status" => "error", "message" => "Friend request already exists"]);
      exit();
   }

   // Insert the new friend request
   $stmt = $conn->prepare("INSERT INTO friend_requests (sender_user_id, receiver_user_id) VALUES (:user_id, :friend_id)");
   error_log("SQL: " . $stmt->queryString);
   error_log("Params: user_id = '$userId', friend_id = '$friendId'");
   $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
   $stmt->bindParam(':friend_id', $friendId, PDO::PARAM_STR);
   $stmt->execute();

   echo json_encode(["status" => "success", "message" => "Friend request sent"]);
} catch (PDOException $e) {
   echo json_encode(["status" => "error", "message" => "Query failed: " . $e->getMessage()]);
}

// Debugging: Log the IDs
error_log("User ID: '$userId'");
error_log("Friend ID: '$friendId'");
error_log("email: '$userEmail'");
