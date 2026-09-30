<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Manage Categories</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <a href="../index.php">Main</a>

    </div>

    <div class="page-content">
        <h1>Manage Categories</h1>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="categories-manage.php" style="margin-bottom:1.5rem; display:flex; gap:0.5rem;">
            <?= Csrf::field() ?>
            <input type="hidden" name="action" value="add">
            <input type="text" name="name" placeholder="New category name" style="flex:1; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px;">
            <button type="submit">Add Category</button>
        </form>

        <div class="info-panel">
            <?php foreach ($categories as $cat): ?>
                <div class="info-row">
                    <form method="POST" action="categories-manage.php" style="display:flex; gap:0.5rem; align-items:center; flex:1;">
                        <?= Csrf::field() ?>
                        <input type="hidden" name="action" value="rename">
                        <input type="hidden" name="category_id" value="<?= (int)$cat['id'] ?>">
                        <input type="text" name="name" value="<?= htmlspecialchars($cat['name']) ?>" style="padding:0.4rem;">
                        <button type="submit">Rename</button>
                    </form>

                    <?php if ($cat['name'] !== 'Uncategorized'): ?>
                        <form method="POST" action="categories-manage.php"
                              onsubmit="return confirm('Delete this category? Its posts will move to Uncategorized.');">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="category_id" value="<?= (int)$cat['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>