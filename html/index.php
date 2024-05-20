<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if not logged in
    header("Location: login.html");
    exit();
}

if (isset($_GET['signup_success']) && $_GET['signup_success'] === 'true') {
    // Set a flag to show the SweetAlert
    $showSweetAlert = true;
} else {
    // Set the flag to false if not showing SweetAlert
    $showSweetAlert = false;
}

$servername = "localhost";
$username = "root"; // default username for XAMPP
$password = "";     // default password for XAMPP
$dbname = "Movie"; // Change this to your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
    // Query to select information of the last movie
    $sql = "SELECT * FROM movies ORDER BY idM DESC LIMIT 1";
    $stmt = $conn->query($sql);
    $movie = $stmt->fetch();
    $sql = "SELECT * FROM movies";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = "SELECT * FROM anime";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $anime = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group movies by their types
    $groupedMovies = [];
    foreach ($movies as $movie) {
        $types = explode(',', $movie['Type']);
        foreach ($types as $type) {
            $type = trim($type);
            if (!isset($groupedMovies[$type])) {
                $groupedMovies[$type] = [];
            }
            $groupedMovies[$type][] = $movie;
        }
    }
    foreach ($anime as $an) {
        $types = explode(',', $an['Type']);
        foreach ($types as $type) {
            $type = trim($type);
            if (!isset($groupedMovies[$type])) {
                $groupedMovies[$type] = [];
            }
            $groupedMovies[$type][] = $an;
        }
    }
    
    
    // Close the connection
    $conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie🎬</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lemon&family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/swiper-bundle.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bb.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    <style>
        /* Your CSS styles here */
        .pic iframe {
            width: 330px; /* Adjust size as needed */
            height: 400px; /* Maintain aspect ratio */
        }
        .descp .movie-content {
            gap: 5rem; /* Adjust gap between image and content */
        } body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .movies-section {
            width: 100%;
            padding: 10px 20px 20px 20px;
            background-color:darkgray;
            margin: 30px 0px 30px 0px;
            border-radius: 10px;
        }

        .movie-card {
            position: relative;
            width: 200px;
            height: 300px;
            background-color: #101116;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            margin: 10px;
        }

        .movie-image {
            width: 100%;
            height: 50%;
            object-fit: cover;
        }

        .movie-card-content {
            padding: 10px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            flex-grow: 1;
        }

        .movie-card-title {
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
        }

        .movie-card-description {
            font-size: 0.8rem;
        }

        footer {
            background-color: #1e1e1e;
            color: white;
            text-align: center;
            
        }

        .footer-social i {
        margin: 0 10px;
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
                        <li><a href="anime.php">Animated Movies</a></li>
                        <li><a href="list.php">My List</a></li>
                        <div class="close-btn" id="close">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                    </ul>
                    <ul class="user">
                        <div class="search" id="search">
    <ul>
        <li>
            <form action="search_results.php" method="GET" id="search-form">
                <input type="text" name="query" id="search-input" placeholder="Search...">
            </form>
        </li>
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
        <?php if ($showSweetAlert): ?>
        <div id="signup-success-alert" class="hidden">
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "success",
                        title: "Signup successful!",
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            </script>
        </div>
        <?php endif; ?>
        <div class="breaking-bad">
            <?php echo '<a href="movie_details.php?id=' . htmlspecialchars($movie["idM"]) . '">'; ?>
            <div class="descp">
                <div class="pic">
                    <iframe src="<?php echo $movie['pic']; ?>" frameborder="0" class="pic"></iframe>
                </div>
                <div class="movie-content">
                <?php
    echo "<p style='color: #c31ca1; font-weight: bold; font-size: 2.5rem; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);'>🎬 New Movie! 🍿</p>";
?>

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
                    </div>
                </div>
            </div>
            <?php echo'</a>'; ?>
        </div>
    <?php
       $types = ['Drama', 'Comedy', 'Romantic', 'Action'];
       foreach ($types as $type) {
           if (isset($groupedMovies[$type])) {
            echo '<div class="movies-section">';
            echo '<h2 class="type">' . htmlspecialchars($type) . '</h2>';
            echo '<div class="owl-carousel">';
            foreach ($groupedMovies[$type] as $movie) {
                $pic_path = htmlspecialchars($movie["pic"]);
                echo '<div class="movie-card">';
                echo '<a href="movie_details.php?id=' . htmlspecialchars($movie["idM"]) . '">';
                echo '<iframe src="' . $pic_path . '" frameborder="0" class="movie-image" width="200" height="300"></iframe>'; // Example width and height
                echo '<div class="movie-card-content" style="width: 200px;">'; // Example width
                echo '<h2 class="movie-card-title">' . htmlspecialchars($movie["Name"]) . '</h2>';
                echo '<p class="movie-card-description" style="height: 100px; overflow: hidden;">' . htmlspecialchars($movie["Desc"]) . '</p>'; // Example height and overflow
                echo '</div></a></div>';
            }
            echo '</div></div>';
       }
    }
       ?>
    
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
    </div>
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.3.slim.min.js" integrity="sha256-ZwqZIVdD3iXNyGHbSYdsmWP//UBokj2FHAxKuSBKDSo=" crossorigin="anonymous"></script>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- Include OwlCarousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <!-- Include Swiper JS -->
    <script src="../js/swiper-bundle.min.js"></script>
    <!-- Include your custom JS -->
    <script src="../js/main.js"></script>

    <!-- Show SweetAlert Popup -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var signupSuccessAlert = document.getElementById("signup-success-alert");
            if (signupSuccessAlert) {
                signupSuccessAlert.classList.remove("hidden");
            }

// Initialize Owl Carousel
$('.owl-carousel').each(function() {
    var $movieCards = $(this).find('.movie-card');
    var moviesCount = $movieCards.length;
    var itemsToShow = Math.min(moviesCount, 5); // Show all available movies if less than 5, otherwise show up to 5
    
    // Remove duplicates if there are fewer than 5 movies
    if (moviesCount < 5) {
        var uniqueMovies = [];
        $movieCards.each(function() {
            var movieTitle = $(this).find('.movie-card-title').text().trim();
            if (!uniqueMovies.includes(movieTitle)) {
                uniqueMovies.push(movieTitle);
            } else {
                $(this).remove();
            }
        });
    }
    
    $(this).owlCarousel({
        items: itemsToShow,
        loop: true,
        margin: 10,
        nav: true,
        navText: ['',''], // Remove text navigation arrows
        responsive: {
            0: {
                items: Math.min(itemsToShow, 1) // Show 1 item on smaller screens if available
            },
            600: {
                items: Math.min(itemsToShow, 3) // Show 3 items on smaller screens if available
            },
            1000: {
                items: itemsToShow
            }
        }
    });
});


        });
    </script>
</body>
</html>
