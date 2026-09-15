<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Edit Post</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">My Website</div>
        <a href="profile.php">Back to Profile</a>
    </div>

    <div class="page-content">
        <h1>Edit Post</h1>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="post-edit.php?id=<?= (int)$postId ?>">
            <div class="field">
                <label for="title">Title</label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($_POST['title'] ?? $post['title']) ?>">
            </div>

            <div class="field">
                <label for="content">Content</label>
                <textarea id="content" name="content" rows="8"
                          style="width:100%; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px; font-family:inherit; font-size:0.95rem; color:var(--ink); background:var(--bg);"><?= htmlspecialchars($_POST['content'] ?? $post['content']) ?></textarea>
            </div>

            <button type="submit">Save Changes</button>
        </form>
    </div>

</body>
</html>