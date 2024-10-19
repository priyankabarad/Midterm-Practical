<?php
session_start();
require('db_connection_mysqli.php');

// Initialize variables
$username = $password = "";
$usernameErr = $passwordErr = "";

// Function to sanitize form inputs
function cleanInput($data) {
    return htmlspecialchars(trim($data));
}

// Validate login inputs
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = cleanInput($_POST["username"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = cleanInput($_POST["password"]);
    }

    // If all validations pass, proceed with login
    if (empty($usernameErr) && empty($passwordErr)) {
        $query = "SELECT * FROM admin WHERE username = ?";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_logged_in'] = true;
                header("Location: index.php"); 
                exit;
            } else {
                $passwordErr = "Invalid username or password.";
            }
        } else {
            $usernameErr = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FF6F61, #6FA3EF);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
            transition: transform 0.3s ease;
        }

        .login-container:hover {
            transform: scale(1.02);
        }

        h2 {
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            height: 50px;
            border-radius: 25px;
            border: 1px solid #ddd;
            padding: 0 20px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #FF6F61; /* Coral */
            box-shadow: 0 0 5px rgba(255, 111, 97, 0.5);
        }

        .btn-primary {
            background-color: #FF6F61; /* Coral */
            border: none;
            border-radius: 25px;
            height: 50px;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-primary:hover {
            background-color: #6FA3EF; /* Sky Blue */
            transform: translateY(-2px);
        }

        .text-danger {
            font-size: 0.9em;
            color: #FF5E3A; /* Vivid Red */
            margin-top: 5px;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            .btn-primary {
                font-size: 14px;
            }
        }

    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="text-center">Admin Login</h2>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <span class="text-danger"><?php echo $usernameErr; ?></span>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="text-danger"><?php echo $passwordErr; ?></span>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
