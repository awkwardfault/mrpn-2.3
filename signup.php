<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   // Extract form data
   $first_name = trim($_POST['first_name']);
   $last_name = trim($_POST['last_name']);
   $user_id = trim($_POST['user_id']);
   $email = trim($_POST['email']);
   $password = trim($_POST['password']);
   $birthdate = trim($_POST['birthdate']);
   $security_question1 = trim($_POST['security_question1']);
   $security_answer1 = trim($_POST['security_answer1']);
   $security_question2 = trim($_POST['security_question2']);
   $security_answer2 = trim($_POST['security_answer2']);

   // Validate and sanitize input
   if (empty($first_name) || empty($last_name) || empty($user_id) || empty($email) || empty($password) || empty($birthdate) || empty($security_question1) || empty($security_answer1) || empty($security_question2) || empty($security_answer2)) {
      echo "All fields are required.";
      exit;
   }

   // Prepend "@" to the user ID
   if ($user_id[0] !== '@') {
      $user_id = '@' . $user_id;
   }

   // Check if user ID or email already exists
   $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE user_id = :user_id OR email = :email");
   $stmt->bindParam(':user_id', $user_id);
   $stmt->bindParam(':email', $email);
   $stmt->execute();
   if ($stmt->fetchColumn() > 0) {
      echo "User ID or Email already exists.";
      exit;
   }

   // Hash the password
   $hashed_password = password_hash($password, PASSWORD_BCRYPT);

   // Insert user data into the database
   $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, user_id, email, password, birthdate, security_question1, security_answer1, security_question2, security_answer2) VALUES (:first_name, :last_name, :user_id, :email, :password, :birthdate, :security_question1, :security_answer1, :security_question2, :security_answer2)");
   $stmt->bindParam(':first_name', $first_name);
   $stmt->bindParam(':last_name', $last_name);
   $stmt->bindParam(':user_id', $user_id);
   $stmt->bindParam(':email', $email);
   $stmt->bindParam(':password', $hashed_password);
   $stmt->bindParam(':birthdate', $birthdate);
   $stmt->bindParam(':security_question1', $security_question1);
   $stmt->bindParam(':security_answer1', $security_answer1);
   $stmt->bindParam(':security_question2', $security_question2);
   $stmt->bindParam(':security_answer2', $security_answer2);

   if ($stmt->execute()) {
      echo "Signup successful!";
      header('Location: login.html');
      exit;
   } else {
      echo "Error: Could not sign up.";
   }
}




// include 'db.php';

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//    $email = $_POST['email'];
//    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

//    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
//    $stmt->bindParam(':email', $email);
//    $stmt->bindParam(':password', $password);

//    if ($stmt->execute()) {
//       echo "Signup successful!";
//       header('Location: login.html');
//    } else {
//       echo "Error: Could not sign up.";
//    }
// }