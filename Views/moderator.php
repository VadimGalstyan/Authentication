<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Moderation</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">
            <a href="../index.php">Main</a>
        </div>
    </div>

    <div class="page-content">
        <h1>Moderation</h1>

        <?php if ($canPosts): ?>
            <h2 style="font-size:1.2rem;">Reported Posts</h2>

            <?php if (empty($postReports)): ?>
                <p style="color:var(--muted);">No reported posts.</p>
            <?php else: ?>
                <div class="info-panel" style="margin-bottom:2rem;">
                    <?php foreach ($postReports as $r): ?>
                        <div class="info-row" style="display:block;">
                            <div class="value"><?= htmlspecialchars($r['title']) ?></div>
                            <div class="label">by <?= htmlspecialchars($r['author_name']) ?></div>
                            <div style="margin:0.4rem 0;"><?= nl2br(htmlspecialchars($r['content'])) ?></div>
                            <div class="label">
                                Reported by <?= htmlspecialchars($r['reporter_name']) ?>
                                &middot; <?= htmlspecialchars($r['created_at']) ?>
                            </div>
                            <div style="margin:0.3rem 0;"><strong>Reason:</strong> <?= htmlspecialchars($r['reason']) ?></div>

                            <!-- Hide post -->
                            <form method="POST" action="post-delete.php" onsubmit="return confirm('Delete this post?');" style="display:inline;">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int)$r['target_id'] ?>">
                                <button type="submit">Hide post</button>
                            </form>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($canComments): ?>
            <h2 style="font-size:1.2rem;">Reported Comments</h2>

            <?php if (empty($commentReports)): ?>
                <p style="color:var(--muted);">No reported comments.</p>
            <?php else: ?>
                <div class="info-panel">
                    <?php foreach ($commentReports as $r): ?>
                        <div class="info-row" style="display:block;">
                            <div class="label">Comment by <?= htmlspecialchars($r['author_name']) ?></div>
                            <div style="margin:0.4rem 0;"><?= nl2br(htmlspecialchars($r['content'])) ?></div>
                            <div class="label">
                                Reported by <?= htmlspecialchars($r['reporter_name']) ?>
                                &middot; <?= htmlspecialchars($r['created_at']) ?>
                            </div>
                            <div style="margin:0.3rem 0;"><strong>Reason:</strong> <?= htmlspecialchars($r['reason']) ?></div>

                             <!-- Hide comment -->
                            <form method="POST" action="comment-delete.php" onsubmit="return confirm('Delete this comment?');" style="display:inline;">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int)$r['target_id'] ?>">
                                <input type="hidden" name="post_id" value="<?= (int)$r['post_id'] ?>">
                                <button type="submit">Hide comment (with its replies)</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>