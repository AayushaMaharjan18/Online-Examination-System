<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('db.php');

// Fetch subjects for dropdown
$subject_query = "SELECT subject_id, subject_name FROM subjects ORDER BY subject_name";
$subjects = $conn->query($subject_query);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_id = $_POST['subject_id'] ?? '';
    $question = trim($_POST['question'] ?? '');
    $options = $_POST['options'] ?? [];
    $status = 1; // use integer for DB consistency
    $correct_option = $_POST['correct_option'] ?? '';

    // Validation
    if (empty($subject_id) || empty($question) || $correct_option === '' || count($options) < 4) {
        $error = "All fields are required.";
    } else {
        // Insert question
        $stmt = $conn->prepare("INSERT INTO questions (subject_id, question, status) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $subject_id, $question, $status);
        if ($stmt->execute()) {
            $qid = $conn->insert_id;
            $success = "Question added successfully!";
        } else {
            $error = "Failed to add question.";
        }

        // Insert options
        if (!empty($qid)) {
            for ($i = 0; $i < count($options); $i++) {
                $value = trim($options[$i]);
                if ($value !== '') {
                    $stmt1 = $conn->prepare("INSERT INTO options (question_id, value, is_correct) VALUES (?, ?, ?)");
                    $is_correct = ($correct_option == $i) ? 1 : 0;
                    $stmt1->bind_param("isi", $qid, $value, $is_correct);
                    if (!$stmt1->execute()) {
                        $error = "Failed to add option.";
                    }
                    $stmt1->close();
                }
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Questions - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            max-width: 700px;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            margin-top: 60px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.7);
        }
        label {
            font-weight: 600;
            color: #fff;
        }
        .form-control {
            background-color: rgba(255,255,255,0.9);
            border: none;
            border-radius: 6px;
            color: #000;
        }
        .btn-primary {
            background-color: rgba(13, 110, 253, 0.9);
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: rgba(13, 110, 253, 1);
        }
        .alert {
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Question</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="subject_id" class="form-label">Select Subject</label>
            <select class="form-control" name="subject_id" id="subject_id" required>
                <option value="">-- Choose Subject --</option>
                <?php while ($row = $subjects->fetch_assoc()): ?>
                    <option value="<?= $row['subject_id'] ?>"><?= htmlspecialchars($row['subject_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Question</label>
            <textarea name="question" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Option 1</label>
            <input type="text" name="options[]" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Option 2</label>
            <input type="text" name="options[]" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Option 3</label>
            <input type="text" name="options[]" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Option 4</label>
            <input type="text" name="options[]" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Correct Option</label>
            <select name="correct_option" class="form-control" required>
                <option value="">-- Choose Correct Option --</option>
                <option value="0">Option 1</option>
                <option value="1">Option 2</option>
                <option value="2">Option 3</option>
                <option value="3">Option 4</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Add Question</button>
    </form>
</div>

<div style="position: absolute; top: 20px; right: 20px; z-index: 10;">
    <a href="admin.php" class="btn" style="background-color: #ffc107; color: #000; font-weight: 600;">
        ← Admin Dashboard
    </a>
</div>

</body>
</html>
