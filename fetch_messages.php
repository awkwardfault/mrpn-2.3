<?php
session_start();
header('Content-Type: application/json');

include 'db.php';

// Ensure the email session variable and receiver_id parameter are provided
if (!isset($_SESSION['email']) || !isset($_GET['receiver_id'])) {
   echo json_encode(["status" => "error", "message" => "Missing parameters"]);
   exit();
}

$email = $_SESSION['email'];
$receiverId = $_GET['receiver_id'];

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

   $senderId = $user['user_id'];

   // Prepare and execute SQL query to fetch messages
   $stmt = $conn->prepare("
        SELECT messages.id, users.email, messages.text, messages.created_at
        FROM messages
        JOIN users ON messages.sender_user_id = users.user_id
        WHERE (messages.sender_user_id = :sender_id AND messages.receiver_user_id = :receiver_id)
        OR (messages.sender_user_id = :receiver_id AND messages.receiver_user_id = :sender_id)
        ORDER BY messages.created_at ASC
    ");

   $stmt->bindParam(':sender_id', $senderId, PDO::PARAM_STR);
   $stmt->bindParam(':receiver_id', $receiverId, PDO::PARAM_STR);

   $stmt->execute();
   $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

   echo json_encode($messages);
} catch (PDOException $e) {
   echo json_encode(["status" => "error", "message" => "Query failed: " . $e->getMessage()]);
}

// Debugging: Log email and sender ID
error_log("email: " . print_r($email, true));
error_log("senderId: " . print_r($senderId, true));
error_log("rcvrId: " . print_r($receiverId, true));
