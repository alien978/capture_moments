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

// Fetch categories for the dropdown
$category_query = "SELECT * FROM categories ORDER BY category ASC";
$category_result = $conn->query($category_query);

// Fetch photographers for the dropdown
$photographers_sql = "SELECT * FROM photographers ORDER BY name ASC";
$photographers_result = $conn->query($photographers_sql);

// Function to resize the image using the GD library
function resizeImage($file, $maxWidth, $maxHeight) {
    list($width, $height, $type) = getimagesize($file);

    $ratio = $width / $height;

    // Determine new dimensions based on aspect ratio
    if ($maxWidth / $maxHeight > $ratio) {
        $maxWidth = $maxHeight * $ratio;
    } else {
        $maxHeight = $maxWidth / $ratio;
    }

    // Create image resource based on the file type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $src = imagecreatefromjpeg($file);
            break;
        case IMAGETYPE_PNG:
            $src = imagecreatefrompng($file);
            break;
        case IMAGETYPE_GIF:
            $src = imagecreatefromgif($file);
            break;
        default:
            return false; // Unsupported image type
    }

    // Create a new blank image with the new dimensions
    $dst = imagecreatetruecolor($maxWidth, $maxHeight);

    // Resize the original image and copy it to the new image
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $maxWidth, $maxHeight, $width, $height);

    // Save the resized image back to the file (overwrite original)
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($dst, $file, 90); // Save as JPEG with 90% quality
            break;
        case IMAGETYPE_PNG:
            imagepng($dst, $file);
            break;
        case IMAGETYPE_GIF:
            imagegif($dst, $file);
            break;
    }

    // Free up memory
    imagedestroy($src);
    imagedestroy($dst);

    return true;
}

// Handle form submission for adding a new portfolio item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $category = $_POST['category']; // Get selected category
    $photographer_id = $_POST['photographer']; // Get selected photographer
    $image = $_FILES['image'];

    $valid_formats = ['image/jpeg', 'image/png', 'image/gif'];  // Allowed image formats

    // Check for upload errors
    if ($image['error'] !== UPLOAD_ERR_OK) {
        $error = "File upload error: " . $image['error'];
    } elseif (!in_array($image['type'], $valid_formats)) {
        // Validate the image format
        $error = "Invalid image format. Please upload JPG, PNG, or GIF files only.";
    } else {
        // Handle image upload if valid
        $image_name = uniqid() . '_' . basename($image['name']); // Generate unique name
        $image_path = 'uploads/' . $image_name;

        // Ensure the uploads directory exists
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        // Move the uploaded file
        if (move_uploaded_file($image['tmp_name'], $image_path)) {
            // Resize the image
            if (resizeImage($image_path, 800, 600)) {
                // Insert new portfolio item without description
                $sql = "INSERT INTO portfolio_items ( category, photographer_id, image_path) VALUES ( ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss" ,$category, $photographer_id, $image_path);

                if ($stmt->execute()) {
                    $success = "Portfolio item added successfully!";
                    header("Location: portfolio.php"); // Redirect to manage portfolio page after successful add
                    exit();
                } else {
                    $error = "Error adding portfolio item: " . $conn->error;
                }
                $stmt->close();
            } else {
                $error = "Failed to resize the image.";
            }
        } else {
            $error = "Failed to upload the image.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Portfolio Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Add Portfolio Item</h2>

        <!-- Display success or error messages -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Form for adding a new portfolio item -->
        <form action="add_portfolio.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="category" class="form-label">Select Category</label>
                <select name="category" class="form-select" id="category" required>
                    <option value="">Choose a category</option>
                    <!-- Dynamically populate categories -->
                    <?php while ($category_row = $category_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($category_row['category']); ?>">
                            <?php echo htmlspecialchars($category_row['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="photographer" class="form-label">Select Photographer</label>
                <select name="photographer" class="form-select" id="photographer" required>
                    <option value="">Choose a photographer</option>
                    <?php while ($photographer_row = $photographers_result->fetch_assoc()): ?>
                        <option value="<?php echo $photographer_row['id']; ?>">
                            <?php echo htmlspecialchars($photographer_row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Image (JPG, PNG, GIF only)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/png, image/gif" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Portfolio Item</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
