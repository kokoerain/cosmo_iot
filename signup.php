<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password || !$confirm) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $check_query = "SELECT id FROM users WHERE email = $1";
        $result = pg_query_params($conn, $check_query, array($email));

        if (!$result) {
            $error = "Database error: " . pg_last_error($conn);
        } elseif (pg_num_rows($result) > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $insert_query = "INSERT INTO users (name, email, password) VALUES ($1, $2, $3)";
            $insert_result = pg_query_params($conn, $insert_query, array($name, $email, $hashedPassword));

            if ($insert_result) {
                $success = "Signup successful! You can now <a href='login.php'>login</a>.";
            } else {
                $error = "Error during signup: " . pg_last_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .password-toggle {
            cursor: pointer;
            user-select: none;
        }
    </style>
</head>
<body class="container mt-5">

    <h2 class="mb-4">Sign Up</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="signup.php" novalidate>
        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input id="name" type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input id="email" type="email" name="email" class="form-control" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        </div>

        <div class="mb-3 position-relative">
            <label class="form-label" for="password">Password</label>
            <input id="password" type="password" name="password" class="form-control" required />
            <span class="password-toggle position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword('password')">Show</span>
        </div>

        <div class="mb-3 position-relative">
            <label class="form-label" for="confirm_password">Confirm Password</label>
            <input id="confirm_password" type="password" name="confirm_password" class="form-control" required />
            <span class="password-toggle position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword('confirm_password')">Show</span>
        </div>

        <button type="submit" class="btn btn-primary">Sign Up</button>
        <a href="login.php" class="btn btn-secondary ms-2">Back to Login</a>
    </form>

    <script>
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const toggle = input.nextElementSibling;
            if (input.type === "password") {
                input.type = "text";
                toggle.textContent = "Hide";
            } else {
                input.type = "password";
                toggle.textContent = "Show";
            }
        }
    </script>
</body>
</html>
