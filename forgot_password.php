<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   $email = trim($_POST['email']);
   $user_id = trim($_POST['user_id']);

   try {
      // Verify the user and fetch security questions and birthdate
      $stmt = $conn->prepare("SELECT security_question1, security_question2, birthdate FROM users WHERE email = :email AND user_id = :user_id");
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':user_id', $user_id);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user) {
         session_start();
         $_SESSION['user_id'] = $user_id;

         // Map question IDs to their text
         $questions = [
            1 => "What was the name of your first pet?",
            2 => "What is your favorite movie?",
            3 => "What was the name of your elementary school?",
            4 => "What is the name of your favorite teacher or professor?",
            5 => "What was the model of your first phone?"
         ];

         $_SESSION['security_question1'] = $questions[$user['security_question1']];
         $_SESSION['security_question2'] = $questions[$user['security_question2']];
         $_SESSION['birthdate'] = $user['birthdate'];

         header('Location: verify_security_questions.php');
         exit;
      } else {
         echo "User not found.";
      }
   } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
   }
}