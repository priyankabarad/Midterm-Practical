<?php
require('db_connection_mysqli.php');

// Initialize variables to hold form values and error messages
$productId = $productName = $brand = $category = $price = $quantityAvailable = $description = $imageURL = "";
$productNameErr = $brandErr = $categoryErr = $priceErr = $quantityAvailableErr = $descriptionErr = $imageURLErr = "";

// Check if the product ID is provided
if (isset($_GET['ProductID'])) {
    $productId = $_GET['ProductID'];

    // Fetch existing product details from the database
    $query = "SELECT * FROM beauty_products WHERE ProductID = ?";
    $stmt = mysqli_prepare($dbc, $query);
    mysqli_stmt_bind_param($stmt, 'i', $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // If the product is found, populate the form fields
    if ($row = mysqli_fetch_assoc($result)) {
        $productName = $row['ProductName'];
        $brand = $row['Brand'];
        $category = $row['Category'];
        $price = $row['Price'];
        $quantityAvailable = $row['QuantityAvailable'];
        $description = $row['Description'];
        $imageURL = $row['ImageURL'];
    } else {
        echo "Product not found!";
        exit;
    }
}

// Function to sanitize form inputs
function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Validate form inputs after submission
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
        $quantityAvailableErr = "Quantity Available is required";
    } else {
        $quantityAvailable = cleanInput($_POST["QuantityAvailable"]);
        if (!filter_var($quantityAvailable, FILTER_VALIDATE_INT, array("options" => array("min_range" => 1)))) {
            $quantityAvailableErr = "Quantity Available must be a positive integer";
        }
    }

    // Validate Description
    if (empty($_POST["Description"])) {
        $descriptionErr = "Description is required";
    } else {
        $description = cleanInput($_POST["Description"]);
    }

    // Validate Image Upload
    if (isset($_FILES["ImageURL"]) && $_FILES["ImageURL"]["error"] == UPLOAD_ERR_OK) {
        $imageURL = $_FILES["ImageURL"]["name"];
        $targetDir = "uploads/"; // Directory where you want to save the uploaded images
        $targetFile = $targetDir . basename($imageURL);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is a valid image
        $check = getimagesize($_FILES["ImageURL"]["tmp_name"]);
        if ($check === false) {
            $imageURLErr = "File is not an image.";
        } elseif (!move_uploaded_file($_FILES["ImageURL"]["tmp_name"], $targetFile)) {
            $imageURLErr = "Sorry, there was an error uploading your file.";
        }
    } else {
        // If no new image is uploaded, keep the existing image
        $imageURL = $imageURL; // Keep the original image URL
    }

    // If all validations pass, proceed with form submission
    if (empty($productNameErr) && empty($brandErr) && empty($categoryErr) && empty($priceErr) &&
        empty($quantityAvailableErr) && empty($descriptionErr) && empty($imageURLErr)) {

        // Clean inputs
        $productName_clean = mysqli_real_escape_string($dbc, $productName);
        $brand_clean = mysqli_real_escape_string($dbc, $brand);
        $category_clean = mysqli_real_escape_string($dbc, $category);
        $price_clean = mysqli_real_escape_string($dbc, $price);
        $quantityAvailable_clean = mysqli_real_escape_string($dbc, $quantityAvailable);
        $description_clean = mysqli_real_escape_string($dbc, $description);
        $imageURL_clean = mysqli_real_escape_string($dbc, $targetFile); // Use the target file path

        // Update data in the database
        $updateQuery = "UPDATE beauty_products SET ProductName=?, Brand=?, Category=?, Price=?, QuantityAvailable=?, Description=?, ImageURL=? WHERE ProductID=?";
        $updateStmt = mysqli_prepare($dbc, $updateQuery);

        // Bind parameters
        mysqli_stmt_bind_param($updateStmt, 'sssssssi', $productName_clean, $brand_clean, $category_clean, 
        $price_clean, $quantityAvailable_clean, $description_clean, $imageURL_clean, $productId);

        // Execute the statement
        $result = mysqli_stmt_execute($updateStmt);

        if ($result) {
            header("Location: index.php"); // Redirect on success to refresh the page
            exit;
        } else {
            echo "<br>Some error in updating the data.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Beauty Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Beauty Product</h2>
        <form action="edit.php?ProductID=<?php echo $productId; ?>" method="POST" enctype="multipart/form-data">
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
                    <option value="" disabled>Select Category</option>
                    <option value="Makeup" <?php if ($category == "Makeup") echo 'selected'; ?>>Makeup</option>
                    <option value="Skincare" <?php if ($category == "Skincare") echo 'selected'; ?>>Skincare</option>
                    <option value="Haircare" <?php if ($category == "Haircare") echo 'selected'; ?>>Haircare</option>
                    <option value="Fragrance" <?php if ($category == "Fragrance") echo 'selected'; ?>>Fragrance</option>
                    <option value="Nail Care" <?php if ($category == "Nail Care") echo 'selected'; ?>>Nail Care</option>
                    <option value="Tools" <?php if ($category == "Tools") echo 'selected'; ?>>Tools</option>
                </select>
                <span class="text-danger"><?php echo $categoryErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="Price" class="form-label">Price</label>
                <input type="number" class="form-control" id="Price" name="Price" step="0.01" value="<?php echo $price; ?>">
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
                <label for="ImageURL" class="form-label">Current Image</label>
                <div>
                    <?php if (!empty($imageURL)): ?>
                        <img src="<?php echo htmlspecialchars($imageURL); ?>" alt="Current Image" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <p>No image uploaded.</p>
                    <?php endif; ?>
                </div>
                <label for="ImageUpload" class="form-label">Upload New Image (optional)</label>
                <input type="file" class="form-control" id="ImageUpload" name="ImageURL">
                <span class="text-danger"><?php echo $imageURLErr; ?></span>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
