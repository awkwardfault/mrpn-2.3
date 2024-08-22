<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
   echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
   exit();
}

$userEmail = trim($_SESSION['email']);

// Include the database connection file
require_once 'db.php'; // Ensure this path is correct

try {
   // Prepare and execute query
   $stmt = $conn->prepare('SELECT user_id, first_name, last_name, email, birthdate FROM users WHERE email = :email');
   $stmt->bindParam(':email', $userEmail, PDO::PARAM_STR);
   $stmt->execute();

   $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($user) {
      echo json_encode(['status' => 'success', 'data' => $user]);
   } else {
      echo json_encode(['status' => 'error', 'message' => 'User not found']);
   }

} catch (PDOException $e) {
   echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
