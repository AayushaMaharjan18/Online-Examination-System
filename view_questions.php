<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('db.php');

if (!isset($_GET['subject_id'])) {
    echo "<script>alert('Subject ID missing'); window.location.href='view_subjects.php';</script>";
    exit();
}

$subject_id = intval($_GET['subject_id']);

// Get subject name
$stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$stmt->bind_result($subjectName);
if (!$stmt->fetch()) {
    echo "<script>alert('Subject not found'); window.location.href='view_subjects.php';</script>";
    exit();
}
$stmt->close();

// Fetch questions
$stmtQ = $conn->prepare("SELECT id, question FROM questions WHERE subject_id = ?");
$stmtQ->bind_param("i", $subject_id);
$stmtQ->execute();
$resultQ = $stmtQ->get_result();

$questions = [];
while ($row = $resultQ->fetch_assoc()) {
    $questions[] = $row;
}
$stmtQ->close();

// Fetch options for questions
$questionIds = array_column($questions, 'id');
$options = [];
if ($questionIds) {
    $placeholders = implode(',', array_fill(0, count($questionIds), '?'));
    $types = str_repeat('i', count($questionIds));
    $sqlOpts = "SELECT question_id, value, is_correct FROM options WHERE question_id IN ($placeholders) ORDER BY question_id";
    $stmtOpts = $conn->prepare($sqlOpts);
    $stmtOpts->bind_param($types, ...$questionIds);
    $stmtOpts->execute();
    $resultOpts = $stmtOpts->get_result();
    while ($opt = $resultOpts->fetch_assoc()) {
        $options[$opt['question_id']][] = $opt;
    }
    $stmtOpts->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Questions for <?= htmlspecialchars($subjectName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            background: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 25px;
        }
        .question {
            margin-bottom: 30px;
            position: relative;
        }
        .option.correct {
            color: green;
            font-weight: bold;
        }
        .option.incorrect {
            color: #333;
        }
        a.btn-back {
            margin-top: 20px;
            display: inline-block;
        }
        .edit-btn {
            position: absolute;
            right: 0;
            top: 0;
            font-size: 0.85rem;
            color:blue;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Questions for Subject: <?= htmlspecialchars($subjectName) ?></h2>

    <?php if (empty($questions)): ?>
        <p>No questions found for this subject.</p>
    <?php else: ?>
        <?php foreach ($questions as $index => $q): ?>
            <div class="question">
                <h5>
                    Q<?= $index + 1 ?>. <?= htmlspecialchars($q['question']) ?>
                    <a href="edit_question.php?question_id=<?= $q['id'] ?>" class="btn btn-sm btn-warning edit-btn">Edit</a>
                </h5>
                <?php if (isset($options[$q['id']])): ?>
                    <ul>
                        <?php foreach ($options[$q['id']] as $opt): ?>
                            <li class="option <?= $opt['is_correct'] ? 'correct' : 'incorrect' ?>">
                                <?= htmlspecialchars($opt['value']) ?>
                                <?= $opt['is_correct'] ? '✔️' : '' ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p><em>No options found for this question.</em></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="view_subject.php" class="btn btn-primary btn-back">⬅ Back to Subjects</a>
</div>
</body>
</html>
