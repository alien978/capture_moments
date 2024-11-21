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

    $sql = "INSERT INTO photographers (name, bio, image_path, facebook, instagram, linkedin) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $name, $bio, $image_path, $facebook, $instagram, $linkedin);
    $stmt->execute();
    $stmt->close();
}

// Handle form submission for editing a photographer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_photographer'])) {
    $id = $_POST['id'];
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

    // Update query
    if ($image_path) {
        $sql = "UPDATE photographers SET name = ?, bio = ?, facebook = ?, instagram = ?, linkedin = ?, image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $bio, $facebook, $instagram, $linkedin, $image_path, $id);
    } else {
        $sql = "UPDATE photographers SET name = ?, bio = ?, facebook = ?, instagram = ?, linkedin = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $bio, $facebook, $instagram, $linkedin, $id);
    }
    $stmt->execute();
    $stmt->close();
}

// Handle deletion of a photographer
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM photographers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch photographers
$photographers_sql = "SELECT * FROM photographers ORDER BY id DESC";
$photographers_result = $conn->query($photographers_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Photographers - Captured Moments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .gallery-card img {
            border-radius: 0; /* Sharp edges */
            transition: transform 0.3s;
        }

        .gallery-card img:hover {
            transform: scale(1.05);
        }
    </style>
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
        <h2 class="text-center mb-4">Manage Photographers</h2>

        <a href="add_photographer.php" class="btn btn-primary mb-3">Add New Photographer</a>

        <!-- Photographers Table -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Bio</th>
                    <th>Image</th>
                    <th>Social Links</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($photographers_result->num_rows > 0): ?>
                    <?php while($photographer = $photographers_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($photographer['name']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($photographer['bio'])); ?></td> <!-- Format bio to handle line breaks -->
                            <td>
                                <?php if ($photographer['image_path']): ?>
                                    <img src="<?php echo $photographer['image_path']; ?>" alt="Photographer Image" style="height: 100px; border-radius: 0;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo htmlspecialchars($photographer['facebook'] ?? '#'); ?>" target="_blank">Facebook</a> |
                                <a href="<?php echo htmlspecialchars($photographer['instagram'] ?? '#'); ?>" target="_blank">Instagram</a> |
                                <a href="<?php echo htmlspecialchars($photographer['linkedin'] ?? '#'); ?>" target="_blank">LinkedIn</a>
                            </td>
                            <td>
                                <a href="gallery.php?photographer_id=<?php echo $photographer['id']; ?>" class="btn btn-info btn-sm">View Gallery</a>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $photographer['id']; ?>">Edit</button>
                                <a href="?delete_id=<?php echo $photographer['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this photographer?');">Delete</a>
                            </td>
                        </tr>

                        <!-- Edit Photographer Modal -->
                        <div class="modal fade" id="editModal<?php echo $photographer['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Photographer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?php echo $photographer['id']; ?>">
                                            <div class="mb-3">
                                                <label for="edit_name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="edit_name" name="name" value="<?php echo htmlspecialchars($photographer['name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_bio" class="form-label">Bio</label>
                                                <textarea class="form-control" id="edit_bio" name="bio" rows="3" required><?php echo htmlspecialchars($photographer['bio']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_facebook" class="form-label">Facebook URL</label>
                                                <input type="url" class="form-control" id="edit_facebook" name="facebook" value="<?php echo htmlspecialchars($photographer['facebook']); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_instagram" class="form-label">Instagram URL</label>
                                                <input type="url" class="form-control" id="edit_instagram" name="instagram" value="<?php echo htmlspecialchars($photographer['instagram']); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_linkedin" class="form-label">LinkedIn URL</label>
                                                <input type="url" class="form-control" id="edit_linkedin" name="linkedin" value="<?php echo htmlspecialchars($photographer['linkedin']); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label for="edit_image" class="form-label">Photographer Image (Leave empty to keep current image)</label>
                                                <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" name="edit_photographer" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No photographers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Captured Moments Photography. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>
