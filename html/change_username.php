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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
    <div id="container">
        <form id="changeusernameForm" method="post" action="../php/change_username.php">
        <?php echo "<label> <h2> Welcome " . $_SESSION['username'] . " &#x1F60A;</h2></label>"; ?>
            <h1>Change username</h1>
            <label>New Username:</label>
            <input type="text" id="new_username" name="new_username" required><br><br>
            
            <label>Confirm New Username:</label>
            <input type="text" id="confirm_username" name="confirm_username" required><br><br>
            
            <button type="submit">Change username</button>
        </form>
        <p><a href="settings.php">Back to the Settings page</a></p>
        <p><a href="index.php">Back to the Home page</a></p>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var form = document.getElementById('changeusernameForm');
            form.addEventListener('submit', function(event) {
                var container = document.getElementById('container');
                container.style.position = 'fixed';
                container.style.top = '50%';
                container.style.left = '50%';
                container.style.transform = 'translate(-50%, -50%)';
                event.preventDefault();
                var formData = new FormData(form);
                fetch(form.getAttribute('action'), {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(result => {
                    if (result === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'username updated successfully',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else if (result === 'username_exists') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Current username already exists try anothe one!'
                        });
                    } else if (result === 'username_mismatch') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'New usernames do not match!'
                        });
                    } else if (result === 'database_error') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Database error. Please try again later!'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Unknown error occurred!'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'An unexpected error occurred. Please try again later!'
                    });
                });
            });
        });
    </script>
    
</body>
</html>
