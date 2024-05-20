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
    exit();
}

// Get the current user
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

// Query to select user's list from the 'list' table
$sql = "SELECT * FROM list WHERE idu = :user_id";
$stmt = $conn->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize arrays to hold movie details
$movies = [];
$anime = [];

// Fetch details from the respective tables
foreach ($list as $item) {
    if (!is_null($item['id'])) {
        // Fetch from anime table
        $sql = "SELECT * FROM anime WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $item['id']]);
        $anime[] = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif (!is_null($item['idM'])) {
        // Fetch from movies table
        $sql = "SELECT * FROM movies WHERE idM = :idM";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['idM' => $item['idM']]);
        $movies[] = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

$combinedList = array_merge($anime, $movies);
$uniqueList = [];
foreach ($combinedList as $item) {
    $uniqueList[$item['Name']] = $item;
}
$uniqueList = array_values($uniqueList);

// Close the connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My List🎬</title>
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
        } 
        body {
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
            background-color: darkgray;
            margin: 30px 0px 30px 0px;
            border-radius: 10px;
            display: block;
            
        }
        .movie-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content:center; /* Adjust as needed */
            margin-top: 20px; /* Add margin for spacing */
            
        }

        .movie-card {
            width: calc(33.33% - 20px); /* Adjust width of each card */
            margin-bottom: 10px;
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
        <div class="movies-section">
            <h2 class="type">My List</h2>
            <div class="movie-grid">
            <?php if (empty($item)) : ?>
            <p>Empty List Add your Movies !</p>
        <?php else : ?>
                <?php foreach ($uniqueList as $item): ?>
                <div class="movie-card">
                <?php if(isset($item['idM'])) { ?>
    <a href="movie_details.php?id=<?php echo htmlspecialchars($item['idM']); ?>"><?php } else { ?><a href="anime_details.php?id=<?php echo htmlspecialchars($item['id']); ?>"><?php } ?>
                    <iframe src="<?php echo htmlspecialchars($item['pic']); ?>" frameborder="0" class="movie-image" width="200" height="300"></iframe>
                        <div class="movie-card-content" style="width: 200px;">
                        <h2 class="movie-card-title"><?php echo htmlspecialchars($item['Name']); ?></h2>
                        <p class="movie-card-description" style="height: 100px; overflow: hidden;"><?php echo htmlspecialchars($item['Desc']); ?></p>
                    </div>
                <?php  echo '</a>'; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </div>
        <footer class="footer">
            <div>
                <h4>Movies</h4>
                <h4>TV Series</h
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
</body>
</html>