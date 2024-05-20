<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = "";     // default password for XAMPP
$dbname = "Movie";  // Change this to your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

if (!isset($_GET['id'])) {
    echo "Movie ID is missing!";
    exit();
}

$movieId = $_GET['id'];

$sql = "SELECT * FROM movies WHERE idM = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $movieId]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    echo "Movie not found!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['movie_id'])) {
        $username = $_SESSION['username'];

        try {
            // Get user ID from the users table
            $stmt = $conn->prepare("SELECT idu FROM users WHERE name = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $userId = $user['idu'];

                // Insert the movie into the list table
                $stmt = $conn->prepare(" INSERT INTO list (idu, idM) VALUES (:user_id, :movie_id)");
                $stmt->execute(['user_id' => $userId, 'movie_id' => $_POST['movie_id']]);

                echo "Success";
                exit(); // Ensure no further output after sending the success message
            } else {
                echo "User not found";
                exit(); // Ensure no further output after sending the error message
            }
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit(); // Ensure no further output after sending the error message
        }
    } elseif (isset($_POST['comment'])) {
        $comment = $_POST['comment'];
        $currentUser = $_SESSION['username'];

        // Query to get user_id from the users table
        $sql = "SELECT idu FROM users WHERE name = :username";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['username' => $currentUser]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo "User not found.";
            exit();
        }
        
        $user_id = $user['idu'];
        
        try {
            // Insert the comment into the database
            $stmt = $conn->prepare("INSERT INTO comments (idu, id, comment, dateheure) VALUES (:user_id, :id, :comment, DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:%s'))");
            $stmt->execute(['user_id' => $user_id, 'id' => $movieId, 'comment' => $comment]);
            
            // Fetch the newly inserted comment
            $stmt = $conn->prepare("SELECT * FROM comments WHERE id = :id ORDER BY dateheure DESC LIMIT 1");
            $stmt->execute(['id' => $movieId]);
            $newComment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Prepare HTML markup for the new comment
            $html = '<div class="user">';
            $html .= '<div class="profile"></div>';
            $html .= '<div class="user-com">';
            $html .= '<div class="info">';
            $html .= '<div class="name"><h4>' . $currentUser . '</h4></div>';
            $html .= '<div class="date"><p><span>' . $newComment['dateheure'] . '</span></p></div>';
            $html .= '</div>';
            $html .= '<div class="comment"><br><p>' . $newComment['comment'] . '</p></div>';
            $html .= '</div></div>';
            
            // Send the HTML markup of the new comment back as response
            echo $html;
            exit();
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}

// Fetch comments from the database
$stmt = $conn->prepare("SELECT * FROM comments WHERE id = :id ORDER BY dateheure DESC");
$stmt->execute(['id' => $movieId]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($movie['Name']); ?> - Movie Details</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lemon&family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="../css/swiper-bundle.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bb.css">
    <link rel="stylesheet" href="../css/searchfile.css">
    <link rel="stylesheet" href="../css/citizenfilmcss.css">
    <style>
        .descp {
            margin: 20px;
            padding: 20px;
            border-radius: 10px;
        }
        .pic iframe {
            width: 330px;
            height: 350px;
        }
        .descp .movie-content {
            gap: 5rem;
        } 
        .movie-image {
            width: 100%;
            height: 50%;
            object-fit: cover;
        }
        .movie-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .movie-type {
            display: flex;
            gap: 10px;
        }
        .imdb {
            margin-top: 10px;
        }
        .trailer {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .trailer .save {
            display: flex;
            gap: 10px;
        }
        .btn-trailer {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-trailer:hover {
            background-color: #0056b3;
        }
        .movie-desc-2 .-img img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .comments .user-com {
            display: flex;
            gap: 10px;
        }
        .comments .user-com .info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <h1>movie</h1>
            </div>
            <nav class="navBar">
                <div class="open-btn" id="open">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="nav-items">
                    <ul class="list">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="movies.php">Movies</a></li>
                        <li><a href="movies.php">Animated Movies</a></li>
                        <li><a href="list.php">My List</a></li>
                        <div class="close-btn" id="close">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                    </ul>
                    <ul class="user">
                        <div class="search" id="search">
                            <ul>
                                <li><input type="text"></li>
                            </ul>
                        </div>
                        <li id="search-icon"><i class="fa-solid fa-magnifying-glass"></i></li>
                        <li><i class="fa-solid fa-bell"></i></li>
                        <li id="user-icon"><i class="fa-solid fa-user"></i></li>
                        <div class="menu" id="menu">
                            <ul>
                                <li><a href="../php/Logout.php">Logout</a></li>
                                <li><a href="settings.php">Settings</a></li>
                            </ul>
                        </div>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="breaking-bad">
            <div class="descp">
                <div class="pic">
                    <iframe src="<?php echo $movie['pic']; ?>" frameborder="0" class="pic"></iframe>
                </div>
                <div class="movie-content">
                    <div class="movie-trailer">
                        <h1><?php echo $movie['Name']; ?></h1>
                        <div class="movie-type">
                            <?php 
                            $Types = explode(',', $movie['Type']);
                            foreach ($Types as $Type) {
                                echo "<p>| $Type |</p>";
                            } 
                            ?>
                        </div>
                        <div class="movie-desc">
                            <br>
                            <p><?php echo $movie['Desc']; ?></p>
                        </div>
                        <div class="imdb">
                            <p>IMDb: <?php echo $movie['Rate']; ?></p>
                        </div>
                        <div class="trailer">
                            <div class="save">
                                <button style="background-color: transparent;"  id="bookmark-btn" data-movie-id="<?php echo $movie['idM']; ?>">
                                    <i class="fa-regular fa-bookmark"></i>
                                </button>
                                <a href="<?php echo htmlspecialchars($movie['linkD']); ?>" target="_blank">
                                    <i class="fa-regular fa-circle-down"></i>
                                </a>    
                                <i class="fa-regular fa-share-from-square"></i>
                            </div>
                            <div>
                                <a href="watch_movie.php?id=<?php echo htmlspecialchars($movie["idM"]); ?>" class="btn-trailer">
                                    Watch
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="comments">
            <h2>Comments</h2>
            <div class="comment-box">
                <form id="comment-form" action="" method="post">
                    <input type="text" name="comment" placeholder="Leave a Comment">
                </form>
            </div>
            <div class="users" id="commentsSection">
                <?php foreach ($comments as $comment): ?>
                    <div class="user">
                        <div class="profile"></div>
                        <div class="user-com">
                            <div class="info">
                                <div class="name">
                                    <h4>
                                    <?php 
                                    $sql = "SELECT name FROM users WHERE idu = :userid"; // Assuming 'idu' is the user ID field in the 'users' table
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute(['userid' => $comment['idu']]);
                                    $user = $stmt->fetch(PDO::FETCH_ASSOC); 
                                    echo $user['name']; 
                                    ?>
                                    </h4>
                                </div>
                                <div class="date">
                                    <p><span><?php echo $comment['dateheure']; ?></span></p>
                                </div>
                            </div>
                            <div class="comment">
                                <br><p><?php echo $comment['comment']; ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <footer class="footer">
            <div>
                <h4>Movies</h4>
                <h4>TV Series</h4>
                <h4>Animated Movies</h4>
            </div>
            <div>
                <h4>Privacy Policy</h4>
                <h4>Cookie Policy</h4>
                <h4>Terms of Use</h4>
            </div>
            <div>
                <h4>Help Center</h4>
                <h4>Support</h4>
                <h4>FAQ</h4>
            </div>
            <div>
                <h4>Follow Us</h4>
                <div class="footer-social">
                    <i class="fa-brands fa-instagram"></i>
                    <i class="fa-brands fa-facebook-f"></i>
                    <i class="fa-brands fa-twitter"></i>
                </div>
            </div>
        </footer>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var commentForm = document.getElementById("comment-form");

                commentForm.addEventListener("submit", function(event) {
                    event.preventDefault();

                    var formData = new FormData(commentForm);

                    fetch(commentForm.action, {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("commentsSection").insertAdjacentHTML("afterbegin", data);
                        commentForm.reset();
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred while adding the comment.");
                    });
                });
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var bookmarkBtn = document.getElementById("bookmark-btn");

                bookmarkBtn.addEventListener("click", function() {
                    var movieId = this.getAttribute("data-movie-id");

                    fetch("", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "movie_id=" + movieId
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data.trim() === "Success") {
                            Swal.fire({
                                icon: "success",
                                title: "Added!",
                                text: "Movie added to your list.",
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: data,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        Swal.fire({
                            icon: "error",
                            title: "Oops...",
                            text: "An error occurred.",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                });
            });
        </script>
        <script src="https://code.jquery.com/jquery-3.6.3.slim.min.js" integrity="sha256-ZwqZIVdD3iXNyGHbSYdsmWP//UBokj2FHAxKuSBKDSo=" crossorigin="anonymous"></script>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Include OwlCarousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <!-- Include Swiper JS -->
    <script src="../js/swiper-bundle.min.js"></script>
    <!-- Include your custom JS -->
    <script src="../js/main.js"></script>
    </div>
</body>
</html>
