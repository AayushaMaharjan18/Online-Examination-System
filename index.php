<?php
session_start();

// Redirect logged-in users
if (isset($_SESSION['email']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
        exit;
    } elseif ($_SESSION['role'] === 'user') {
        header("Location: dash.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url("image/Online-Examination-Management-System.png") no-repeat center center fixed;
        background-size: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
    }
    .container {
        max-width: 450px;
        padding: 40px 30px;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
    }
    .btn-custom {
        width: 100%;
        padding: 12px;
        margin-top: 15px;
        border-radius: 30px;
    }
</style>

</head>
<body>

<div class="container">
    <h1>Welcome to Online Examination System</h1>
    <p>Take quizzes, test your knowledge, and track your performance.</p>

    <!-- Go to Login Page -->
    <a href="login.php" class="btn btn-success btn-custom">Admin / User Login</a>

    <!-- Go to Register Page -->
    <a href="register.php" class="btn btn-info btn-custom">Create User Account</a>
</div>

</body>
</html>
