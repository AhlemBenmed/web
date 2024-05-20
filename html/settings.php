<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movie&#x1F3AC; </title>
    <link rel="stylesheet" href="../css/style1.css" media="screen" type="text/css">
</head>
<body>
    <div id="container">
        <h1>Settings &#x1F6E0;</h1>
        <div class="settings-buttons">
            <button type="button" onclick="window.location.href='change_password.php'">Change Password</button>
            <button type="button" onclick="window.location.href='change_username.php'">Change Username</button>
            <button type="button" onclick="window.location.href='change_email.php'">Change Email</button>
        </div>
        <p><a href="index.php">Back to the Home page</a></p>
    </div>
</body>
</html>
