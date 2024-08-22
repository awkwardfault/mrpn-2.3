<?php
session_start(); // Start the session

// Check if the user is logged in by verifying if 'email' is set in the session
if (!isset($_SESSION['email'])) {
   echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
   exit();
}

// Include the database connection script
require 'db.php';

// Fetch the user ID and current password hash based on the email stored in the session
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT user_id, password FROM users WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
   echo json_encode(['status' => 'error', 'message' => 'User not found']);
   exit();
}

$user_id = $user['user_id'];
$current_password_hash = $user['password'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Get the form data
   $new_first_name = $_POST['first-name'] ?? null;
   $new_last_name = $_POST['last-name'] ?? null;
   $current_password = $_POST['current-password'];
   $new_password = $_POST['new-password'] ?? null;
   $confirm_new_password = $_POST['confirm-password'] ?? null;

   // Verify the current password
   if (!password_verify($current_password, $current_password_hash)) {
      echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
      exit();
   }

   // Initialize SQL parts
   $sql_parts = [];
   $params = [':user_id' => $user_id];

   // Process the form data
   if (!empty($new_first_name)) {
      $sql_parts[] = "first_name = :first_name";
      $params[':first_name'] = $new_first_name;
   }

   if (!empty($new_last_name)) {
      $sql_parts[] = "last_name = :last_name";
      $params[':last_name'] = $new_last_name;
   }

   if (!empty($new_password)) {
      // Check if the new password and confirm new password match
      if ($new_password !== $confirm_new_password) {
         echo json_encode(['status' => 'error', 'message' => 'New passwords do not match']);
         exit();
      }

      // Hash the new password
      $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
      $sql_parts[] = "password = :password";
      $params[':password'] = $new_password_hash;
   }

   if (!empty($sql_parts)) {
      // Prepare and execute the SQL statement
      $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE user_id = :user_id";
      $stmt = $conn->prepare($sql);

      // Bind the parameters
      foreach ($params as $param => $value) {
         $stmt->bindParam($param, $value);
      }

      // Execute the statement
      if ($stmt->execute()) {
         echo json_encode(['status' => 'success', 'message' => 'User information updated successfully.']);
      } else {
         echo json_encode(['status' => 'error', 'message' => 'Error updating user information.']);
      }
   } else {
      echo json_encode(['status' => 'error', 'message' => 'No updates provided.']);
   }
} else {
   echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}