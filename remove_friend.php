<?php
include 'db.php';
session_start();

// Check if the user is logged in by verifying if 'email' is set in the session
if (!isset($_SESSION['email'])) {
   echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
   exit();
}

// Fetch the user ID based on the email stored in the session
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
   echo json_encode(['status' => 'error', 'message' => 'User not found']);
   exit();
}

$user_id = $user['user_id'];
$friend_user_id = $_POST['friend_user_id'];

// Remove the friend from the `friends` table
$stmt = $conn->prepare("DELETE FROM friends WHERE (user_user_id = :user_id AND friend_user_id = :friend_user_id) OR (user_user_id = :friend_user_id AND friend_user_id = :user_id)");
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':friend_user_id', $friend_user_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
   echo json_encode(['status' => 'success', 'message' => 'Friend removed']);
} else {
   echo json_encode(['status' => 'error', 'message' => 'Failed to remove friend']);
}