<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('db.php'); 

// Query results with JOIN to get user_name and subject_name dynamically
$sql = "SELECT 
            r.id, 
            r.user_id, 
            u.name AS user_name, 
            r.subject_id, 
            s.subject_name AS subject_name, 
            r.score, 
            r.taken_at 
        FROM results r
        JOIN user u ON r.user_id = u.id
        JOIN subjects s ON r.subject_id = s.subject_id
        ORDER BY r.score DESC, r.taken_at ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Database query failed: " . $conn->error);
}

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Exam Results - Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            z-index: -1;
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
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding-top: 80px;
            text-align: center;
        }
        h2 {
            font-weight: 700;
            margin-bottom: 40px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
        }
        table {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            color: #fff;
            width: 100%;
            margin: 0 auto;
        }
        thead th {
            border-bottom: 2px solid #fff;
            font-weight: 600;
            text-align: center;
        }
        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.25);
            cursor: pointer;
            color: #000;
        }
        tbody td {
            text-align: center;
        }
        .alert {
            margin-top: 30px;
        }
        @media (max-width: 576px) {
            .container {
                padding: 20px 10px;
            }
            table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="overlay"></div>

<div style="position: absolute; top: 20px; right: 20px; z-index: 10;">
    <a href="admin.php" class="btn" style="background-color: #ffc107; color: #000; font-weight: 600;">
        ← Admin Dashboard
    </a>
</div>

<div class="container">
    <h2>📋 Exam Results</h2>

    <?php if (count($results) > 0): ?>
        <table class="table table-borderless table-hover align-middle" border="1" style="color:black; background:white;">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>User Name</th>
                <th>Subject Name</th>
                <th>Marks Obtained</th>
                <th>Remarks</th>
                <th>Date Taken</th>
                <th>View</th>
            </tr>
        </thead>      
        <tbody>
            <?php foreach ($results as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td><?= htmlspecialchars($row['user_name']) ?></td>
                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                <td><?= htmlspecialchars($row['score']) ?>/10</td>
                <td><?= ($row['score'] > 4) ? 'Pass' : 'Fail' ?></td>
                <td><?= htmlspecialchars($row['taken_at']) ?></td>
                <td><a class="btn btn-sm btn-primary" href="view_answers.php?result_id=<?= (int) $row['id'] ?>">Details</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-light text-center">No exam results available.</div>
    <?php endif; ?>
</div>

</body>
</html>
