<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('db.php');

if (!isset($_GET['question_id'])) {
    echo "<script>alert('Question ID missing'); window.location.href='view_subjects.php';</script>";
    exit();
}

$question_id = intval($_GET['question_id']);

// Fetch question data
$stmtQ = $conn->prepare("SELECT question, subject_id FROM questions WHERE id = ?");
$stmtQ->bind_param("i", $question_id);
$stmtQ->execute();
$stmtQ->bind_result($questionText, $subject_id);
if (!$stmtQ->fetch()) {
    echo "<script>alert('Question not found'); window.location.href='view_subjects.php';</script>";
    exit();
}
$stmtQ->close();

// Fetch subject name for back link
$stmtSub = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
$stmtSub->bind_param("i", $subject_id);
$stmtSub->execute();
$stmtSub->bind_result($subjectName);
$stmtSub->fetch();
$stmtSub->close();

// Fetch options
$stmtOpt = $conn->prepare("SELECT id, value, is_correct FROM options WHERE question_id = ?");
$stmtOpt->bind_param("i", $question_id);
$stmtOpt->execute();
$resultOpt = $stmtOpt->get_result();

$options = [];
while ($row = $resultOpt->fetch_assoc()) {
    $options[] = $row;
}
$stmtOpt->close();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newQuestion = trim($_POST['question'] ?? '');
    $option_ids = $_POST['option_id'] ?? [];
    $option_values = $_POST['option_value'] ?? [];
    $correctIndex = $_POST['option_correct'] ?? null;  // single selected radio value

    if ($newQuestion === '') {
        $errors[] = "Question text cannot be empty.";
    }

    // Count valid options (existing + new)
    $validOptionsCount = 0;
    foreach ($option_values as $optValue) {
        if (trim($optValue) !== '') {
            $validOptionsCount++;
        }
    }
    $new_option_values = $_POST['new_option_value'] ?? [];
    foreach ($new_option_values as $newOptVal) {
        if (trim($newOptVal) !== '') {
            $validOptionsCount++;
        }
    }

    if ($validOptionsCount < 2) {
        $errors[] = "Please provide at least two options.";
    }

    if ($correctIndex === null) {
        $errors[] = "Please mark exactly one option as correct.";
    }

    if (empty($errors)) {
        // Update question text
        $stmtUpdQ = $conn->prepare("UPDATE questions SET question = ? WHERE id = ?");
        $stmtUpdQ->bind_param("si", $newQuestion, $question_id);
        $stmtUpdQ->execute();
        $stmtUpdQ->close();

        // Update existing options
        foreach ($option_ids as $idx => $optId) {
            $optValue = trim($option_values[$idx]);
            $isCorrect = ($correctIndex == $idx) ? 1 : 0;

            if ($optValue !== '') {
                $stmtUpdOpt = $conn->prepare("UPDATE options SET value = ?, is_correct = ? WHERE id = ?");
                $stmtUpdOpt->bind_param("sii", $optValue, $isCorrect, $optId);
                $stmtUpdOpt->execute();
                $stmtUpdOpt->close();
            }
        }

        // Handle new options (added dynamically)
        if (!empty($new_option_values)) {
            foreach ($new_option_values as $idx => $newOptVal) {
                $newOptVal = trim($newOptVal);
                if ($newOptVal !== '') {
                    $newIsCorrect = ($correctIndex === "new-$idx") ? 1 : 0;
                    $stmtInsOpt = $conn->prepare("INSERT INTO options (question_id, value, is_correct) VALUES (?, ?, ?)");
                    $stmtInsOpt->bind_param("isi", $question_id, $newOptVal, $newIsCorrect);
                    $stmtInsOpt->execute();
                    $stmtInsOpt->close();
                }
            }
        }

        $success = "Question and options updated successfully.";

        // Refresh options data after update
        $stmtOpt = $conn->prepare("SELECT id, value, is_correct FROM options WHERE question_id = ?");
        $stmtOpt->bind_param("i", $question_id);
        $stmtOpt->execute();
        $resultOpt = $stmtOpt->get_result();
        $options = [];
        while ($row = $resultOpt->fetch_assoc()) {
            $options[] = $row;
        }
        $stmtOpt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
        }
        .container {
            max-width: 700px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .btn-back {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        .option-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .option-row input[type="text"] {
            flex: 1;
            margin-right: 10px;
        }
        .option-row label {
            margin-right: 10px;
            white-space: nowrap;
        }
        .btn-remove-option {
            color: red;
            cursor: pointer;
            font-weight: bold;
        }
        .alert {
            margin-top: 20px;
        }
        .btn-add-option {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>


<a href="view_questions.php?subject_id=<?= $subject_id ?>" class="btn btn-primary btn-back">⬅ Back to Questions</a>

<div class="container">
    <h2>Edit Question for Subject: <?= htmlspecialchars($subjectName) ?></h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="question" class="form-label">Question Text</label>
            <textarea name="question" id="question" rows="3" class="form-control" required><?= htmlspecialchars($questionText) ?></textarea>
        </div>

        <h5>Options</h5>
        <div id="options-container">
            <?php foreach ($options as $idx => $opt): ?>
                <div class="option-row">
                    <input type="hidden" name="option_id[]" value="<?= $opt['id'] ?>">
                    <input type="text" name="option_value[]" value="<?= htmlspecialchars($opt['value']) ?>" class="form-control" required>
                    <label>
                        <input type="radio" name="option_correct" value="<?= $idx ?>" <?= $opt['is_correct'] ? 'checked' : '' ?>>
                        Correct
                    </label>
                    <span class="btn-remove-option" onclick="removeOption(this)" title="Remove option">&times;</span>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-sm btn-success btn-add-option" onclick="addOption()">+ Add Option</button>

        <div id="new-options-container"></div>

        <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
        <a href="view_questions.php?subject_id=<?= $subject_id ?>" class="btn btn-secondary mt-3">Cancel</a>
    </form>
</div>

<script>
function removeOption(el) {
    const container = el.parentElement;
    container.remove();
}

function addOption() {
    const container = document.getElementById('new-options-container');
    const idx = container.children.length;
    const div = document.createElement('div');
    div.classList.add('option-row');

    div.innerHTML = `
        <input type="text" name="new_option_value[]" class="form-control" required style="flex:1; margin-right:10px;" placeholder="Option text">
        <label>
            <input type="radio" name="option_correct" value="new-${idx}">
            Correct
        </label>
        <span class="btn-remove-option" onclick="removeOption(this)" title="Remove option">&times;</span>
    `;
    container.appendChild(div);
}
</script>

</body>
</html>
