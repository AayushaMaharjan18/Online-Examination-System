<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include('db.php');

$error = '';

// Handle deletion
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $admin_email = $_SESSION['email'];

    // Prevent self-deletion
    $check = $conn->prepare("SELECT email FROM user WHERE id = ?");
    $check->bind_param("i", $delete_id);
    $check->execute();
    $check_result = $check->get_result()->fetch_assoc();
    if ($check_result && $check_result['email'] != $admin_email) {
        $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        header("Location: manage_users.php?deleted=1");
        exit;
    }
}

// Load user data for editing
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT id, name, gender, college, email, mobile FROM user WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_user = $result->fetch_assoc();
    $stmt->close();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user_id'])) {
    $update_id = intval($_POST['update_user_id']);
    $name = trim($_POST['name'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $college = trim($_POST['college'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');

    if (empty($name) || empty($gender) || empty($college) || empty($mobile)) {
        $error = "Please fill in all fields.";
    } elseif (!preg_match('/^\d{10,15}$/', $mobile)) {
        $error = "Enter a valid mobile number (digits only).";
    } else {
        $stmt = $conn->prepare("UPDATE user SET name = ?, gender = ?, college = ?, mobile = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $gender, $college, $mobile, $update_id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: manage_users.php?updated=1");
            exit;
        }
        $error = "Failed to update user: " . $stmt->error;
        $stmt->close();
    }

    // Reload edited user data if validation/update fails
    $stmt = $conn->prepare("SELECT id, name, gender, college, email, mobile FROM user WHERE id = ?");
    $stmt->bind_param("i", $update_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_user = $result->fetch_assoc();
    $stmt->close();
}

$users = $conn->query("SELECT id, name, gender, college, email, mobile FROM user");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            position: relative;
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
            padding: 60px 30px;
        }
        h2 {
            font-weight: bold;
            text-shadow: 0 2px 8px rgba(0,0,0,0.7);
        }
        table {
            background-color: #fff;
            color: #000;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.4);
        }
        th, td {
            padding: 12px 15px;
        }
        th {
            background-color: #343a40;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .btn-delete:hover {
            background-color: #a71d2a;
        }
        .btn-edit {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 6px;
            display: inline-block;
        }
        .btn-edit:hover {
            background-color: #0a58ca;
            color: white;
            text-decoration: none;
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
      
    </style>
</head>
<body>

<div style="position: absolute; top: 20px; right: 20px; z-index: 10;">
    <a href="admin.php" class="btn" style="background-color: #ffc107; color: #000; font-weight: 600;">
        ← Admin Dashboard
    </a>
</div>

<div class="container">
    <h2>👥 Manage Users</h2>

    <?php if (isset($_GET['deleted'])): ?>
        <div class="alert alert-success">User deleted successfully.</div>
    <?php endif; ?>
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">User updated successfully.</div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($edit_user): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title text-dark">Edit User #<?= (int)$edit_user['id'] ?></h5>
                <form method="POST" action="">
                    <input type="hidden" name="update_user_id" value="<?= (int)$edit_user['id'] ?>">

                    <div class="mb-3">
                        <label for="name" class="form-label text-dark">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($edit_user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-dark">Gender</label><br>
                        <div class="form-check form-check-inline text-dark">
                            <input class="form-check-input" type="radio" name="gender" id="gender_male" value="Male" <?= ($edit_user['gender'] === 'Male') ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="gender_male">Male</label>
                        </div>
                        <div class="form-check form-check-inline text-dark">
                            <input class="form-check-input" type="radio" name="gender" id="gender_female" value="Female" <?= ($edit_user['gender'] === 'Female') ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="gender_female">Female</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="college" class="form-label text-dark">College</label>
                        <input type="text" class="form-control" id="college" name="college" value="<?= htmlspecialchars($edit_user['college']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-dark">Email</label>
                        <input type="email" class="form-control" id="email" value="<?= htmlspecialchars($edit_user['email']) ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="mobile" class="form-label text-dark">Mobile</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" value="<?= htmlspecialchars($edit_user['mobile']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>College</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['gender']) ?></td>
                        <td><?= htmlspecialchars($user['college']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['mobile']) ?></td>
                        <td>
                            <a href="manage_users.php?edit=<?= $user['id'] ?>" class="btn-edit">Edit</a>
                            <?php if ($user['email'] != $_SESSION['email']): ?>
                                <a href="manage_users.php?delete=<?= $user['id'] ?>" 
                                   class="btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            <?php else: ?>
                                <span class="text-muted">Self</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
