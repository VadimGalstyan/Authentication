<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Manage Tags</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <a href="../index.php">Main</a>

    </div>

    <div class="page-content">
        <h1>Manage Tags</h1>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="tags-manage.php" style="margin-bottom:1.5rem; display:flex; gap:0.5rem;">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="add">
            <input type="text" name="name" placeholder="New tag name" style="flex:1; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px;">
            <button type="submit">Add Tag</button>
        </form>

        <?php if (empty($tags)): ?>
            <p style="color:var(--muted);">No tags yet.</p>
        <?php else: ?>
            <div class="info-panel">
                <?php foreach ($tags as $tag): ?>
                    <div class="info-row" style="align-items:center;">
                        <form method="POST" action="tags-manage.php" style="display:flex; gap:0.5rem; align-items:center; flex:1;">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="rename">
                            <input type="hidden" name="tag_id" value="<?= (int)$tag['id'] ?>">
                            <input type="text" name="name" value="<?= htmlspecialchars($tag['name']) ?>" style="padding:0.4rem;">
                            <button type="submit">Rename</button>
                            <span class="label"><?= (int)$tag['post_count'] ?> post(s)</span>
                        </form>

                        <form method="POST" action="tags-manage.php"
                              onsubmit="return confirm('Delete this tag? It will be removed from <?= (int)$tag['post_count'] ?> post(s).');">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="tag_id" value="<?= (int)$tag['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>