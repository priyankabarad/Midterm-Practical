<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Initialize variables and error messages
$productName = $brand = $category = $price = $quantityAvailable = $description = $imageURL = "";
$productNameErr = $brandErr = $categoryErr = $priceErr = $quantityAvailableErr = $descriptionErr = $imageURLErr = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Product Name
    if (empty($_POST["ProductName"])) {
        $productNameErr = "Product Name is required";
    } else {
        $productName = cleanInput($_POST["ProductName"]);
    }

    // Validate Brand
    if (empty($_POST["Brand"])) {
        $brandErr = "Brand is required";
    } else {
        $brand = cleanInput($_POST["Brand"]);
    }

    // Validate Category
    if (empty($_POST["Category"])) {
        $categoryErr = "Category is required";
    } else {
        $category = cleanInput($_POST["Category"]);
    }

    // Validate Price
    if (empty($_POST["Price"])) {
        $priceErr = "Price is required";
    } else {
        $price = cleanInput($_POST["Price"]);
        if (!filter_var($price, FILTER_VALIDATE_FLOAT) || $price <= 0) {
            $priceErr = "Price must be a positive number";
        }
    }

    // Validate Quantity Available
    if (empty($_POST["QuantityAvailable"])) {
        $quantityAvailableErr = "Quantity is required";
    } else {
        $quantityAvailable = cleanInput($_POST["QuantityAvailable"]);
        if (!filter_var($quantityAvailable, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            $quantityAvailableErr = "Quantity must be a positive integer";
        }
    }

    // Validate Description
    if (empty($_POST["Description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = cleanInput($_POST["Description"]);
    }

    // Handle image upload
    if (!empty($_FILES["ImageURL"]["name"])) {
        // Specify the target directory for image upload
        $target_dir = "uploads/"; // Adjust this as needed
        $imageURL = $target_dir . basename($_FILES["ImageURL"]["name"]);

        // Check if the uploaded file is an image
        $check = getimagesize($_FILES["ImageURL"]["tmp_name"]);
        if ($check === false) {
            $imageURLErr = "File is not an image.";
        }

        // Check file size (limit to 8MB for example)
        if ($_FILES["ImageURL"]["size"] > 8000000) {
            $imageURLErr = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        $imageFileType = strtolower(pathinfo($imageURL, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageURLErr = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        $imageURLErr = "Image is required.";
    }

    // If no errors, insert the data into the database
    if (empty($productNameErr) && empty($brandErr) && empty($categoryErr) && empty($priceErr) && empty($quantityAvailableErr) && empty($descriptionErr) && empty($imageURLErr)) {
        if (move_uploaded_file($_FILES["ImageURL"]["tmp_name"], $imageURL)) {
            $stmt = $dbc->prepare("INSERT INTO beauty_products (ProductName, Brand, Category, Price, QuantityAvailable, Description, ImageURL) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssdiss", $productName, $brand, $category, $price, $quantityAvailable, $description, $imageURL);

            if ($stmt->execute()) {
                // Redirect to the index page after successful insert
                header("Location: index.php");
                exit;
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $imageURLErr = "Sorry, there was an error uploading your file.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Beauty Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html, body {
            height: 100%; /* Full height */
        }

        body {
            display: flex;
            flex-direction: column; /* Use flexbox for vertical alignment */
            background: linear-gradient(135deg, #FF6F61, #6FA3EF); /* Match index page theme */
            color: white;
        }

        .container {
            flex: 1; /* Allow the container to grow */
            background-color: #fff; /* White background for the form */
            color: black; /* Black text for readability */
            border-radius: 10px; /* Rounded corners */
            padding: 30px; /* Inner spacing */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            margin-top: 20px; /* Space above the container */
        }
        .footer {
            background-color: #6a11cb; /* Darker background for footer */
            padding: 10px 0; /* Vertical padding */
            text-align: center; /* Center text */
            color: white; /* White text */
            width: 100%; /* Full width */
            position: relative; /* Positioning */
        }

        .text-danger {
            font-size: 0.9em; /* Slightly smaller error messages */
        }

        .btn-submit {
            background-color: #28A745; /* Green for submit button */
            border: none;
            color: white;
        }

        .btn-submit:hover {
            background-color: #218838; /* Darker Green on hover */
        }

        .img-preview {
            width: 100%;
            max-width: 200px; /* Maximum width for the preview */
            height: auto; /* Maintain aspect ratio */
            border-radius: 5px; /* Rounded corners */
            margin-top: 10px; /* Top margin */
        }
    </style>
    <script>
        function previewImage() {
            const file = document.querySelector('input[type=file]').files[0];
            const preview = document.getElementById('imagePreview');
            const reader = new FileReader();
            
            reader.onloadend = function () {
                preview.src = reader.result;
                preview.style.display = "block"; // Show the image preview
            }
            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.style.display = "none"; // Hide the image preview
            }
        }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #6a11cb;">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Beauty Store</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Manage Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Add Beauty Product</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2>Add New Beauty Product</h2>
    <form method="POST" action="products.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="ProductName" class="form-label">Product Name</label>
            <input type="text" class="form-control" id="ProductName" name="ProductName" value="<?php echo $productName; ?>">
            <span class="text-danger"><?php echo $productNameErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="Brand" class="form-label">Brand</label>
            <input type="text" class="form-control" id="Brand" name="Brand" value="<?php echo $brand; ?>">
            <span class="text-danger"><?php echo $brandErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="Category" class="form-label">Category</label>
            <select class="form-select" id="Category" name="Category">
                <option value="" disabled selected>Select Category</option>
                <option value="Skincare">Skincare</option>
                <option value="Makeup">Makeup</option>
                <option value="Haircare">Haircare</option>
                <option value="Body Care">Body Care</option>
                <option value="Fragrance">Fragrance</option>
                <option value="Nail Care">Nail Care</option>
            </select>
            <span class="text-danger"><?php echo $categoryErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="Price" class="form-label">Price</label>
            <input type="number" class="form-control" id="Price" name="Price" value="<?php echo $price; ?>" step="0.01">
            <span class="text-danger"><?php echo $priceErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="QuantityAvailable" class="form-label">Quantity Available</label>
            <input type="number" class="form-control" id="QuantityAvailable" name="QuantityAvailable" value="<?php echo $quantityAvailable; ?>">
            <span class="text-danger"><?php echo $quantityAvailableErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="Description" class="form-label">Description</label>
            <textarea class="form-control" id="Description" name="Description"><?php echo $description; ?></textarea>
            <span class="text-danger"><?php echo $descriptionErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="ImageURL" class="form-label">Image File</label>
            <input type="file" class="form-control" id="ImageURL" name="ImageURL" accept="image/*" onchange="previewImage()">
            <span class="text-danger"><?php echo $imageURLErr; ?></span>
            <img id="imagePreview" class="img-preview" src="" alt="Image Preview" style="display: none;">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<footer class="footer mt-auto py-3">
    <div class="container">
        © 2024 Beauty Store. All rights reserved.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
