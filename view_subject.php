<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('db.php');

$message = '';
$messageType = '';

// Handle deletion
if (isset($_GET['delete'])) {
    $subject_id = intval($_GET['delete']);
    
    // Check if subject exists
    $checkStmt = $conn->prepare("SELECT subject_name FROM subjects WHERE subject_id = ?");
    $checkStmt->bind_param("i", $subject_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $subjectName = $checkResult->fetch_assoc()['subject_name'];
        $errorOccurred = false;
        $errorMessage = '';
        
        // Start transaction for atomicity
        $conn->begin_transaction();
        
        // Temporarily disable foreign key checks to allow deletion
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        
        try {
            // Get all result IDs for this subject first
            $getResultsStmt = $conn->prepare("SELECT id FROM results WHERE subject_id = ?");
            $getResultsStmt->bind_param("i", $subject_id);
            $getResultsStmt->execute();
            $resultsData = $getResultsStmt->get_result();
            $resultIds = [];
            while ($row = $resultsData->fetch_assoc()) {
                $resultIds[] = $row['id'];
            }
            $getResultsStmt->close();
            
            // Delete user_answers for these results (if table exists)
            if (!empty($resultIds)) {
                // Check if user_answers table exists
                $tableCheck = $conn->query("SHOW TABLES LIKE 'user_answers'");
                if ($tableCheck && $tableCheck->num_rows > 0) {
                    $placeholders = implode(',', array_fill(0, count($resultIds), '?'));
                    $deleteUserAnswers = $conn->prepare("DELETE FROM user_answers WHERE result_id IN ($placeholders)");
                    $types = str_repeat('i', count($resultIds));
                    $deleteUserAnswers->bind_param($types, ...$resultIds);
                    if (!$deleteUserAnswers->execute()) {
                        // Don't throw error if user_answers deletion fails, just log it
                        error_log("Warning: Could not delete user_answers: " . $conn->error);
                    }
                    $deleteUserAnswers->close();
                }
            }
            
            // Delete results for this subject
            $deleteResults = $conn->prepare("DELETE FROM results WHERE subject_id = ?");
            $deleteResults->bind_param("i", $subject_id);
            if (!$deleteResults->execute()) {
                throw new Exception("Error deleting results: " . $conn->error);
            }
            $deleteResults->close();
            
            // Delete feedback for this subject
            $deleteFeedback = $conn->prepare("DELETE FROM feedback WHERE subject_id = ?");
            $deleteFeedback->bind_param("i", $subject_id);
            if (!$deleteFeedback->execute()) {
                throw new Exception("Error deleting feedback: " . $conn->error);
            }
            $deleteFeedback->close();
            
            // Delete the subject (questions will cascade delete automatically)
            $deleteSubject = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
            $deleteSubject->bind_param("i", $subject_id);
            if (!$deleteSubject->execute()) {
                throw new Exception("Error deleting subject: " . $conn->error);
            }
            $deleteSubject->close();
            
            // Re-enable foreign key checks
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
            
            // Commit transaction
            $conn->commit();
            $checkStmt->close();
            
            // Redirect to avoid resubmission
            header("Location: view_subject.php?deleted=1");
            exit;
            
        } catch (Exception $e) {
            // Re-enable foreign key checks even on error
            $conn->query("SET FOREIGN_KEY_CHECKS = 1");
            
            // Rollback on error
            $conn->rollback();
            $checkStmt->close();
            $errorMessage = $e->getMessage();
            header("Location: view_subject.php?error=" . urlencode($errorMessage));
            exit;
        }
    } else {
        $message = "Subject not found.";
        $messageType = 'danger';
        $checkStmt->close();
    }
}

$query = "SELECT subject_id, subject_code, subject_name FROM subjects ORDER BY subject_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Subjects - Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
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
            padding-top: 50px;
            padding-bottom: 60px;
        }
        h2 {
            font-weight: 700;
            margin-bottom: 30px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.7);
            text-align: center;
        }
        .table-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            color: #000; 
        }
        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
        .btn-back {
            margin-top: 20px;
            display: inline-block;
            background-color: rgba(13, 110, 253, 0.85);
            color: #fff;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background-color: #fff;
            color: rgba(13, 110, 253, 0.85);
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }
        .btn-delete:hover {
            background-color: #c82333;
            color: white;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .alert {
            position: relative;
            z-index: 10;
            margin-bottom: 20px;
        }
    </style>
    <script>
        function confirmDelete(subjectName, subjectId) {
            return confirm("Are you sure you want to delete '" + subjectName + "'?\n\nThis will also delete all related questions, results, and feedback for this subject. This action cannot be undone!");
        }
    </script>
</head>
<body>
<div class="container">
    <h2>📚 Subjects List</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> Subject deleted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($message && $messageType): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <strong><?= $messageType === 'success' ? '<i class="bi bi-check-circle-fill"></i> Success!' : '<i class="bi bi-exclamation-triangle-fill"></i> Error!' ?></strong><br>
            <?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill"></i> Error!</strong><br>
            <?= htmlspecialchars(urldecode($_GET['error'])) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table class="table table-bordered table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Subject ID</th>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Actions</th> 
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['subject_id']; ?></td>
                            <td><?= $row['subject_code']; ?></td>
                            <td><?= $row['subject_name']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="view_questions.php?subject_id=<?= $row['subject_id']; ?>" 
                                       class="btn btn-primary btn-sm">
                                       <i class="bi bi-eye"></i> View Questions
                                    </a>
                                    <a href="view_subject.php?delete=<?= $row['subject_id']; ?>" 
                                       class="btn-delete btn-sm"
                                       onclick="return confirmDelete('<?= htmlspecialchars($row['subject_name'], ENT_QUOTES); ?>', <?= $row['subject_id']; ?>)">
                                       <i class="bi bi-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No subjects found.</td> 
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <a href="admin.php" class="btn-back">⬅ Back to Dashboard</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
