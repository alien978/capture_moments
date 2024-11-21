<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection
include 'db.php';

// Fetch the photographer information based on the ID from the URL
if (isset($_GET['id'])) {
    $photographer_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM photographers WHERE id = ?");
    $stmt->bind_param("i", $photographer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: portfolio_photographers.php"); // Redirect if no photographer found
        exit();
    }

    $photographer = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: portfolio_photographers.php"); // Redirect if ID is not set
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($photographer['name']); ?> - Photographer Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .photographer-image {
            width: 100%;
            max-width: 400px; /* Set a maximum width for the image */
            max-height: 500px;
            height: auto; /* Maintain aspect ratio */
            object-fit: cover; /* Ensure it covers without distortion */
            margin: 0 auto; /* Center the image */
            display: block; /* Center the image within its container */
        }

        .social-links a {
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Captured Moments</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="gallery.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio_photographers.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="text-center mb-4"><?php echo htmlspecialchars($photographer['name']); ?></h2>
        <img src="<?php echo $photographer['image_path']; ?>" alt="Photographer Image" class="photographer-image mb-3">
        <p class="lead text-center"><?php echo nl2br(htmlspecialchars($photographer['bio'])); ?></p>
        
        <!-- Social Media Links -->
        <div class="text-center social-links">
            <h5>Connect with <?php echo htmlspecialchars($photographer['name']); ?></h5>
            <a href="<?php echo htmlspecialchars($photographer['facebook']); ?>" target="_blank">Facebook</a>
            <a href="<?php echo htmlspecialchars($photographer['instagram']); ?>" target="_blank">Instagram</a>
            <a href="<?php echo htmlspecialchars($photographer['linkedin']); ?>" target="_blank">LinkedIn</a>
        </div>

        <!-- Link to Photographer's Gallery -->
        <div class="text-center mt-4">
            <a href="gallery.php?photographer_id=<?php echo $photographer['id']; ?>" class="btn btn-info">View Gallery</a>
        </div>

        <div class="text-center">
            <a href="portfolio_photographers.php" class="btn btn-secondary mt-4">Back to Portfolio</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Captured Moments Photography. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
