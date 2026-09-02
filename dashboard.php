<?php
    session_start();

    if (!isset($_SESSION["user_id"]))
    {
        header('Location: login.php');
        exit;
    }

    $id = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];
    $email = $_SESSION['user_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">Authentication</div>
        <a href="logout.php">Log out</a>
    </div>

    <div class="page-content">
        <h1>Welcome, <?= htmlspecialchars($userName) ?></h1>

        <div class="info-panel">
            <div class="info-row">
                <span class="label">Id</span>
                <span class="value"><?= htmlspecialchars($id) ?></span>
            </div>

            <div class="info-row">
                <span class="label">Name</span>
                <span class="value"><?= htmlspecialchars($userName) ?></span>
            </div>

            <div class="info-row">
                <span class="label">Email</span>
                <span class="value"><?= htmlspecialchars($email) ?></span>
            </div>
        </div>
    </div>

</body>
</html>