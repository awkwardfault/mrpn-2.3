<?php
session_start();
header('Content-Type: application/json');

include 'db.php';

// Debugging: Check current session data
// error_log("Session data: " . print_r($_SESSION, true));

// Check if the email session variable is set
if (!isset($_SESSION['email'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in"]);
   exit();
}

$email = $_SESSION['email'];

try {
   // Prepare SQL query to find user ID based on email
   $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
   $stmt->bindParam(':email', $email, PDO::PARAM_STR);
   $stmt->execute();
   $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if (!$user) {
      echo json_encode(["status" => "error", "message" => "User not found"]);
      exit();
   }

   $userId = $user['user_id'];

   // Prepare SQL query to fetch friend requests
   $stmt = $conn->prepare("
        SELECT users.user_id, users.email
        FROM users
        JOIN friend_requests ON users.user_id = friend_requests.sender_user_id
        WHERE friend_requests.receiver_user_id = :user_id
    ");
   $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
   $stmt->execute();
   $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Debugging: Log the fetched requests
   // error_log("Fetched requests data: " . print_r($requests, true));

   echo json_encode($requests);
} catch (PDOException $e) {
   error_log("Query failed: " . $e->getMessage());
   echo json_encode(["status" => "error", "message" => "Query failed: " . $e->getMessage()]);
}

// Debugging: Log the email
error_log("email: " . print_r($email, true));
