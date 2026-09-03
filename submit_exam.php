<?php
session_start();
include("db.php");

// Check if the exam was submitted properly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers']) && !empty($_POST['answers'])) {
    $answers = $_POST['answers']; // answers[question_id] = option_id
    $score = 0;

    // Validate user session
    $user_id = $_SESSION['user_id'] ?? 0;
    $subject_id = $_SESSION['subject_id'] ?? 1;

    if ($user_id <= 0) {
        die("Invalid user session!");
    }

    // Prepare statement to check answers
    $stmt = $conn->prepare("SELECT is_correct FROM options WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Calculate score
    foreach ($answers as $question_id => $option_id) {
        if (!is_numeric($option_id)) continue;

        $stmt->bind_param("i", $option_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if ($row['is_correct'] == 1) {
                $score++;
            }
        }
    }
    $stmt->close();

    // Fetch user name
    $user_name = '';
    $stmt = $conn->prepare("SELECT name FROM user WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $user_name = $res->fetch_assoc()['name'];
    }
    $stmt->close();

    // Fetch subject name
    $subject_name = '';
    $stmt = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $subject_name = $res->fetch_assoc()['subject_name'];
    }
    $stmt->close();

    // Save final result in results table
    $insert = $conn->prepare("INSERT INTO results (user_id, user_name, subject_id, subject_name, score, taken_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $insert->bind_param("isisi", $user_id, $user_name, $subject_id, $subject_name, $score);
    $insert->execute();
    $result_id = $insert->insert_id; // get the result ID for user_answers
    $insert->close();

    // Save each answer in user_answers table
    $insert_answer = $conn->prepare("INSERT INTO user_answers (user_id, result_id, question_id, selected_option_id) VALUES (?, ?, ?, ?)");
    foreach ($answers as $question_id => $option_id) {
        if (!is_numeric($option_id)) continue;
        $insert_answer->bind_param("iiii", $user_id, $result_id, $question_id, $option_id);
        $insert_answer->execute();
    }
    $insert_answer->close();

    // Set success message and redirect
    $_SESSION['exam_message'] = "You have submitted your exam. Please view your result.";
    header("Location: user_result.php");
    exit;

} else {
    // Redirect back if accessed incorrectly
    header("Location: exam.php");
    exit();
}
?>
