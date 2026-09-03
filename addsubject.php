<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('db.php');

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subjectName = trim($_POST['subject_name'] ?? '');
    $subjectCode = trim($_POST['subject_code'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($subjectName) || empty($subjectCode)) {
        $error = "Subject name and code are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name, subject_code, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $subjectName, $subjectCode, $description);

        if ($stmt->execute()) {
            header("Location: admin.php?subject_added=1");
            exit;
        } else {
            $error = "Database error: Could not add subject.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Subject - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            color: #fff;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }
        .container {
            max-width: 500px;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
            margin-top: 80px;
        }
        h2 {
            margin-bottom: 30px;
            text-align: center;
            font-weight: 700;
            text-shadow: 0 2px 8px rgba(0,0,0,0.7);
        }
        label {
            font-weight: 600;
            text-shadow: 0 1px 4px rgba(0,0,0,0.6);
        }
        .form-control {
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 6px;
            color: #000;
            font-weight: 500;
        }
        .btn-primary {
            background-color: rgba(13, 110, 253, 0.9);
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: rgba(13, 110, 253, 1);
        }
        .btn-secondary {
            background-color: rgba(108, 117, 125, 0.8);
            border: none;
            font-weight: 600;
        }
        .btn-secondary:hover {
            background-color: rgba(108, 117, 125, 1);
        }
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.9);
            border: none;
            color: white;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        

    </style>
</head>
<body>

<div style="position: absolute; top: 20px; right: 20px; z-index: 10;">
    <a href="admin.php" class="btn" style="background-color: #ffc107; color: #000; font-weight: 600;">
        ← Admin Dashboard
    </a>
</div>

<div class="container">
    <h2>Add New Subject</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="subject_name" class="form-label">Subject Name</label>
            <input type="text" class="form-control" id="subject_name" name="subject_name" required>
        </div>
        <div class="mb-3">
            <label for="subject_code" class="form-label">Subject Code</label>
            <input type="text" class="form-control" id="subject_code" name="subject_code" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description (optional)</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Subject</button>
        <a href="admin.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
    </form>
</div>

</body>
</html>
