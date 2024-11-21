<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define the session timeout duration (15 minutes)
$timeout_duration = 900; // 900 seconds = 15 minutes

// Check if the user is logged in and if session has expired
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
} elseif (isset($_SESSION['logged_in_time']) && (time() - $_SESSION['logged_in_time']) > $timeout_duration) {
    // If session has expired, destroy session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: login.php?message=Session expired. Please log in again.");
    exit();
} else {
    // Update the logged-in time to the current time
    $_SESSION['logged_in_time'] = time();
}

// Include the database connection
include 'db.php';

// Fetch portfolio items
$portfolio_sql = "SELECT * FROM portfolio_items ORDER BY created_at DESC";
$portfolio_result = $conn->query($portfolio_sql);

// Fetch contact messages
$messages_sql = "SELECT * FROM contact_messages ORDER BY submitted_at DESC";
$messages_result = $conn->query($messages_sql);

// Fetch photographers
$photographers_sql = "SELECT * FROM photographers ORDER BY id DESC";
$photographers_result = $conn->query($photographers_sql);

// Fetch categories
$categories_sql = "SELECT DISTINCT category FROM portfolio_items";
$categories_result = $conn->query($categories_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Captured Moments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link active" href="admin.php">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Content -->
    <section class="admin py-5">
        <div class="container">

            <!-- Manage Portfolio Section -->
            <div class="accordion mb-4" id="portfolioAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingPortfolio">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePortfolio" aria-expanded="true" aria-controls="collapsePortfolio">
                            Manage Portfolio Items
                        </button>
                    </h2>
                    <div id="collapsePortfolio" class="accordion-collapse collapse show" aria-labelledby="headingPortfolio" data-bs-parent="#portfolioAccordion">
                        <div class="accordion-body">
                            <a href="portfolio.php" class="btn btn-primary mb-3">View All Portfolio Items</a>
                            <a href="add_portfolio.php" class="btn btn-primary mb-3">Add New Portfolio Item</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Contact Messages Section -->
            <div class="accordion mb-4" id="messagesAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingMessages">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMessages" aria-expanded="false" aria-controls="collapseMessages">
                            View Contact Messages
                        </button>
                    </h2>
                    <div id="collapseMessages" class="accordion-collapse collapse" aria-labelledby="headingMessages" data-bs-parent="#messagesAccordion">
                        <div class="accordion-body">
                            <a href="contact_messages.php" class="btn btn-primary mb-3">View All Messages</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manage Photographers Section -->
            <div class="accordion mb-4" id="photographersAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingPhotographers">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePhotographers" aria-expanded="false" aria-controls="collapsePhotographers">
                            Manage Photographers
                        </button>
                    </h2>
                    <div id="collapsePhotographers" class="accordion-collapse collapse" aria-labelledby="headingPhotographers" data-bs-parent="#photographersAccordion">
                        <div class="accordion-body">
                            <a href="photographers.php" class="btn btn-primary mb-3">View All Photographers</a>
                            <a href="add_photographer.php" class="btn btn-primary mb-3">Add New Photographer</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manage Categories Section -->
            <div class="accordion mb-4" id="categoriesAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingCategories">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories" aria-expanded="false" aria-controls="collapseCategories">
                            Manage Categories
                        </button>
                    </h2>
                    <div id="collapseCategories" class="accordion-collapse collapse" aria-labelledby="headingCategories" data-bs-parent="#categoriesAccordion">
                        <div class="accordion-body">
                            <a href="categories.php" class="btn btn-primary mb-3">View All Categories</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout Button (inside the page) -->
            <div class="text-end mb-3">
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>

        </div>
    </section>

    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Captured Moments Photography. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
