<?php
require('db_connection_mysqli.php');
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Prepare the delete query
    $deleteQuery = "DELETE FROM beauty_products WHERE ProductID = ?"; 
    $deleteStmt = mysqli_prepare($dbc, $deleteQuery);
    
    if ($deleteStmt) {
        mysqli_stmt_bind_param($deleteStmt, 'i', $deleteId);
        
        // Check if the statement executed successfully
        if (mysqli_stmt_execute($deleteStmt)) {
            header("Location: index.php"); // Redirect after successful deletion
            exit;
        } else {
            echo "Error deleting record: " . mysqli_error($dbc);
        }

        // Close the statement
        mysqli_stmt_close($deleteStmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($dbc);
    }
} else {
    // Redirect to index.php if no delete_id is set
    header("Location: index.php");
    exit;
}
?>
