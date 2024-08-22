<?php
session_start();
header('Content-Type: application/json');

include 'db.php';

// Check if the email session variable is set
if (!isset($_SESSION['email'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit();
}

$email = $_SESSION['email'];

// Retrieve user ID based on the email
try {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "User not found"]);
        exit();
    }

    $userId = $user['user_id'];

    // Prepare SQL query to fetch contacts excluding the current user
    $stmt = $conn->prepare("SELECT user_id, email, first_name, last_name FROM users WHERE user_id != :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
    $stmt->execute();
    $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: Log the user ID
    error_log("User ID: '$userId'");

    echo json_encode($contacts);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Query failed: " . $e->getMessage()]);
}
