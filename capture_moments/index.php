<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captured Moments - Portfolio</title>
    <!-- Link Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .hero {
            position: relative;
            height: 90vh; /* Adjust as needed */
            background: url('arko.jpg') no-repeat center center/cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-style: italic;
            text-align: center;
            padding: 0 10%; /* Add padding for smaller screens */
        }
        .hero h1 {
            
            margin-bottom: 10px; /* Space between title and subtitle */
        }
        .about-us {
            background-color: #f8f9fa; /* Light background for contrast */
            padding: 60px 0; /* Adjust padding as needed */
        }
        .contact-details {
            background-color: #f8f9fa; /* Match light background of About Us */
            padding: 40px 0;
        }
        .contact-details h3 {
            margin-bottom: 20px;
        }
        .contact-details p {
            font-size: 1.1rem;
        }
        .contact-icon {
            margin-right: 10px;
        }
        .contact-link {
            margin-top: 20px;
            display: inline-block; /* Button width will be based on content */
            background-color: #6c757d; /* Grey color */
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .contact-link:hover {
            background-color: #5a6268; /* Darker grey on hover */
            text-decoration: none; /* Keep it clean with no underline */
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

    <!-- Hero Section -->
    <section class="hero">
        <h1 class="display-4">Welcome to Captured Moments</h1>
        <p class="lead">The beauty of capturing the essence for the moment <br>and reliving it forever. </p>
    </section>

    <!-- About Us Section -->
    <section class="about-us text-center">
        <div class="container">
            <h2 class="mb-4">Captured Moments</h2>
            <p>At Captured Moments, we believe that every moment tells a story. Our team of professional photographers is dedicated to capturing the essence of your most cherished events, whether it's a wedding, a family gathering, or a special milestone. With a keen eye for detail and a passion for photography, we strive to provide you with stunning images that you can treasure for a lifetime.</p>
            <p>Our portfolio showcases a diverse range of photography styles, ensuring that we can cater to your unique needs. Let us help you preserve your memories in the most beautiful way possible.</p>
        </div>
    </section>

    <!-- Contact Details Section -->
    <section class="contact-details text-center">
        <div class="container">
            <h3>Contact Us</h3>
            <p><strong>Email:</strong> info@capturedmoments.com</p>
            <p><strong>Phone:</strong> +977-9823454334</p>
            <p><strong>Location:</strong> Janakinagar, Tilottama, Nepal</p>

            <!-- Contact Form Link -->
            <a href="contact.php" class="contact-link">Leave Us a Message</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-light text-center py-3">
        <p>&copy; 2024 Captured Moments Photography. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
