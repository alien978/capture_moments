<?php
// Include the database connection
include 'db.php';

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch all photographers with pagination
$photographers_sql = "SELECT * FROM photographers ORDER BY id DESC LIMIT $limit OFFSET $offset";
$photographers_result = $conn->query($photographers_sql);

// Fetch total photographers count for pagination
$count_sql = "SELECT COUNT(*) AS total FROM photographers";
$count_result = $conn->query($count_sql);
$total_photographers = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_photographers / $limit);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captured Moments - Our Photographers</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .photographer-card {
            position: relative;
            margin-bottom: 20px;
            text-align: center;
            overflow: hidden; /*  to prevent overflow */
        }

        /* Fixed size for photographer images */
        .photographer-card img {
            width: 100%; /* Ensure responsiveness */
            height: 500px; /* Fixed height for all images */
            object-fit: cover; /* Maintain aspect ratio without distortion */
            transition: transform 0.3s ease;
        }

        .photographer-card:hover img {
            transform: scale(1.1);
        }

        .photographer-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.5); /* Transparent hover effect */
            color: white;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .photographer-card:hover .photographer-info {
            opacity: 1;
        }

        .photographer-bio {
            font-size: 14px;
            color: #ddd;
        }

        .pagination {
            justify-content: center;
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
                    <li class="nav-item"><a class="nav-link active" href="portfolio_photographers.php">Portfolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Portfolio Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Meet Our Photographers</h2>
            <div class="row">
                <?php if ($photographers_result->num_rows > 0): ?>
                    <?php while ($photographer = $photographers_result->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card photographer-card">
                                <!-- Photographer Image (Fixed Size) -->
                                <img src="<?php echo $photographer['image_path']; ?>" alt="Photographer Image" class="card-img-top">
                                
                                <!-- Photographer Information -->
                                <div class="photographer-info">
                                    <h5 class="card-title"><?php echo htmlspecialchars($photographer['name']); ?></h5>
                                    <p class="photographer-bio"><?php echo htmlspecialchars($photographer['bio']); ?></p>
                                    <a href="photographer_details.php?id=<?php echo $photographer['id']; ?>" class="btn btn-light btn-sm mt-2">View Profile</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center">No photographers found.</p>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Captured Moments Photography. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
