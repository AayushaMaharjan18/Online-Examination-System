<?php
session_start();

// Protect page - only allow logged in users with role 'user'
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

include('db.php'); 

$userEmail = $_SESSION['email'];

// Get user ID and name
$stmtUser = $conn->prepare("SELECT id, name FROM user WHERE email = ?");
$stmtUser->bind_param("s", $userEmail);
$stmtUser->execute();
$stmtUser->bind_result($userId, $userName);
$stmtUser->fetch();
$stmtUser->close();

if (!$userId) {
    die("User not found.");
}

// Handle form submission
$feedbackMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subjectId = $_POST['subject_id'] ?? '';
    $feedbackText = trim($_POST['feedback_text'] ?? '');

    if ($subjectId == '' || $feedbackText == '') {
        $feedbackMessage = '<div class="alert alert-danger">Please select a subject and enter your feedback.</div>';
    } else {
        // Insert feedback
        $stmtInsert = $conn->prepare("INSERT INTO feedback (user_id, subject_id, feedback_text) VALUES (?, ?, ?)");
        $stmtInsert->bind_param("iis", $userId, $subjectId, $feedbackText);
        if ($stmtInsert->execute()) {
            $feedbackMessage = '<div class="alert alert-success">Thank you for your feedback!</div>';
        } else {
            $feedbackMessage = '<div class="alert alert-danger">Error submitting feedback. Please try again.</div>';
        }
        $stmtInsert->close();
    }
}

// Fetch subjects for which the user has results
$sqlSubjects = "
    SELECT DISTINCT s.subject_id, s.subject_name 
    FROM results r
    JOIN subjects s ON r.subject_id = s.subject_id
    WHERE r.user_id = ?
";
$stmtSubj = $conn->prepare($sqlSubjects);
$stmtSubj->bind_param("i", $userId);
$stmtSubj->execute();
$resultSubj = $stmtSubj->get_result();
$subjects = [];
while ($row = $resultSubj->fetch_assoc()) {
    $subjects[] = $row;
}
$stmtSubj->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Submit Feedback - Online Exam System</title>
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
            background-color: rgba(0,0,0,0.7);
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            max-width: 600px;
            margin: 80px auto;
            background: rgba(0,0,0,0.6);
            padding: 30px 25px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.8);
        }
        h2 {
            margin-bottom: 25px;
            text-align: center;
            font-weight: 700;
            text-shadow: 0 2px 8px rgba(0,0,0,0.8);
        }
        label {
            font-weight: 600;
        }
        select, textarea {
            background: rgba(255,255,255,0.9);
            border: none;
            border-radius: 8px;
            padding: 10px;
            width: 100%;
            color: #000;
            font-size: 1rem;
            resize: vertical;
        }
        button {
            background-color: #0d6efd;
            color: white;
            font-weight: 600;
            border: none;
            padding: 12px 25px;
            border-radius: 30px;
            width: 100%;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0b5ed7;
        }
        .alert {
            margin-bottom: 20px;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Give Feedback for Your Subjects</h2>

    <?= $feedbackMessage ?>

    <?php if (count($subjects) > 0): ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="subject_id">Select Subject</label>
                <select name="subject_id" id="subject_id" required>
                    <option value="" disabled selected>-- Choose a subject --</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= htmlspecialchars($subject['subject_id']) ?>"><?= htmlspecialchars($subject['subject_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="feedback_text">Your Feedback</label>
                <textarea name="feedback_text" id="feedback_text" rows="6" placeholder="Write your feedback here..." required></textarea>
            </div>

            <button type="submit">Submit Feedback</button>
        </form>
    <?php else: ?>
        <p class="text-center">You have no subjects to give feedback on yet.</p>
    <?php endif; ?>

    <div class="back-link">
        <a href="dash.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
