<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'db.php';

// Handle form submission for adding a new photographer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photographer'])) {
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $facebook = $_POST['facebook'];
    $instagram = $_POST['instagram'];
    $linkedin = $_POST['linkedin'];
    $image = $_FILES['image'];

    // Handle image upload
    $image_path = '';
    if ($image['error'] === UPLOAD_ERR_OK) {
        $image_name = basename($image['name']);
        $image_path = 'uploads/' . $image_name;
        move_uploaded_file($image['tmp_name'], $image_path);
    }

    // Prepare SQL to insert photographer details
    $sql = "INSERT INTO photographers (name, bio, image_path, facebook, instagram, linkedin) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $bio, $image_path, $facebook, $instagram, $linkedin);
    
    if ($stmt->execute()) {
        // Redirect to the manage photographers page after successful addition
        header("Location: photographers.php?message=Photographer added successfully.");
        exit();
    } else {
        // Handle error
        $error = "Error adding photographer: " . $stmt->error;
    }
    
    $stmt->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Photographer - Captured Moments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
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
        <h2 class="text-center mb-4">Add Photographer</h2>

        <!-- Display error message if there is an error -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Add Photographer Form -->
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="bio" class="form-label">Bio</label>
                <textarea class="form-control" id="bio" name="bio" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="facebook" class="form-label">Facebook URL</label>
                <input type="url" class="form-control" id="facebook" name="facebook">
            </div>
            <div class="mb-3">
                <label for="instagram" class="form-label">Instagram URL</label>
                <input type="url" class="form-control" id="instagram" name="instagram">
            </div>
            <div class="mb-3">
                <label for="linkedin" class="form-label">LinkedIn URL</label>
                <input type="url" class="form-control" id="linkedin" name="linkedin">
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Photographer Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
            </div>
            <button type="submit" name="add_photographer" class="btn btn-primary">Add Photographer</button>
        </form>
    </div>

    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Captured Moments Photography. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
