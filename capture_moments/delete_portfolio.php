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

// Check if the portfolio item ID is provided in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the item to get the image path
    $stmt = $conn->prepare("SELECT image_path FROM portfolio_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    // Delete the image file from the server
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // Delete the item from the database
    $stmt = $conn->prepare("DELETE FROM portfolio_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: admin.php?message=Portfolio item deleted successfully.");
        exit();
    } else {
        echo "Error deleting portfolio item: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid portfolio item ID.";
}

$conn->close();
?>
