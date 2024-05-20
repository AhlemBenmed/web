<?php
session_start();
$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = "";     // default password for XAMPP
$database = "Movie";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assuming you have some form of authentication for the user, let's say the username is stored in a session variable
    $user_name = $_SESSION['username']; // Assuming you have stored the user's current username in the session
    // Retrieve form data
    $current_username = $user_name; // Current username doesn't need to be retrieved from the form
    $new_username = $_POST['new_username'];
    $confirm_username = $_POST['confirm_username'];
    
    // Check if the new username matches the confirm username
    if ($new_username != $confirm_username) {
        echo "username_mismatch";
        exit;
    }
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // Set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if the new username already exists in the database
        $stmt = $conn->prepare("SELECT * FROM users WHERE name = :username");
        $stmt->bindParam(':username', $new_username);
        $stmt->execute();
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existing_user) {
            echo "username_exists";
            exit;
        }
        
        // Update the username
        $sql = "UPDATE users SET name=:new_username WHERE name=:current_username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':new_username', $new_username);
        $stmt->bindParam(':current_username', $current_username);
        
        if ($stmt->execute()) {
            echo "success";
            $_SESSION["username"] = $new_username; // Update the username in the session
        } else {
            echo "database_error";
        }
    } catch (PDOException $e) {
        echo "database_error";
    }
}
?>
