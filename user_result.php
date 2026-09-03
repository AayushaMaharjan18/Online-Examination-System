<?php
session_start();
include('db.php');

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$userEmail = $_SESSION['email'];

// Get user ID
$stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$stmt->bind_result($userId);
$stmt->fetch();
$stmt->close();

// Fetch all results for this user
$sql = "SELECT r.id AS result_id, r.subject_id, s.subject_name, r.score, r.taken_at
        FROM results r
        JOIN subjects s ON r.subject_id = s.subject_id
        WHERE r.user_id = ?
        ORDER BY r.taken_at DESC";

$results = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $results = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Your Exam Results</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
    body {
        background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
        background-size: cover;
        font-family: Arial, sans-serif;
        padding: 30px;
    }
    .container {
        max-width: 900px;
        margin: 0 auto;
        position: relative;
        background: rgba(255,255,255,0.95);
        padding: 25px;
        border-radius: 12px;
    }
    .top-right {
        position: absolute;
        top: 15px;
        right: 15px;
    }
    table {
        width: 100%;
    }
    th, td {
        text-align: center;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
</style>
</head>
<body>
<div class="container">
    <div class="top-right">
        <a href="dash.php" class="btn btn-warning fw-bold">← Back to Dashboard</a>
    </div>
    <h2 class="mb-4 text-center text-primary">Your Exam Results</h2>

    <?php if (count($results) === 0): ?>
        <p class="text-center">No exam results found.</p>
    <?php else: ?>
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Result ID</th>
                    <th>Subject</th>
                    <th>Marks Obtained</th>
                    <th>Remarks</th>
                    <th>Date Taken</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result): 
                    $remarks = ($result['score'] >= 5) ? "Pass" : "Fail";
                ?>
                <tr>
                    <td><?= htmlspecialchars($result['result_id']) ?></td>
                    <td><?= htmlspecialchars($result['subject_name']) ?></td>
                    <td><?= htmlspecialchars($result['score']) ?>/10</td>
                    <td><?= $remarks ?></td>
                    <td><?= htmlspecialchars($result['taken_at']) ?></td>
                    <td>
                        <form action="view_answers.php" method="get" style="margin:0;">
                            <input type="hidden" name="result_id" value="<?= $result['result_id'] ?>">
                            <button type="submit" class="btn btn-info btn-sm">Show</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
