<?php
session_start();
header('Content-Type: application/json');

include 'db.php'; // Ensure this sets up $conn properly

if (!isset($_SESSION['user_id'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in"]);
   exit();
}

$userId = $_SESSION['user_id'];
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (empty($query)) {
   echo json_encode([]);
   exit();
}

try {
   $stmt = $conn->prepare("
        SELECT users.user_id, users.email, 
        CASE 
            WHEN friends.friend_user_id IS NOT NULL THEN 1 
            ELSE 0 
        END AS is_friend
        FROM users
        LEFT JOIN friends ON users.user_id = friends.friend_user_id AND friends.user_user_id = :user_id
        WHERE users.email LIKE :query AND users.user_id != :user_id
        LIMIT 10
    ");
   $likeQuery = "%$query%";
   $stmt->bindParam(':query', $likeQuery, PDO::PARAM_STR);
   $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
   $stmt->execute();
   $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Ensure we return an array
   if (!$users) {
      $users = [];
   }

   echo json_encode($users);
} catch (PDOException $e) {
   echo json_encode(["status" => "error", "message" => "Query failed: " . $e->getMessage()]);
}
