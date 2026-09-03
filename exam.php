<?php
session_start();
include('db.php');

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}  

if (!isset($_GET['subject_id'])) {
    header("Location: select_subject.php");
    exit();
}

$subject_id = intval($_GET['subject_id']);
$userEmail = $_SESSION['email'];

// Fetch user id
$userSql = "SELECT id FROM user WHERE email = ?";
$stmtUser = $conn->prepare($userSql);
$stmtUser->bind_param("s", $userEmail);
$stmtUser->execute();
$stmtUser->bind_result($userId);
$stmtUser->fetch();
$stmtUser->close();

// Fetch subject name
$subjectName = "Selected Subject";
$stmtSub = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
$stmtSub->bind_param("i", $subject_id);
$stmtSub->execute();
$stmtSub->bind_result($subjectNameFetched);
if ($stmtSub->fetch()) {
    $subjectName = $subjectNameFetched;
}
$stmtSub->close();

/**
 * ================================
 * Randomization & Session Storage
 * ================================
 */
if (!isset($_SESSION['exam_data'][$subject_id])) {
    // Fetch 10 random questions for this subject
    $questionsSql = "SELECT * FROM questions WHERE subject_id = ? ORDER BY RAND() LIMIT 10";
    $stmtQ = $conn->prepare($questionsSql);
    $stmtQ->bind_param("i", $subject_id);
    $stmtQ->execute();
    $resultQ = $stmtQ->get_result();

    $questions = [];
    while ($q = $resultQ->fetch_assoc()) {
        $questions[] = $q;
    }
    $stmtQ->close();

    if (empty($questions)) {
        $msg = "No questions found for this subject. Please choose another subject.";
        echo "<script>
                alert('$msg');
                window.location.href = 'select_subject.php';
              </script>";
        exit();
    }

    // Fetch options for all questions
    $questionIds = array_column($questions, 'id');
    $placeholders = implode(',', array_fill(0, count($questionIds), '?'));
    $types = str_repeat('i', count($questionIds));

    $options = [];
    if ($questionIds) {
        $sqlOpts = "SELECT * FROM options WHERE question_id IN ($placeholders)";
        $stmtOpts = $conn->prepare($sqlOpts);
        $stmtOpts->bind_param($types, ...$questionIds);
        $stmtOpts->execute();
        $resultOpts = $stmtOpts->get_result();
        while ($opt = $resultOpts->fetch_assoc()) {
            $options[$opt['question_id']][] = $opt;
        }
        $stmtOpts->close();
    }

    // Shuffle questions using Fisher-Yates Algorithm
    $lengthQ = count($questions);
    for ($i = $lengthQ - 1; $i > 0; $i--) {
        $j = rand(0, $i);
        $temp = $questions[$i];
        $questions[$i] = $questions[$j];
        $questions[$j] = $temp;
    }   

    // Shuffle options for each question using Fisher-Yates
    foreach ($options as $qid => $optArray) {
        $lengthO = count($optArray);
        for ($i = $lengthO - 1; $i > 0; $i--) {
            $j = rand(0, $i);
            $temp = $options[$qid][$i];
            $options[$qid][$i] = $options[$qid][$j];
            $options[$qid][$j] = $temp;
        }
    }
    unset($optArray);

    // Store randomized data in session
    $_SESSION['exam_data'][$subject_id] = [
        'questions' => $questions,
        'options'   => $options
    ];
} else {
    // Load from session if already randomized
    $questions = $_SESSION['exam_data'][$subject_id]['questions'];
    $options   = $_SESSION['exam_data'][$subject_id]['options'];
}

/**
 * ================================
 * Handle Submission
 * ================================
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;

    // Insert result row first
    $insertResult = $conn->prepare("INSERT INTO results (user_id, subject_id, score, taken_at) VALUES (?, ?, 0, NOW())");
    $insertResult->bind_param("ii", $userId, $subject_id);
    $insertResult->execute();
    $result_id = $insertResult->insert_id;
    $insertResult->close();

    foreach ($questions as $q) {
        $qid = $q['id'];
        $selectedOptionId = $_POST['answer'][$qid] ?? null;

        if ($selectedOptionId !== null) {
            $sqlCorrect = "SELECT is_correct FROM options WHERE id = ? LIMIT 1";
            $stmtC = $conn->prepare($sqlCorrect);
            $stmtC->bind_param("i", $selectedOptionId);
            $stmtC->execute();
            $stmtC->bind_result($is_correct);
            $stmtC->fetch();
            $stmtC->close();

            if ($is_correct == 1) {
                $score++;
            }

            $saveAns = $conn->prepare("INSERT INTO user_answers (user_id, result_id, question_id, selected_option_id) VALUES (?, ?, ?, ?)");
            $saveAns->bind_param("iiii", $userId, $result_id, $qid, $selectedOptionId);
            $saveAns->execute();
            $saveAns->close();
        }
    }

    $updateScore = $conn->prepare("UPDATE results SET score = ? WHERE id = ?");
    $updateScore->bind_param("ii", $score, $result_id);
    $updateScore->execute();
    $updateScore->close();

    unset($_SESSION['exam_data'][$subject_id]);

    echo "<script>
        alert('Exam submitted successfully!');
        window.location.href = 'user_result.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Exam: <?= htmlspecialchars($subjectName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
        }
        .container {
            max-width: 900px;
            margin-top: 30px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        .question {
            margin-bottom: 25px;
        }
        .options label {
            display: block;
            margin-bottom: 8px;
            cursor: pointer;
        }
        .nav-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .question:not(.active) {
            display: none;
        }
        #prevBtn, #nextBtn {
            background-color: #6f42c1;
            color: white;
            border: none;
        }
        #prevBtn:disabled {
            background-color: #c3a4f7;
            color: #eee;
        }
        #prevBtn:hover:not(:disabled),
        #nextBtn:hover {
            background-color: #4e2a99;
            color: white;
        }
        #timer {
            position: fixed;
            top: 15px;
            right: 20px;
            background: rgba(111, 66, 193, 0.85);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            user-select: none;
            z-index: 9999;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
<div id="timer">Time Left: 5:00</div>
<div class="container">
    <h2 style="display: flex; justify-content: space-between; align-items: center;">
        Exam: <?= htmlspecialchars($subjectName) ?> 
        <span id="questionCounter" style="font-size:20px; color:black;"></span>
    </h2>
    <form method="POST" id="examForm">
        <?php foreach ($questions as $index => $q): ?>
            <div class="question <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                <h5>Q<?= $index + 1 ?>. <?= htmlspecialchars($q['question']) ?></h5>
                <div class="options">
                    <?php
                    if (isset($options[$q['id']])) {
                        foreach ($options[$q['id']] as $opt):
                    ?>
                            <label>
                                <input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= $opt['id'] ?>" >
                                <?= htmlspecialchars($opt['value']) ?>
                            </label>
                    <?php
                        endforeach;
                    } else {
                        echo "<p>No options found.</p>";
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="nav-buttons">
            <button type="button" class="btn btn-secondary" id="prevBtn" disabled>Previous</button>
            <button type="button" class="btn btn-secondary" id="nextBtn">Next</button>
            <button type="submit" class="btn btn-primary" id="submitBtn" style="display:none;">Submit Exam</button>
        </div>
    </form>
</div>

<script>
    const questions = document.querySelectorAll('.question');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    let current = 0;

    function showQuestion(index) {
        questions.forEach((q, i) => {
            q.classList.toggle('active', i === index);
        });

        // Update counter display
        document.getElementById('questionCounter').textContent = `${index + 1}/${questions.length}`;

        prevBtn.disabled = index === 0;
        nextBtn.style.display = index === questions.length - 1 ? 'none' : 'inline-block';
        submitBtn.style.display = index === questions.length - 1 ? 'inline-block' : 'none';
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
        }
    });

    showQuestion(current);

    let totalSeconds = 5 * 60;
const timerDisplay = document.getElementById('timer');

function updateTimer() {
    let minutes = Math.floor(totalSeconds / 60);
    let seconds = totalSeconds % 60;
    seconds = seconds < 10 ? '0' + seconds : seconds;
    timerDisplay.textContent = `Time Left: ${minutes}:${seconds}`;

    // Show warning when exactly 1:00 left
    if (totalSeconds === 60) {
        alert("Warning: Only 1 minute left!");
    }

    // Change color to red starting from 00:59 till 00:00
    if (totalSeconds <= 59 && totalSeconds > 0) {
        timerDisplay.style.color = 'red';
        timerDisplay.style.fontWeight = 'bold';
    }

    if (totalSeconds <= 0) {
        clearInterval(timerInterval);
        alert("Time is up! The exam will be submitted automatically.");
        document.getElementById('examForm').submit();
    }
    totalSeconds--;
}

const timerInterval = setInterval(updateTimer, 1000);

</script>
</body>
</html>
