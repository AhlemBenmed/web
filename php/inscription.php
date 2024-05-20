<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = "";     // default password for XAMPP
$database = "Movie"; // Change this to your database name

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sanitize and validate input data (you should add more validation)
    $username = htmlspecialchars($username);
    $email = htmlspecialchars($email);
    // You can use password_hash() to hash the password before storing it in the database

    // Prepare SQL INSERT statement
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:username, :email, :password)");

    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password); // You should hash the password before binding

    // Execute SQL query
    $stmt->execute();
    session_start();
    $_SESSION['username'] = $username;
    $$_SESSION['password'] = $password;
// Redirect the user to the index.php page with a success message in the URL
header("Location: ../html/index.php?signup_success=true");
exit();

    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection (optional for PDO)
$conn = null;
?>