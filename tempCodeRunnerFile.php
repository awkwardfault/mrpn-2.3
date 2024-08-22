<?php
session_start();
header('Content-Type: application/json');

include 'db.php';

if (!isset($_SESSION['user_id'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in"]);
   exit();
}

$userId = $_SESSION['user_id'];

try {
   $stmt = $conn->prepare("
       SELECT users.id, users.email
       FROM users
       JOIN friend_requests ON users.id = friend_requests.sender_id
       WHERE friend_requests.receiver_id = :user_id
   ");
   $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
   $stmt->execute();
   $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
   echo json_encode($requests);
} catch (PDOException $e) {
   echo json_encode(["status" => "error", "message" => "Query failed: " . $e->getMessage()]);
}