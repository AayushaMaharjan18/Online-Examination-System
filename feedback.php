<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('db.php');

// Join feedback with users and subjects to get name, email, subject name
$sql = "
    SELECT 
        f.id, 
        u.name AS user_name, 
        u.email AS user_email, 
        s.subject_name, 
        f.feedback_text, 
        f.submitted_at
    FROM feedback f
    JOIN user u ON f.user_id = u.id
    JOIN subjects s ON f.subject_id = s.subject_id
    ORDER BY f.submitted_at DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Feedback - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
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
            position: relative;
            z-index: 1;
            padding: 60px 30px;
        }
        h2 {
            font-weight: 700;
            margin-bottom: 30px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.7);
            color: #fff;
        }
        table {
            background: #fff;
            color: #000;
            width: 100%;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.4);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #007BFF;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
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
        @media (max-width: 768px) {
            .container {
                padding: 30px 15px;
            }
            table {
                font-size: 0.9rem;
            }
        }
        /* Back to dashboard button */
        #back-dashboard {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
            background-color: #ffc107;
            color: #000;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        #back-dashboard:hover {
            background-color: #e0a800;
            color: #000;
        }
    </style>
</head>
<body>

<a href="admin.php" id="back-dashboard">← Admin Dashboard</a>

<div class="container">
    <h2>📬 User Feedback</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): 
                        $datetime = new DateTime($row['submitted_at']);
                        $date = $datetime->format('Y-m-d');
                        $time = $datetime->format('H:i:s');
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['user_email']) ?></td>
                            <td><?= htmlspecialchars($row['subject_name']) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['feedback_text'])) ?></td>
                            <td><?= $date ?></td>
                            <td><?= $time ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No feedback found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
