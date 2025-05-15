<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user info from PostgreSQL using pg_query_params
$result = pg_query_params($conn, "SELECT name FROM users WHERE id = $1", array($userId));

if ($result && pg_num_rows($result) === 1) {
    $user = pg_fetch_assoc($result);
    $userName = $user['name'];
} else {
    $userName = "User";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="container mt-5">

    <h2>Welcome, <?= htmlspecialchars($userName) ?>!</h2>

    <p>This is your dashboard.</p>

    <a href="logout.php" class="btn btn-danger">Logout</a>

</body>
</html>
