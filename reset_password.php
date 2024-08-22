<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $user_id = $_SESSION['user_id'];
   $new_password = trim($_POST['new_password']);
   $confirm_password = trim($_POST['confirm_password']);

   if ($new_password === $confirm_password) {
      $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

      $stmt = $conn->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
      $stmt->bindParam(':password', $hashed_password);
      $stmt->bindParam(':user_id', $user_id);

      if ($stmt->execute()) {
         echo "Password reset successful!";
         session_destroy();
         header('Location: login.html');
      } else {
         echo "Error: Could not reset password.";
      }
   } else {
      echo "Passwords do not match.";
   }
}
