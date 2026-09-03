<?php
session_start();
include('db.php');

if (!isset($_SESSION['email']) || !in_array($_SESSION['role'], ['user', 'admin'], true)) {
    header("Location: login.php");
    exit;
}

$isAdmin = ($_SESSION['role'] === 'admin');
$userId = null;
$userEmail = $_SESSION['email'];

if (!$isAdmin) {
    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $stmt->bind_result($userId);
    $stmt->fetch();
    $stmt->close();
}

if (!isset($_GET['result_id']) || !is_numeric($_GET['result_id'])) {
    die("Invalid result ID.");
}

$result_id = intval($_GET['result_id']);

if ($isAdmin) {
    $stmt = $conn->prepare("SELECT user_id, subject_id, score, taken_at FROM results WHERE id = ?");
    $stmt->bind_param("i", $result_id);
} else {
    $stmt = $conn->prepare("SELECT subject_id, score, taken_at FROM results WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $result_id, $userId);
}
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    die("Result not found or access denied.");
}
$resultRow = $res->fetch_assoc();
$stmt->close();

if ($isAdmin) {
    $userId = (int) $resultRow['user_id'];
}

$student_name = '';
if ($isAdmin) {
    $st = $conn->prepare("SELECT name FROM user WHERE id = ?");
    $st->bind_param("i", $userId);
    $st->execute();
    $st->bind_result($student_name);
    $st->fetch();
    $st->close();
}

$backUrl = $isAdmin ? 'results.php' : 'user_result.php';

$subject_id = $resultRow['subject_id'];

// Fetch subject name
$stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$stmt->bind_result($subject_name);
$stmt->fetch();
$stmt->close();

// Fetch all questions asked during exam with user selected option (can be NULL)
$sql = "SELECT q.id AS question_id, q.question, ua.selected_option_id
        FROM user_answers ua
        JOIN questions q ON ua.question_id = q.id
        WHERE ua.result_id = ? AND ua.user_id = ?
        ORDER BY ua.id ASC";

$questions = [];
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $result_id, $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $questions[] = $row;
    }
    $stmt->close();
}

// Fetch all options for a question
function get_options($conn, $question_id) {
    $sql = "SELECT id, value, is_correct FROM options WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $options = $res->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $options;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Exam Details - <?= htmlspecialchars($subject_name) ?></title>
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
        background: rgba(255,255,255,0.95);
        padding: 25px;
        border-radius: 12px;
    }
    .top-right {
        position: absolute;
        top: 15px;
        right: 15px;
    }
    .question-block {
        background: #ffffffc4;
        margin-top: 15px;
        border-radius: 8px;
        padding: 15px;
        text-align: left;
        display: none;
    }
    .question-block.active {
        display: block;
    }
    .correct {
        background: #d4edda;
        color: #155724;
        padding: 8px;
        border-radius: 5px;
        margin-top: 5px;
    }
    .wrong {
        background: #f8d7da;
        color: #721c24;
        padding: 8px;
        border-radius: 5px;
        margin-top: 5px;
    }
    .correct-answer {
        color: #155724;
        font-weight: bold;
        margin-top: 5px;
    }
    .nav-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }

    
    #prevBtn {
        background-color: #6f42c1; 
        color: white;
        border: none;
        min-width: 100px;
    }
    #prevBtn:disabled {
        background-color: #c3a4f7;
        color: #eee;
    }
    #prevBtn:hover:not(:disabled) {
        background-color: #4e2a99;
        color: white;
    }

    #nextBtn {
        background-color: #007bff; 
        color: white;
        border: none;
        min-width: 100px;
    }
    #nextBtn:hover {
        background-color: #0056b3;
        color: white;
    }

    #nextBtn.finish {
        background-color: #28a745; 
        color: white;
    }
    #nextBtn.finish:hover {
        background-color: #1e7e34;
        color: white;
    }

    /* Additional styles for 4 option boxes */
    .options-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-top: 10px;
    }
    .option-box {
        padding: 12px 15px;
        border-radius: 8px;
        font-weight: 500;
        cursor: default;
        border: 2px solid transparent;
        user-select: none;
    }
    .option-box.correct {
        background-color: #d4edda;
        color: #155724;
        border-color: #28a745;
    }
    .option-box.wrong {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #dc3545;
    }
    .option-box:hover {
        background-color: #eee;
    }
</style>
</head>
<body>
<div class="container">
    <div class="top-right">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="btn btn-warning fw-bold">← Back to Results</a>
    </div>
    <h2 class="text-center mb-4">Exam Details: <?= htmlspecialchars($subject_name) ?></h2>
    <?php if ($isAdmin && $student_name !== ''): ?>
        <p><strong>Student:</strong> <?= htmlspecialchars($student_name) ?></p>
    <?php endif; ?>
    <p><strong>Date Taken:</strong> <?= htmlspecialchars($resultRow['taken_at']) ?></p>
    <p><strong>Score:</strong> <?= htmlspecialchars($resultRow['score']) ?>/10</p>

    <?php if (empty($questions)): ?>
        <p>No answers found for this exam.</p>
    <?php else: ?>
        <?php foreach ($questions as $index => $q): ?>
            <div class="question-block <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                <strong>Q<?= $index + 1 ?>:</strong> <?= htmlspecialchars($q['question']) ?><br>

                <?php
                $options = get_options($conn, $q['question_id']);
                $selected = $q['selected_option_id'];
                ?>

                <?php if ($selected === null): ?>
                    <div class="wrong">You did not attempt this question.</div>
                <?php endif; ?>

                <div class="options-grid">
                <?php foreach ($options as $opt): ?>
                    <div class="option-box <?php 
                        if ($opt['is_correct']) {
                            echo 'correct';
                        } elseif ($opt['id'] == $selected) {
                            echo 'wrong';
                        }
                    ?>">
                        <?= htmlspecialchars($opt['value']) ?>
                        <?php if ($opt['id'] == $selected): ?>
                            (Your choice)
                        <?php endif; ?>
                        <?php if ($opt['is_correct']): ?>
                            (Correct)
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="nav-buttons">
            <button class="btn" id="prevBtn" disabled>Previous</button>
            <button class="btn" id="nextBtn">Next</button>
        </div>
    <?php endif; ?>
</div>

<script>
    const questions = document.querySelectorAll('.question-block');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let current = 0;

    function showQuestion(index) {
        questions.forEach((q, i) => {
            q.classList.toggle('active', i === index);
        });
        prevBtn.disabled = index === 0;

        if (index === questions.length - 1) {
            nextBtn.textContent = 'Finish';
            nextBtn.classList.add('finish');
        } else {
            nextBtn.textContent = 'Next';
            nextBtn.classList.remove('finish');
        }

        nextBtn.disabled = false;
    }

    prevBtn.addEventListener('click', () => {
        if (current > 0) {
            current--;
            showQuestion(current);
        }
    });

    nextBtn.addEventListener('click', () => {
        if (current < questions.length - 1) {
            current++;
            showQuestion(current);
        } else {
            window.location.href = '<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>';
        }
    });

    showQuestion(current);
</script>
</body>
</html>
