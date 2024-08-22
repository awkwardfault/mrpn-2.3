<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in"]);
   exit;
}

include 'db.php';

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

   // Read and decode the input
   $input = file_get_contents('php://input');
   file_put_contents('log.txt', "Raw input: " . $input . "\n", FILE_APPEND);
   $data = json_decode($input, true);
   file_put_contents('log.txt', "Decoded data: " . print_r($data, true) . "\n", FILE_APPEND);

   // Check if the 'message' key exists and validate it
   if (isset($data['message']) && !empty($data['message']) && isset($data['receiver_id'])) {
      $message = $data['message'];
      $receiverId = $data['receiver_id'];

      // Check if the receiver is a friend of the sender
      $stmt = $conn->prepare("
            SELECT 1 FROM friends 
            WHERE (user_user_id = :sender_id AND friend_user_id = :receiver_id) 
               OR (user_user_id = :receiver_id AND friend_user_id = :sender_id)
        ");
      $stmt->bindParam(':sender_id', $userId, PDO::PARAM_STR);
      $stmt->bindParam(':receiver_id', $receiverId, PDO::PARAM_STR);
      $stmt->execute();

      if ($stmt->rowCount() > 0) {
         // Receiver is a friend, save the message to the database
         $stmt = $conn->prepare("INSERT INTO messages (sender_user_id, receiver_user_id, text) VALUES (:sender_id, :receiver_id, :text)");
         $stmt->bindParam(':sender_id', $userId, PDO::PARAM_STR);
         $stmt->bindParam(':receiver_id', $receiverId, PDO::PARAM_STR);
         $stmt->bindParam(':text', $message, PDO::PARAM_STR);

         if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
         } else {
            echo json_encode(["status" => "error", "message" => "Failed to send message"]);
         }
      } else {
         echo json_encode(["status" => "error", "message" => "You can only send messages to friends"]);
      }
   } else {
      // Log the error for debugging
      file_put_contents('log.txt', "Error: 'message' key not found or empty in input\n", FILE_APPEND);
      echo json_encode(["status" => "error", "message" => "'message' key not found or empty in input"]);
   }
} catch (PDOException $e) {
   echo json_encode(["status" => "error", "message" => "Query failed: " . $e->getMessage()]);
}

// Debugging: Log email and user ID
file_put_contents('log.txt', "email: " . print_r($email, true) . "\n", FILE_APPEND);
file_put_contents('log.txt', "userId: " . print_r($userId, true) . "\n", FILE_APPEND);
