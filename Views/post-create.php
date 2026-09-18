<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Create Post</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">
            <a href="../index.php">Main</a>
        </div>
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

        <form method="POST" action="post-create.php" enctype="multipart/form-data">
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

            <div class="field">
                <label for="status_id">Status</label>
                <select id="status_id" name="status_id" style="width:100%; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px; font-family:inherit; font-size:0.95rem; background:var(--bg);">
                    <?php $selectedStatus = $_POST['status_id'] ?? 1; ?>
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= (int)$s['id'] ?>" <?= $selectedStatus == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($s['status'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" style="width:100%; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px; font-family:inherit; font-size:0.95rem; background:var(--bg);">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"
                            <?= (($_POST['category_id'] ?? $defaultCategoryId) == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="tag_ids">Tags</label>
                <select id="tag_ids" name="tag_ids[]" multiple size="4"
                        style="width:100%; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px; font-family:inherit; font-size:0.95rem; background:var(--bg);">
                    <?php $selectedTags = $_POST['tag_ids'] ?? []; ?>
                    <?php foreach ($allTags as $tag): ?>
                        <option value="<?= (int)$tag['id'] ?>"
                            <?= in_array($tag['id'], $selectedTags) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tag['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div style="font-size:0.8rem; color:var(--muted); margin-top:0.3rem;">
                    Hold Ctrl (Windows) or Cmd (Mac) to select multiple.
                </div>
            </div>

            <div class="field">
                <label for="post_images">Photos (up to 5)</label>
                <input type="file" id="post_images" name="post_images[]" multiple accept="image/jpeg,image/png,image/webp">
            </div>

            <button type="submit">Publish Post</button>
        </form>
    </div>

</body>
</html>