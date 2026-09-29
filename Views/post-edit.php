<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Edit Post</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">
            <a href="../index.php">Main</a>
        </div>
        <a href="post-view.php?id=<?= (int)$postId ?>">Back to Post</a>
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

        <form method="POST" action="post-edit.php?id=<?= (int)$postId ?>" enctype="multipart/form-data">
            <?= Csrf::field() ?>
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

            <div class="field">
                <label for="status_id">Status</label>
                <select id="status_id" name="status_id" style="width:100%; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px; background:var(--bg);">
                    <?php $selectedStatus = $_POST['status_id'] ?? $post['status_id']; ?>
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?= (int)$s['id'] ?>" <?= $selectedStatus == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($s['status'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" style="width:100%; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px; background:var(--bg);">
                    <?php $selectedCategory = $_POST['category_id'] ?? $post['category_id']; ?>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>" <?= $selectedCategory == $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="tag_ids">Tags</label>
                <select id="tag_ids" name="tag_ids[]" multiple size="4" style="width:100%; padding:0.65rem 0.75rem; border:1px solid var(--border); border-radius:6px; background:var(--bg);">
                    <?php $selectedTags = $_POST['tag_ids'] ?? $currentTagIds; ?>
                    <?php foreach ($allTags as $tag): ?>
                        <option value="<?= (int)$tag['id'] ?>" <?= in_array($tag['id'], $selectedTags) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tag['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($existingImages)): ?>
                <div class="field">
                    <label>Current Photos</label>
                    <div style="display:flex; gap:0.8rem; flex-wrap:wrap;">
                        <?php foreach ($existingImages as $img): ?>
                            <div style="text-align:center;">
                                <img src="../uploads/posts/<?= htmlspecialchars($img['file_path']) ?>"
                                     style="width:100px; height:100px; object-fit:cover; border-radius:6px; border:1px solid var(--border); display:block; margin-bottom:0.3rem;">
                                <label style="font-size:0.8rem;">
                                    <input type="checkbox" name="remove_image_ids[]" value="<?= (int)$img['id'] ?>"> Remove
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="field">
                <label for="post_images">Add More Photos</label>
                <input type="file" id="post_images" name="post_images[]" multiple accept="image/jpeg,image/png,image/webp">
            </div>

            <button type="submit">Save Changes</button>
        </form>
    </div>

</body>
</html>