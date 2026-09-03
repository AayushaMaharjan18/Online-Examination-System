<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

include('db.php');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    die("User not found.");
}

// Initialize variables
$name = $gender = $college = $email = $mobile = "";
$success = $error = "";

// Fetch current user data
$stmt = $conn->prepare("SELECT name, gender, college, email, mobile FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $gender, $college, $email, $mobile);
$stmt->fetch();
$stmt->close();

// Handle form submission to update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $gender = $_POST['gender'] ?? '';
    $college = trim($_POST['college']);
    $mobile = trim($_POST['mobile']);

    // Basic validation
    if (empty($name) || empty($gender) || empty($college) || empty($mobile)) {
        $error = "Please fill in all fields.";
    } elseif (!preg_match('/^\d{10,15}$/', $mobile)) {
        $error = "Enter a valid mobile number (digits only).";
    } else {
        // Update query
        $stmt = $conn->prepare("UPDATE user SET name = ?, gender = ?, college = ?, mobile = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $gender, $college, $mobile, $user_id);
        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
        } else {
            $error = "Failed to update profile: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Profile - Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            font-family: Arial, sans-serif;
            padding: 40px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 0 auto;
        }
        h2 {
            margin-bottom: 25px;
            font-weight: 700;
            text-align: center;
        }
        label {
            font-weight: 600;
        }
        .btn-primary {
            width: 100%;
        }
        .alert {
            margin-top: 15px;
        }
        
        .back-to-dashboard {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background-color: yellow;
            color: black;
            border: none;
            padding: 8px 16px;
            font-weight: 600;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .back-to-dashboard:hover {
            background-color: #ffeb3b;
            color: black;
            text-decoration: none;
        }
    </style>
</head>
<body>


<a href="dash.php" class="back-to-dashboard">← Back to Dashboard</a>

<div class="container">
    <h2>Edit Your Profile</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input 
                type="text" 
                class="form-control" 
                id="name" 
                name="name" 
                value="<?= htmlspecialchars($name) ?>" 
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Gender</label><br>
            <div class="form-check form-check-inline">
                <input 
                    class="form-check-input" 
                    type="radio" 
                    name="gender" 
                    id="gender_male" 
                    value="Male" 
                    <?= ($gender == 'Male') ? 'checked' : '' ?>
                    required
                >
                <label class="form-check-label" for="gender_male">Male</label>
            </div>
            <div class="form-check form-check-inline">
                <input 
                    class="form-check-input" 
                    type="radio" 
                    name="gender" 
                    id="gender_female" 
                    value="Female" 
                    <?= ($gender == 'Female') ? 'checked' : '' ?>
                    required
                >
                <label class="form-check-label" for="gender_female">Female</label>
            </div>
        </div>

        <div class="mb-3">
            <label for="college" class="form-label">College</label>
            <input 
                type="text" 
                class="form-control" 
                id="college" 
                name="college" 
                value="<?= htmlspecialchars($college) ?>" 
                required
            >
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email (cannot be changed)</label>
            <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="email" 
                value="<?= htmlspecialchars($email) ?>" 
                readonly
            >
        </div>

        <div class="mb-3">
            <label for="mobile" class="form-label">Mobile Number</label>
            <input 
                type="text" 
                class="form-control" 
                id="mobile" 
                name="mobile" 
                value="<?= htmlspecialchars($mobile) ?>" 
                required
            >
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

</body>
</html>
