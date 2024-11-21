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

// Fetch the portfolio item to edit
if (isset($_GET['id'])) {
    $portfolio_id = $_GET['id'];

    // Fetch the portfolio item from the database
    $stmt = $conn->prepare("SELECT * FROM portfolio_items WHERE id = ?");
    $stmt->bind_param("i", $portfolio_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $portfolio_item = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: admin.php");
    exit();
}

// Fetch categories for the dropdown
$category_query = "SELECT * FROM categories ORDER BY category ASC";
$category_result = $conn->query($category_query);

// Fetch photographers for the dropdown
$photographer_query = "SELECT * FROM photographers ORDER BY name ASC";
$photographer_result = $conn->query($photographer_query);

// Handle form submission for updating the portfolio item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $photographer_id = $_POST['photographer_id'];
    $image = $_FILES['image'];

    $valid_formats = ['image/jpeg', 'image/png', 'image/gif'];  // Allowed image formats
    $new_image_path = $portfolio_item['image_path']; // Default to the current image path

    // If a new image is uploaded, validate the image
    if (!empty($image['name'])) {
        if (!in_array($image['type'], $valid_formats)) {
            $error = "Invalid image format. Please upload JPG, PNG, or GIF files only.";
        } else {
            $image_name = basename($image['name']);
            $new_image_path = 'uploads/' . $image_name;

            // Delete the old image if it exists
            if (file_exists($portfolio_item['image_path'])) {
                unlink($portfolio_item['image_path']);
            }

            // Move the uploaded file
            move_uploaded_file($image['tmp_name'], $new_image_path);
        }
    }

    // If there's no error, update the portfolio item in the database
    if (!isset($error)) {
        $sql = "UPDATE portfolio_items SET  category = ?, photographer_id = ?, image_path = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $category, $photographer_id, $new_image_path, $portfolio_id);

        if ($stmt->execute()) {
            header("Location: admin.php?message=Portfolio item updated successfully.");
            exit();
        } else {
            $error = "Error updating portfolio item: " . $conn->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Portfolio Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-5">Edit Portfolio Item</h2>

        <!-- Display error messages -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Form for editing the portfolio item -->
        <form action="edit_portfolio.php?id=<?php echo $portfolio_id; ?>" method="POST" enctype="multipart/form-data">
          
            <div class="mb-3">
                <label for="category" class="form-label">Select Category</label>
                <select name="category" class="form-select" id="category" required>
                    <option value="">Choose a category</option>
                    <?php while ($category_row = $category_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($category_row['category']); ?>" <?php echo ($portfolio_item['category'] === $category_row['category']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category_row['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="photographer_id" class="form-label">Select Photographer</label>
                <select name="photographer_id" class="form-select" id="photographer_id" required>
                    <option value="">Choose a photographer</option>
                    <?php while ($photographer_row = $photographer_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($photographer_row['id']); ?>" <?php echo ($portfolio_item['photographer_id'] == $photographer_row['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($photographer_row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload New Image (JPG, PNG, GIF only)</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/png, image/gif">
                <p>Current Image:</p>
                <img src="<?php echo $portfolio_item['image_path']; ?>" alt="Portfolio Image" style="height: 100px;">
            </div>
            <button type="submit" class="btn btn-primary">Update Portfolio Item</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
