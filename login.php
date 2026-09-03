<?php
session_start();
include('db.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check Admin
    $sql_admin = "SELECT * FROM admin WHERE email = ? AND password = ?";
    $stmt_admin = $conn->prepare($sql_admin);
    $stmt_admin->bind_param("ss", $email, $password);
    $stmt_admin->execute();
    $result_admin = $stmt_admin->get_result();

    if ($result_admin->num_rows > 0) {
        $admin = $result_admin->fetch_assoc();
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin';
        $_SESSION['user_id'] = $admin['id']; // Store admin ID
        header("Location: admin.php");
        exit;
    }

    // Check User
    $sql_user = "SELECT * FROM user WHERE email = ? AND password = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("ss", $email, $password);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'user';
        $_SESSION['user_id'] = $user['id']; // Store user ID
        header("Location: dash.php");
        exit;
    }

    $error = "Invalid email or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Login - Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.55);
            z-index: 0;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            background-color: rgba(255, 255, 255, 0.95);
            position: relative;
            z-index: 1;
            padding: 30px;
        }
        .form-control::placeholder {
            font-size: 0.9rem;
        }
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h3 class="text-center mb-4">🔐 Login</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
        <div class="mb-3">
            <label class="form-label">Email address</label>
            <div class="input-group">
                <span class="input-group-text">@</span>
                <input type="email" name="email" class="form-control" placeholder="Enter email" required />
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text">🔒</span>
                <input type="password" name="password" class="form-control" placeholder="Enter password" required />
            </div>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>

        <p class="mt-3 text-center text-dark">
            New user? <a href="register.php">Register here</a>
        </p>
    </form>
</div>

</body>
</html>
