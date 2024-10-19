<?php
require('db_connection_mysqli.php');
session_start();

$username = $password = $email = $phone_no = "";
$usernameErr = $passwordErr = $emailErr = $phoneErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
    } else {
        $username = htmlspecialchars(trim($_POST["username"]));
    }

    // Validate password
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
    } else {
        $password = htmlspecialchars(trim($_POST["password"]));
    }

    // Validate email
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
    }

    // Validate phone number
    if (empty($_POST["phone_no"])) {
        $phoneErr = "Phone number is required";
    } else {
        $phone_no = htmlspecialchars(trim($_POST["phone_no"]));
    }

    // If all validations pass
    if (empty($usernameErr) && empty($passwordErr) && empty($emailErr) && empty($phoneErr)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO admin (username, password, email, phone_no) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($dbc, $query);
        mysqli_stmt_bind_param($stmt, 'ssss', $username, $hashed_password, $email, $phone_no);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php");
            exit;
        } else {
            echo "Error registering admin: " . mysqli_error($dbc);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #FF6F61, #6FA3EF);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .registration-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            margin: auto;
            margin-top: 100px;
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
    </style>
</head>
<body>
    <div class="registration-container">
        <h2 class="text-center">Admin Registration</h2>
        <form method="POST" action="register.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
                <span class="text-danger"><?php echo $usernameErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <span class="text-danger"><?php echo $passwordErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <span class="text-danger"><?php echo $emailErr; ?></span>
            </div>
            <div class="mb-3">
                <label for="phone_no" class="form-label">Phone Number:</label>
                <input type="text" class="form-control" id="phone_no" name="phone_no" required>
                <span class="text-danger"><?php echo $phoneErr; ?></span>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
