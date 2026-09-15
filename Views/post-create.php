<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Create Post</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">My Website</div>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>

    <div class="page-content">
        <h1>Create Post</h1>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="post-create.php">
            <div class="field">
                <label for="title">Title</label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>

            <div class="field">
                <label for="content">Content</label>
                <textarea id="content" name="content" rows="8"
                          style="width:100%; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px; font-family:inherit; font-size:0.95rem; color:var(--ink); background:var(--bg);"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </div>

            <button type="submit">Publish Post</button>
        </form>
    </div>

</body>
</html>