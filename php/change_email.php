<?php
session_start();
$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = "";     // default password for XAMPP
$database = "Movie";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have some form of authentication for the user, let's say the user ID is stored in a session variable
    $user_name = $_SESSION['username'];
    $new_email = $_POST['new_email'];
    $confirm_email = $_POST['confirm_email'];
    
    // Check if the new email matches the confirm email
    if ($new_email != $confirm_email) {
        echo "email_mismatch";
        exit;
    }
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // Set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if the new email already exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $new_email);
        $stmt->execute();
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing_user) {
            echo "email_exists";
            exit;
        }
        
        // Update the email
        $sql = "UPDATE users SET email=:email WHERE name=:username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $new_email);
        $stmt->bindParam(':username', $user_name);
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "database_error";
        }
    } catch (PDOException $e) {
        echo "database_error";
    }
}
?>
