<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Edit Profile</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <a href="../index.php">Main</a>
        <a href="profile.php">Back to Profile</a>
    </div>

    <div class="page-content">
        <h1>Edit Profile</h1>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="profile-edit.php" enctype="multipart/form-data">
            <?= Csrf::field() ?>
            <div class="field">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name"
                       value="<?= htmlspecialchars($_POST['first_name'] ?? $profile['first_name'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name"
                       value="<?= htmlspecialchars($_POST['last_name'] ?? $profile['last_name'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone"
                       value="<?= htmlspecialchars($_POST['phone'] ?? $profile['phone'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="location">Location</label>
                <input type="text" id="location" name="location"
                       value="<?= htmlspecialchars($_POST['location'] ?? $profile['location'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" id="date_of_birth" name="date_of_birth"
                       value="<?= htmlspecialchars($_POST['date_of_birth'] ?? $profile['date_of_birth'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="bio">Bio</label>
                <input type="text" id="bio" name="bio"
                       value="<?= htmlspecialchars($_POST['bio'] ?? $profile['bio'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/webp">
            </div>

            <?php if (!empty($profile['profile_picture'])): ?>
                <div class="field">
                    <label>
                        Remove current picture <input type="checkbox" name="remove_picture" value="1">
                    </label>
                </div>
            <?php endif; ?>

            <button type="submit">Save Changes</button>
        </form>
    </div>

</body>
</html>