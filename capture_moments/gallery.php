<?php
// Include the database connection
include 'db.php';

// Pagination
$limit = 12; // Set the number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Check if a photographer_id is set
$photographer_id = isset($_GET['photographer_id']) ? (int)$_GET['photographer_id'] : 0;
$selected_category = isset($_GET['category']) ? $_GET['category'] : 'all'; // Get selected category or default to 'all'

// Fetch unique categories
$category_query = "SELECT DISTINCT category FROM portfolio_items";
$category_result = $conn->query($category_query);

// Fetch all portfolio items with pagination based on photographer_id and selected category
if ($photographer_id && $selected_category !== 'all') {
    $portfolio_sql = "SELECT * FROM portfolio_items WHERE photographer_id = ? AND category = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($portfolio_sql);
    $stmt->bind_param("isii", $photographer_id, $selected_category, $limit, $offset);
} elseif ($photographer_id) {
    $portfolio_sql = "SELECT * FROM portfolio_items WHERE photographer_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($portfolio_sql);
    $stmt->bind_param("iii", $photographer_id, $limit, $offset);
} elseif ($selected_category !== 'all') {
    $portfolio_sql = "SELECT * FROM portfolio_items WHERE category = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($portfolio_sql);
    $stmt->bind_param("sii", $selected_category, $limit, $offset);
} else {
    $portfolio_sql = "SELECT * FROM portfolio_items ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($portfolio_sql);
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$portfolio_result = $stmt->get_result();

// Fetch total items count for pagination
if ($photographer_id && $selected_category !== 'all') {
    $count_sql = "SELECT COUNT(*) AS total FROM portfolio_items WHERE photographer_id = ? AND category = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("is", $photographer_id, $selected_category);
} elseif ($photographer_id) {
    $count_sql = "SELECT COUNT(*) AS total FROM portfolio_items WHERE photographer_id = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("i", $photographer_id);
} elseif ($selected_category !== 'all') {
    $count_sql = "SELECT COUNT(*) AS total FROM portfolio_items WHERE category = ?";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param("s", $selected_category);
} else {
    $count_sql = "SELECT COUNT(*) AS total FROM portfolio_items";
    $count_stmt = $conn->prepare($count_sql);
}

$count_stmt->execute();
$total_items = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_items / $limit);

// Store all portfolio items in an array
$all_items = [];
while ($row = $portfolio_result->fetch_assoc()) {
    $all_items[] = $row;
}

// Shuffle the array to create a zigzag effect
shuffle($all_items);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captured Moments - Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
    <style>
        .gallery-card {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 0;
            margin-bottom: 20px;
        }

        .gallery-card img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            transition: transform 0.3s;
            border-radius: 0;
        }

        .gallery-card:hover img {
            transform: scale(1.1);
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
                    <li class="nav-item"><a class="nav-link active" href="gallery.php">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link" href="portfolio_photographers.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Filter Buttons -->
    <div class="text-center mb-4">
        <a href="gallery.php?category=all" class="btn <?php echo $selected_category === 'all' ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <?php foreach ($category_result as $category): ?>
            <a href="gallery.php?category=<?php echo urlencode($category['category']); ?>" class="btn <?php echo $selected_category === $category['category'] ? 'btn-primary' : 'btn-secondary'; ?>">
                <?php echo htmlspecialchars($category['category']); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Gallery Section -->
    <section class="gallery py-5">
        <div class="container">
            <h2 class="text-center mb-4">Photo Gallery</h2>

            <div class="row">
                <?php foreach ($all_items as $row): ?>
                    <div class="col-md-3 mb-4">
                        <div class="card gallery-card" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                            <a href="<?php echo $row['image_path']; ?>" data-lightbox="gallery">
                                <img src="<?php echo $row['image_path']; ?>" class="card-img-top" alt="Gallery Image">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                        <a class="page-link" href="gallery.php?page=<?php echo $i; ?>&category=<?php echo urlencode($selected_category); ?><?php if ($photographer_id) echo '&photographer_id=' . $photographer_id; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </section>

    <!-- Footer -->
    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Captured Moments Photography. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS and Lightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
