<?php
session_start();

if (!isset($_SESSION['user_id'])) {
   header('Location: forgot_password.html');
   exit;
}

$security_question1 = $_SESSION['security_question1'];
$security_question2 = $_SESSION['security_question2'];
$birthdate = $_SESSION['birthdate'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Verify Security Questions</title>
   <link rel="stylesheet" href="style.css">
</head>

<body>
   <div class="signup-container">
      <form id="verify-security-questions-form" class="signup-form" action="verify_security_answers.php" method="POST">
         <h2>Verify Security Questions</h2>
         <label for="birthdate">Enter your Birthdate:</label>
         <input type="date" id="birthdate" name="birthdate" required>
         <label for="security_answer1">
            <?php echo htmlspecialchars($security_question1); ?>
         </label>
         <input type="text" id="security_answer1" name="security_answer1" required>
         <label for="security_answer2">
            <?php echo htmlspecialchars($security_question2); ?>
         </label>
         <input type="text" id="security_answer2" name="security_answer2" required>
         <button type="submit">Verify</button>
      </form>
   </div>
</body>

</html>