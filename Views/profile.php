<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>My Profile</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">My Website</div>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <div class="page-content">
        <?php
            require_once(__DIR__ . '/../config/constants.php');

            $picturePath = BASE_PATH . '/uploads/profiles/'. $profile['profile_picture'];
            $pictureUrl = '/uploads/profiles/' . ($profile['profile_picture'] ?? '');
            $defaultUrl = '/assets/default-avatar.png';

            $displayPicture = (!empty($profile['profile_picture']) && file_exists($picturePath)) ? $pictureUrl : $defaultUrl;
        ?>

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

        <!-- <p><a href="profile_edit.php">Edit profile</a></p> check -->
    </div>

</body>
</html>