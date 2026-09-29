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

            <?php if ($isOwnProfile): ?>
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
    <?= $isOwnProfile ? 'My Posts' : htmlspecialchars($profile['first_name'] ?? "user") . '\'s Posts' ?>
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

                <div class="label" style="margin-bottom:0.5rem;">
                    <?= htmlspecialchars($post['category_name']) ?>
                    &middot; <?= htmlspecialchars(ucfirst($post['status_name'])) ?>
                    &middot; <?= htmlspecialchars($post['created_at']) ?>
                    <?= $post['updated_at'] ? ' (edited)' : '' ?>
                </div>

                <?php if (!empty($tagsByPost[$post['id']])): ?>
                    <div style="margin-bottom:0.5rem; font-size:0.85rem; color:var(--accent);">
                        <?= htmlspecialchars(implode(', ', $tagsByPost[$post['id']])) ?>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom:0.5rem;">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>

                <?php if (!empty($imagesByPost[$post['id']])): ?>
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.5rem;">
                        <?php foreach ($imagesByPost[$post['id']] as $path): ?>
                            <img src="../uploads/posts/<?= htmlspecialchars($path) ?>"
                                alt="Post image"
                                style="width:100px; height:100px; object-fit:cover; border-radius:6px; border:1px solid var(--border);">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($isOwnProfile): ?>
                    <div style="margin-top:0.5rem;">
                        <a href="post-edit.php?id=<?= (int)$post['id'] ?>">Edit</a>
                        &nbsp;&nbsp;
                        <form method="POST" action="post-delete.php" onsubmit="return confirm('Delete this post?');" style="display:inline;">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
                            <button type="submit" style="background:none; border:none; padding:0; color:var(--accent); text-decoration:underline; cursor:pointer;">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>
</html>