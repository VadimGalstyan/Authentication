<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Posts Feed</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">
            <a href="../index.php">Main</a>
        </div>
    </div>

    <div class="page-content">
        <h1>Posts Feed</h1>

        <?php if (empty($posts)): ?>
            <p style="color:var(--muted);">No posts yet.</p>
        <?php else: ?>
            <div class="info-panel">
                <?php foreach ($posts as $post): ?>
                    <div class="info-row" style="display:block;">
                        <div class="value" style="font-size:1.05rem; margin-bottom:0.2rem;">
                            <?= htmlspecialchars($post['title']) ?>
                        </div>
                        <div class="label" style="margin-bottom:0.5rem;">
                            by
                            <a href="../Controllers/profile.php?id=<?= (int)$post['author_id'] ?>">
                                <?= htmlspecialchars($post['author_name']) ?>
                            </a>
                            &middot; <?= htmlspecialchars($post['created_at']) ?>
                        </div>
                        <div>
                            <?= nl2br(htmlspecialchars($post['content'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>