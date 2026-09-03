<?php
include('db.php');
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $gender = trim($_POST['gender']);
    $college = trim($_POST['college']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = trim($_POST['password']);

    // Validate required fields
    if (empty($name) || empty($gender) || empty($college) || empty($email) || empty($mobile) || empty($password)) {
        $message = "All fields are required. Please fill in all the details.";
    }
    // Full name must contain only letters and spaces
    elseif (!preg_match('/^[A-Za-z\s]+$/', $name)) {
        $message = "Name must contain only letters and spaces.";
    }
    // College name must contain only letters and spaces
    elseif (!preg_match('/^[A-Za-z\s]+$/', $college)) {
        $message = "College name must contain only letters and spaces.";
    }
    // Email must start with a letter and be valid
    elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9._]*@[a-zA-Z]+\.[a-zA-Z]{2,}$/', $email)) {
        $message = "Enter a valid email starting with a letter (e.g., example@example.com).";
    }
    // Mobile must be exactly 10 digits
    elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $message = "Please enter a valid 10-digit mobile number.";
    }
    // Password length
    elseif (strlen($password) < 7) {
        $message = "Password must be at least 7 characters long.";
    } else {
        // Check for duplicate email
        $checkSql = "SELECT id FROM user WHERE email = ?";
        $stmt = $conn->prepare($checkSql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "This email is already registered. Please use a different email or login.";
        } else {
            $stmt->close();

            // Insert user
            $sql = "INSERT INTO user (name, gender, college, email, mobile, password)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $name, $gender, $college, $email, $mobile, $password);

            if ($stmt->execute()) {
                $message = "Registration successful. You can now <a href='login.php'>login</a>.";
            } else {
                $message = "Error: " . $conn->error;
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Online Exam System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('image/Online-Examination-Management-System.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            z-index: 0;
        }
        .register-card {
            max-width: 500px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.7);
            background-color: rgba(255, 255, 255, 0.95);
            position: relative;
            z-index: 1;
            color: #000;
            padding: 30px;
        }
        .form-control::placeholder {
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="card register-card">
    <div class="card-body">
        <h3 class="text-center mb-4">📝 User Registration</h3>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="post" novalidate>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter your name"
                       pattern="[A-Za-z\s]+" title="Name must contain only letters and spaces" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">College Name</label>
                <input type="text" name="college" class="form-control" placeholder="Enter your college name"
                       pattern="[A-Za-z\s]+" title="College name must contain only letters and spaces" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text">@</span>
                    <input type="email" name="email" class="form-control"
                           placeholder="example@example.com"
                           pattern="[a-zA-Z][a-zA-Z0-9._]*@[a-zA-Z]+\.[a-zA-Z]{2,}"
                           title="Email must start with a letter and follow standard format (e.g. example@example.com)"
                           required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <div class="input-group">
                    <span class="input-group-text">📱</span>
                    <input type="text" name="mobile" class="form-control" placeholder="10-digit number"
                           maxlength="10" pattern="\d{10}"
                           title="Enter a valid 10-digit mobile number" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text">🔒</span>
                    <input type="password" name="password" class="form-control"
                           placeholder="Choose a password"
                           minlength="7"
                           title="Password must be at least 7 characters" required>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success">Register</button>
            </div>

            <p class="mt-3 text-center">
                Already registered? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</div>

</body>
</html>
