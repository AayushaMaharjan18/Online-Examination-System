<?php
session_start();

// Protect page - only allow logged in users with role 'user'
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

include('db.php'); // Include your DB connection here

$userEmail = $_SESSION['email'];
$name = 'User'; // default fallback

// Prepare statement to fetch user's name
$sql = "SELECT name FROM user WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($fetchedName);

if ($stmt->fetch()) {
    $name = $fetchedName;
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard - Online Exam System</title>
    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }
        .main-content {
            position: relative;
            z-index: 1;
            padding: 40px 20px;
            max-width: 1000px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
            color: #fff;
        }
        .header h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .welcome-name {
            color: #fff;
            font-weight: 500;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 40px;
        }
        .dashboard-item {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            padding: 30px 25px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: block;
        }
        .dashboard-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            color: #333;
            text-decoration: none;
        }
        .dashboard-item i {
            font-size: 3rem;
            margin-bottom: 15px;
            display: block;
        }
        .dashboard-item h3 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .dashboard-item p {
            font-size: 0.95rem;
            color: #666;
            margin: 0;
        }
        .dashboard-item.exam i { color: #3498db; }
        .dashboard-item.results i { color: #2ecc71; }
        .dashboard-item.profile i { color: #f39c12; }
        .dashboard-item.feedback i { color: #1abc9c; }
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
        }
        .logout-btn a {
            background-color: #e74c3c;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .logout-btn a:hover {
            background-color: #c0392b;
            color: #fff;
            text-decoration: none;
        }
        .alert {
            position: relative;
            z-index: 10;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            .logout-btn {
                top: 10px;
                right: 10px;
            }
            .logout-btn a {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

<?php if (isset($_SESSION['exam_message'])): ?>
    <div class="alert alert-success text-center" role="alert" style="position: relative; z-index: 10; margin: 20px auto; max-width: 600px;">
        <?= $_SESSION['exam_message'] ?>
    </div>
    <?php unset($_SESSION['exam_message']); ?>
<?php endif; ?>

<div class="logout-btn">
    <a href="logout.php" onclick="return confirmLogout()">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>

<div class="main-content">
    <div class="header">
        <h1><i class="bi bi-person-circle"></i> Welcome, <span class="welcome-name"><?= htmlspecialchars($name) ?></span></h1>
        <p>Manage your exams and track your progress</p>
    </div>

    <div class="dashboard-grid">
        <a href="select_subject.php" class="dashboard-item exam">
            <i class="bi bi-journal-text"></i>
            <h3>Select Subject</h3>
            <p>Choose a subject to start your exam</p>
        </a>

        <a href="user_result.php" class="dashboard-item results">
            <i class="bi bi-trophy-fill"></i>
            <h3>View Results</h3>
            <p>Check your exam scores</p>
        </a>

        <a href="edit_profile.php" class="dashboard-item profile">
            <i class="bi bi-person-gear"></i>
            <h3>Edit Profile</h3>
            <p>Update your information</p>
        </a>

        <a href="user_feedback.php" class="dashboard-item feedback">
            <i class="bi bi-chat-left-text-fill"></i>
            <h3>Give Feedback</h3>
            <p>Share your thoughts</p>
        </a>
    </div>
</div>

</body>
</html>
