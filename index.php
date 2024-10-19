<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Fetch existing beauty products from the database
$query = "SELECT * FROM beauty_products";
$result = mysqli_query($dbc, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Beauty Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"> <!-- Font Awesome CSS -->
    <style>
        html, body {
            height: 100%; /* Full height */
        }

        body {
            display: flex;
            flex-direction: column; /* Use flexbox for vertical alignment */
            background: linear-gradient(135deg, #FF6F61, #6FA3EF); /* Match login page theme */
            color: white;
        }

        .container {
            flex: 1; /* Allow the container to grow */
        }

        .table {
            background-color: #ffffff; /* Table background */
            color: black; /* Text color inside table */
            border-radius: 10px; /* Rounded corners for table */
            overflow: hidden; /* Prevents overflow for rounded corners */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        .table th, .table td {
            vertical-align: middle; /* Center-align the content */
        }

        .table thead {
            background-color: #FF4C4C; /* New Header background color (Bright Red) */
            color: white; /* Header text color */
        }
        .footer {
            background-color: #6a11cb; /* Darker background for footer */
            padding: 10px 0; /* Vertical padding */
            text-align: center; /* Center text */
            color: white; /* White text */
            width: 100%; /* Full width */
            position: relative; /* Positioning */
        }
        .product-image {
            width: 100%; /* Fill the width of the cell */
            height: 100px; /* Fixed height */
            object-fit: cover; /* Maintain aspect ratio while covering the element */
        }

        .btn-edit {
            background-color: #28A745; /* Green */
            border: none;
            color: white;
        }

        .btn-edit:hover {
            background-color: #218838; /* Darker Green on hover */
        }

        .btn-delete {
            background-color: #DC3545; /* Red */
            border: none;
        }

        .btn-delete:hover {
            background-color: #C82333; /* Darker Red on hover */
        }

        .nav-link {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
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
        <h2 class="text-center">Beauty Products</h2>
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity Available</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display each beauty product in a table row
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        // Image display with validation
                        $imageURL = htmlspecialchars($row['ImageURL']);
                        echo "<td><img src='$imageURL' alt='" . htmlspecialchars($row['ProductName']) . "' class='product-image'></td>"; // Updated line
                        echo '<td>' . htmlspecialchars($row['ProductName']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['Brand']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['Category']) . '</td>';
                        echo '<td>$' . htmlspecialchars($row['Price']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['QuantityAvailable']) . '</td>';
                        echo '<td>' . htmlspecialchars($row['Description']) . '</td>';
                        // Edit and Delete Buttons
                        echo '<td>';
                        echo '<a href="edit.php?ProductID=' . $row['ProductID'] . '" class="btn btn-warning btn-sm"><i class="fas fa-pencil-alt"></i> Edit</a> ';
                        echo '<a href="delete.php?delete_id=' . $row['ProductID'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\');"><i class="fas fa-trash-alt"></i> Delete</a>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No products found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div> <!-- End container -->

    <!-- Footer -->
    <footer class="footer mt-auto py-3">
        <div class="container">
            © 2024 Beauty Store. All rights reserved.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
