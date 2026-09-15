<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>My Profile</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">
            <a href="../index.php">Main</a>
        </div>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <div class="page-content">
        <h1>My Profile</h1>
        
        <img src="<?= htmlspecialchars($displayPicture) ?>"
         alt="Profile picture"
         style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:1px solid var(--border);margin-bottom:1.5rem;">

        <div class="info-panel">
            <div class="info-row">
                <span class="label">First Name: </span>
                <span class="value"><?= htmlspecialchars($profile['first_name'] ?? '—') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Last Name: </span>
                <span class="value"><?= htmlspecialchars($profile['last_name'] ?? '—') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Phone: </span>
                <span class="value"><?= htmlspecialchars($profile['phone'] ?? '—') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Location: </span>
                <span class="value"><?= htmlspecialchars($profile['location'] ?? '—') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Date of Birth: </span>
                <span class="value"><?= htmlspecialchars($profile['date_of_birth'] ?? '—') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Bio: </span>
                <span class="value"><?= htmlspecialchars($profile['bio'] ?? '—') ?></span>
            </div>
        </div>

        <div class="footer-link" style="text-align:left; margin-top:1.3rem;">
            <a href="profile-edit.php">Edit Profile</a>
        </div>
    </div>

</body>
</html>