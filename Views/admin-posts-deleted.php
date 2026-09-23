<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Deleted Posts</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <a href="../index.php">Home</a>
    </div>

    <div class="page-content">
        <h1>Deleted Posts</h1>

        <?php if (empty($deletedPosts)): ?>
            <p style="color:var(--muted);">No deleted posts.</p>
        <?php else: ?>
            <?php foreach ($deletedPosts as $post): $pid = $post['id']; ?>
                <div class="info-panel" style="margin-bottom:1.2rem; padding:1.25rem;">

                    <div class="value" style="font-size:1.1rem; margin-bottom:0.2rem;">
                        <?= htmlspecialchars($post['title']) ?>
                    </div>

                    <div class="label" style="margin-bottom:0.6rem;">
                        by <a href="profile.php?id=<?= (int)$post['author_id'] ?>"><?= htmlspecialchars($post['author_name']) ?></a>
                        &middot; <?= htmlspecialchars($post['category_name']) ?>
                        &middot; <?= htmlspecialchars(ucfirst($post['status_name'])) ?>
                        &middot; created <?= htmlspecialchars($post['created_at']) ?>
                        &middot; deleted <?= htmlspecialchars($post['deleted_at']) ?>
                    </div>

                    <?php if (!empty($tagsByPost[$pid])): ?>
                        <div style="margin-bottom:0.6rem; font-size:0.85rem; color:var(--accent);">
                            <?= htmlspecialchars(implode(', ', $tagsByPost[$pid])) ?>
                        </div>
                    <?php endif; ?>

                    <div style="margin-bottom:0.8rem;">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>

                    <?php if (!empty($imagesByPost[$pid])): ?>
                        <div style="display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.8rem;">
                            <?php foreach ($imagesByPost[$pid] as $path): ?>
                                <img src="../uploads/posts/<?= htmlspecialchars($path) ?>"
                                     style="width:100px; height:100px; object-fit:cover; border-radius:6px; border:1px solid var(--border);">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="admin-posts-deleted.php">
                        <input type="hidden" name="restore_id" value="<?= (int)$pid ?>">
                        <button type="submit">Restore</button>
                    </form>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>