<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Movie ID is missing!";
    exit();
}

$movieId = $_GET['id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "Movie";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

$sql = "SELECT * FROM anime WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $movieId]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    echo "Movie not found!";
    exit();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
    <title>Movie <?php echo $movie['Name'];?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Lemon&family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet" href="../css/swiper-bundle.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/bb.css">
    <link rel="stylesheet" href="../css/st.css">
    <style>
        /* Your CSS styles here */
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
            align-items: center; /* Center content horizontally */
        }

        .centered-video {
            width: 100%;
            height: 100vh; /* Fill the entire viewport height */
            border: none; /* Remove border around iframe */
        }

        .button-container {
            width: 100%;
            max-width: 800px; /* Limit the width of the container */
            display: flex;
            justify-content: flex-start; /* Align items to the left */
            margin-top: 20px; /* Adjust margin as needed */
        }

        .button-container a.btn {
            margin-right: 10px; /* Add space between buttons */
            align-items: center;
        }

        footer {
            background-color: #1e1e1e;
            color: white;
            text-align: center;
            align-items: center;
            margin-top: auto; /* Align footer to the bottom of the page */
        }

        .footer-social i {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
        </header>
        <center><h1 style="color:chocolate;text-shadow:burlywood 2px;">Watching <?php echo $movie['Name'];?></h1></center>
        <iframe src="<?php echo htmlspecialchars($movie['linkW']); ?>" class="centered-video" allowfullscreen></iframe>
        <center><div class="button-container">
            <a href="<?php echo "anime_details.php?id=$movieId"; ?>" class="btn"> <i class="fa-regular fa-share-from-square"></i> Back to Details</a>
            <a href="<?php echo htmlspecialchars($movie['linkD']); ?>" target="_blank" class="btn"> <i class="fa-regular fa-circle-down"></i> Download Now</a>
        </div>
        </center>
    </div>
    <footer class="footer">
    </footer>

<script src="https://code.jquery.com/jquery-3.6.3.slim.min.js" integrity="sha256-ZwqZIVdD3iXNyGHbSYdsmWP//UBokj2FHAxKuSBKDSo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="../js/swiper-bundle.min.js"></script>
<script src="../js/main.js"></script>
<script src="..\js\scriptsearch.js"></script>
<script src="..\img\movies\thumb_20231228165100414.jpg"></script>
</body>
</html>

