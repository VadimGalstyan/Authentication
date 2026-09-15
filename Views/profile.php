<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title><?= $isOwnProfile ? 'My Profile' : htmlspecialchars($profile['first_name'] . '\'s Profile') ?></title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">
            <a href="../index.php">Main</a>
        </div>

        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <div class="page-content">
        <h1><?= $isOwnProfile ? 'My Profile' : htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></h1>

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

            <?php if ($isOwnProfile): // phone/DOB stay private to the profile owner ?>
                <div class="info-row">
                    <span class="label">Phone: </span>
                    <span class="value"><?= htmlspecialchars($profile['phone'] ?? '—') ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Date of Birth: </span>
                    <span class="value"><?= htmlspecialchars($profile['date_of_birth'] ?? '—') ?></span>
                </div>
            <?php endif; ?>

            <div class="info-row">
                <span class="label">Location: </span>
                <span class="value"><?= htmlspecialchars($profile['location'] ?? '—') ?></span>
            </div>
            <div class="info-row">
                <span class="label">Bio: </span>
                <span class="value"><?= htmlspecialchars($profile['bio'] ?? '—') ?></span>
            </div>
        </div>

        <?php if ($isOwnProfile): ?>
            <div class="footer-link" style="text-align:left; margin-top:1.3rem;">
                <a href="profile-edit.php">Edit Profile</a>
            </div>
        <?php endif; ?>

    </div>

    <h1 style="font-size:1.4rem; margin-top:2rem;">
    <?= $isOwnProfile ? 'My Posts' : htmlspecialchars($profile['first_name']) . '\'s Posts' ?>
    </h1>

    <?php if (empty($posts)): ?>
        <p style="color:var(--muted);">No posts yet.</p>
    <?php else: ?>
        <div class="info-panel">
            <?php foreach ($posts as $post): ?>
                <div class="info-row" style="display:block;">
                    <div class="value" style="font-size:1.05rem; margin-bottom:0.3rem;">
                        <?= htmlspecialchars($post['title']) ?>
                    </div>
                    <div style="margin-bottom:0.5rem;">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>
                    <div class="label">
                        <?= htmlspecialchars($post['created_at']) ?>
                        <?= $post['updated_at'] ? ' (edited)' : '' ?>
                    </div>

                    <?php if ($isOwnProfile): ?>
                        <div style="margin-top:0.5rem;">
                            <a href="post-edit.php?id=<?= (int)$post['id'] ?>">Edit</a>
                            &nbsp;&nbsp;
                            <a href="post-delete.php?id=<?= (int)$post['id'] ?>"
                            onclick="return confirm('Delete this post?');">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>
</html>