<?php
session_start();
include('db.php');

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userEmail = $_SESSION['email'];

// Fetch user ID for checking exam attempts 
$userSql = "SELECT id FROM user WHERE email = ?";
$stmt = $conn->prepare($userSql);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

if (!$userId) {
    // fallback redirect if no user id found
    header("Location: ../login.php");
    exit();
}

// Fetch all subjects
$subjectSql = "SELECT * FROM subjects ORDER BY subject_name";
$subjectResult = mysqli_query($conn, $subjectSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Select Subject - Online Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: rgba(0,0,0,0.6);
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            padding-top: 60px;
            padding-bottom: 60px;
            max-width: 600px;
        }
        h2 {
            font-weight: 700;
            margin-bottom: 40px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.7);
        }
        .list-group-item {
            background: linear-gradient(135deg, #4e54c8, #8f94fb);
            color: #fff;
            margin-bottom: 16px;
            border-radius: 12px;
            cursor: pointer;
            user-select: none;
            font-size: 1.3rem;
            transition: background 0.4s ease, transform 0.3s ease;
            box-shadow: 0 6px 15px rgba(78, 84, 200, 0.6);
            border: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
        }
        .list-group-item:hover {
            background: linear-gradient(135deg, #8f94fb, #4e54c8);
            color: #fff;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(78, 84, 200, 0.8);
        }
        .list-group-item.disabled, .list-group-item.disabled:hover {
            background-color: rgba(108, 117, 125, 0.7);
            cursor: not-allowed;
            color: #ddd;
            box-shadow: none;
            transform: none;
        }
        .badge {
            font-size: 1rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .badge.bg-danger {
            background-color: #e63946;
            box-shadow: 0 0 8px rgba(230, 57, 70, 0.7);
        }
        .back-btn {
            margin-top: 40px;
            display: inline-block;
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            padding: 12px 30px;
            border-radius: 30px;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(255, 126, 95, 0.6);
            transition: background 0.4s ease, transform 0.3s ease;
            user-select: none;
        }
        .back-btn:hover {
            background: linear-gradient(135deg, #feb47b, #ff7e5f);
            box-shadow: 0 10px 30px rgba(255, 126, 95, 0.9);
            transform: translateY(-5px);
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h2>Select Subject to Attend Exam</h2>
    <div class="list-group">
        <?php while($subject = mysqli_fetch_assoc($subjectResult)):
            $subjectId = $subject['subject_id'];

            // Check if user already took this exam for this subject
            $checkSql = "SELECT * FROM results WHERE user_id = ? AND subject_id = ?";
            $stmtCheck = $conn->prepare($checkSql);
            $stmtCheck->bind_param("ii", $userId, $subjectId);
            $stmtCheck->execute();
            $checkResult = $stmtCheck->get_result();
            $taken = $checkResult->num_rows > 0;
            $stmtCheck->close();
        ?>
            <a href="<?php echo $taken ? '#' : "exam.php?subject_id=$subjectId"; ?>"
               class="list-group-item <?php echo $taken ? 'disabled' : ''; ?>"
               <?php if($taken) echo 'tabindex="-1" aria-disabled="true"'; ?>>
               <?php echo htmlspecialchars($subject['subject_name']); ?>
               <?php if ($taken): ?>
                   <span class="badge bg-danger">Already Taken</span>
               <?php endif; ?>
            </a>
        <?php endwhile; ?>
    </div>
    <a href="dash.php" class="back-btn">← Back to Dashboard</a>
</div>

</body>
</html>
