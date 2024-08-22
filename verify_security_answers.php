<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $user_id = $_SESSION['user_id'];
   $security_answer1 = trim($_POST['security_answer1']);
   $security_answer2 = trim($_POST['security_answer2']);
   $birthdate = trim($_POST['birthdate']);


   $stmt = $conn->prepare("SELECT security_answer1, security_answer2, birthdate FROM users WHERE user_id = :user_id");
   $stmt->bindParam(':user_id', $user_id);
   $stmt->execute();
   $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if ($user && $user['security_answer1'] === $security_answer1 && $user['security_answer2'] === $security_answer2) {
      header('Location: reset_password.html');
      exit;
   } else {
      echo "Incorrect answers.";
   }
}
