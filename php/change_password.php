<?php
session_start();
$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = "";     // default password for XAMPP
$database = "Movie";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have some form of authentication for the user, let's say the user ID is stored in a session variable
    $user_name = $_SESSION['username'];
    $userpassword = $_SESSION['password'];
    // Retrieve form data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if($current_password != $userpassword){
        echo "wrong_password";
        exit;
    }
    // Check if the new passwords match
    if ($new_password != $confirm_password) {
        echo "password_mismatch";
        exit;
    }
    

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // Set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Update the password
        $sql = "UPDATE users SET password=:password WHERE name=:username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':password', $new_password);
        $stmt->bindParam(':username', $user_name);
        
        if ($stmt->execute()) {
            echo "success";
            $_SESSION["password"] = $new_password;
        } else {
            echo "database_error";
        }
    } catch (PDOException $e) {
        echo "database_error";
    }
}
?>
