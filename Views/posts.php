<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Posts Feed</title>
    <style>
        .post-card { border: 1px solid var(--border); border-radius: 8px; margin-bottom: 1.5rem; padding: 1.25rem; background: var(--surface); }
        .post-card.highlighted { border: 2px solid var(--accent); }
        .post-author-row { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.8rem; }
        .post-author-row img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border); }
        .post-author-row a { font-weight: 500; color: var(--ink); text-decoration: none; }
        .post-author-row a:hover { text-decoration: underline; }
        .post-category { font-size: 0.8rem; color: var(--accent); text-transform: uppercase; letter-spacing: 0.03em; }
        .post-title { font-family: 'Fraunces', serif; font-size: 1.2rem; color: var(--primary); margin: 0.2rem 0 0.7rem; }
        .post-images { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.8rem; }
        .post-images img { width: 100%; max-width: 300px; max-height: 300px; object-fit: cover; border-radius: 6px; }
        .post-meta { font-size: 0.85rem; color: var(--muted); margin-top: 0.6rem; }
        .post-tags { font-size: 0.85rem; color: var(--accent); }
        .post-actions { margin-top: 0.8rem; display: flex; gap: 1rem; align-items: center; }
        .post-actions button, .toggle-link { background: none; border: none; padding: 0; color: var(--primary); cursor: pointer; font-size: 0.9rem; }
        .toggle-link { text-decoration: underline; }
        .comments-section { display: none; margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 1rem; }
        .comment-block { margin-bottom: 0.9rem; }
        .replies-block { display: none; margin-top: 0.5rem; margin-left: 1.5rem; border-left: 2px solid var(--border); padding-left: 1rem; font-size: 0.85rem; }
        .comment-form textarea { width: 100%; padding: 0.6rem; border: 1px solid var(--border); border-radius: 6px; font-family: inherit; font-size: 0.9rem; background: var(--bg); color: var(--ink); }
        #filter-panel .field { margin-bottom: 0.8rem; }
        #filter-panel select, #filter-panel input[type="text"] { width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; }
        .pagination a, .pagination strong { margin: 0 0.3rem; }
    </style>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">
            <a href="../index.php">Main</a>
        </div>
    </div>

    <div class="page-content">
        <h1>Posts Feed</h1>

        <button type="button" onclick="var p = document.getElementById('filter-panel'); if (p.style.display === 'block') { p.style.display = 'none'; } else { p.style.display = 'block'; }">
            Filters
        </button>

        <form method="GET" action="posts.php" id="filter-panel" style="display:none; border:1px solid var(--border); border-radius:8px; padding:1rem; margin:1rem 0; background:var(--surface);">
            <div class="field">
                <label>Search</label>
                <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Title or content...">
            </div>

            <div class="field">
                <label>Category</label>
                <select name="category_id">
                    <option value="">Any</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>" <?php if ($categoryId == $cat['id']) echo 'selected'; ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label>Tag</label>
                <select name="tag_id">
                    <option value="">Any</option>
                    <?php foreach ($allTags as $tag): ?>
                        <option value="<?= (int)$tag['id'] ?>" <?php if ($tagId == $tag['id']) echo 'selected'; ?>>
                            <?= htmlspecialchars($tag['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label>Author</label>
                <select name="author_id">
                    <option value="">Any</option>
                    <?php foreach ($authors as $author): ?>
                        <option value="<?= (int)$author['id'] ?>" <?php if ($authorId == $author['id']) echo 'selected'; ?>>
                            <?= htmlspecialchars($author['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label>Sort by</label>
                <select name="sort">
                    <option value="newest" <?php if ($sort === 'newest') echo 'selected'; ?>>Newest first</option>
                    <option value="most_liked" <?php if ($sort === 'most_liked') echo 'selected'; ?>>Most liked</option>
                    <option value="most_commented" <?php if ($sort === 'most_commented') echo 'selected'; ?>>Most commented</option>
                </select>
            </div>

            <button type="submit">Apply</button>
            <a href="posts.php" style="margin-left:0.5rem;">Clear</a>
        </form>

        <?php if (empty($posts)): ?>
            <p style="color:var(--muted);">No posts found.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): $pid = $post['id']; ?>
                <div class="post-card<?= $post['id'] == $highlightId ? ' highlighted' : '' ?>">

                    <div class="post-author-row">
                        <img src="../uploads/profiles/<?= htmlspecialchars($post['profile_picture'] ?: 'default.jpg') ?>" alt="Profile Picture">
                        <a href="profile.php?id=<?= (int)$post['author_id'] ?>">
                            <?= htmlspecialchars($post['author_name']) ?>
                        </a>
                    </div>

                    <div class="post-category"><?= htmlspecialchars($post['category_name']) ?></div>
                    <div class="post-title">
                        <a href="posts.php?id=<?= (int)$pid ?>" style="color:inherit; text-decoration:none;">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </div>

                    <?php if (!empty($imagesByPost[$pid])): ?>
                        <div class="post-images">
                            <?php foreach ($imagesByPost[$pid] as $path): ?>
                                <img src="../uploads/posts/<?= htmlspecialchars($path) ?>" alt="Post image">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div><?= nl2br(htmlspecialchars($post['content'])) ?></div>

                    <div class="post-meta">
                        <?= htmlspecialchars($post['created_at']) ?>
                        <?php if (!empty($tagsByPost[$pid])): ?>
                            &middot; <span class="post-tags"><?= htmlspecialchars(implode(', ', $tagsByPost[$pid])) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="post-actions">
                        <?php if ($isLoggedIn && $post['author_id'] != $userId): ?>
                            <button type="button" class="toggle-link" 
                            onclick="toggleSection('report-post-<?= $pid ?>')">Report</button>
                        <?php endif; ?>

                        <?php if ($isLoggedIn && ($post['author_id'] == $userId || $canModeratePosts)): ?>
                            <form method="POST" action="post-delete.php" onsubmit="return confirm('Delete this post?');" style="display:inline;">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="id" value="<?= (int)$pid ?>">
                                <button type="submit" style="background:none; border:none; padding:0; color:var(--primary); text-decoration:underline; cursor:pointer;">Delete</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($isLoggedIn): ?>
                            <form method="POST" action="post-like.php" style="display:inline;">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="post_id" value="<?= (int)$pid ?>">
                                <input type="hidden" name="redirect" value="posts.php">
                                <button type="submit">
                                    <?= isset($likedByMe[$pid]) ? 'Unlike' : 'Like' ?> (<?= (int)$post['like_count'] ?>)
                                </button>
                            </form>
                        <?php else: ?>
                            <span style="font-size:0.9rem; color:var(--muted);"><?= (int)$post['like_count'] ?> likes</span>
                        <?php endif; ?>

                        <button type="button" class="toggle-link" onclick="toggleSection('comments-<?= $pid ?>')">
                            Show comments (<?= (int)$post['comment_count'] ?>)
                        </button>
                    </div>

                    <?php if ($isLoggedIn && $post['author_id'] != $userId): ?>
                        <form method="POST" action="report.php" id="report-post-<?= $pid ?>" style="display:none; margin-top:0.5rem;">
                            <?= Csrf::field() ?>
                            <input type="hidden" name="target_type" value="post">
                            <input type="hidden" name="target_id" value="<?= (int)$pid ?>">
                            <input type="text" name="reason" placeholder="Why are you reporting this post?" maxlength="255" required>
                            <button type="submit">Send report</button>
                        </form>
                    <?php endif; ?>

                    <div class="comments-section" id="comments-<?= $pid ?>">

                        <?php if ($isLoggedIn): ?>
                            <form method="POST" action="comment-add.php" class="comment-form" style="margin-bottom:1rem;">
                                <?= Csrf::field() ?>
                                <input type="hidden" name="post_id" value="<?= (int)$pid ?>">
                                <textarea name="content" rows="2" placeholder="Write a comment..."></textarea>
                                <button type="submit" style="margin-top:0.4rem;">Post Comment</button>
                            </form>
                        <?php endif; ?>

                        <?php if (empty($topLevelByPost[$pid])): ?>
                            <p style="color:var(--muted); font-size:0.9rem;">No comments yet.</p>
                        <?php else: ?>
                            <?php foreach ($topLevelByPost[$pid] as $comment): $cid = $comment['id']; ?>
                                <div class="comment-block">
                                    <div class="post-meta">
                                        <?= htmlspecialchars($comment['author_name']) ?>
                                        &middot; <?= htmlspecialchars($comment['created_at']) ?>
                                    </div>

                                    <div><?= nl2br(htmlspecialchars($comment['content'])) ?></div>

                                    <?php if ($isLoggedIn && ($comment['user_id'] == $userId || $canModerateComments)): ?>
                                        <form method="POST" action="comment-delete.php" onsubmit="return confirm('Delete this comment?');" style="display:inline;">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="id" value="<?= (int)$cid ?>">
                                            <input type="hidden" name="post_id" value="<?= (int)$pid ?>">
                                            <button type="submit" style="background:none; border:none; padding:0; color:var(--primary); text-decoration:underline; cursor:pointer;">Delete</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($isLoggedIn && $comment['user_id'] != $userId): ?>
                                        <button type="button" class="toggle-link" onclick="toggleSection('report-comment-<?= (int)$cid ?>')">Report</button>
                                        <form method="POST" action="report.php" id="report-comment-<?= (int)$cid ?>" style="display:none; margin-top:0.3rem;">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="target_type" value="comment">
                                            <input type="hidden" name="target_id" value="<?= (int)$cid ?>">
                                            <input type="text" name="reason" placeholder="Reason..." maxlength="255" required>
                                            <button type="submit">Send</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (!empty($repliesByComment[$cid])): ?>
                                        <button type="button" class="toggle-link" onclick="toggleSection('replies-<?= $cid ?>')">
                                            Show replies (<?= count($repliesByComment[$cid]) ?>)
                                        </button>
                                        <div class="replies-block" id="replies-<?= $cid ?>">
                                            <?php foreach ($repliesByComment[$cid] as $reply): ?>
                                                <div style="margin-bottom:0.6rem;">
                                                    <div class="post-meta">
                                                        <?= htmlspecialchars($reply['author_name']) ?>
                                                        &middot; <?= htmlspecialchars($reply['created_at']) ?>
                                                    </div>
                                                    <div><?= nl2br(htmlspecialchars($reply['content'])) ?></div>

                                                    <?php if ($isLoggedIn && ($reply['user_id'] == $userId || $canModerateComments)): ?>
                                                        <form method="POST" action="comment-delete.php" onsubmit="return confirm('Delete this comment?');" style="display:inline;">
                                                            <?= Csrf::field() ?>
                                                            <input type="hidden" name="id" value="<?= (int)$reply['id'] ?>">
                                                            <input type="hidden" name="post_id" value="<?= (int)$pid ?>">
                                                            <button type="submit" style="background:none; border:none; padding:0; color:var(--primary); text-decoration:underline; cursor:pointer;">Delete</button>
                                                        </form>
                                                    <?php endif; ?>

                                                    <?php if ($isLoggedIn && $reply['user_id'] != $userId): ?>
                                                        <button type="button" class="toggle-link" onclick="toggleSection('report-comment-<?= (int)$reply['id'] ?>')">Report</button>
                                                        <form method="POST" action="report.php" id="report-comment-<?= (int)$reply['id'] ?>" style="display:none; margin-top:0.3rem;">
                                                            <?= Csrf::field() ?>
                                                            <input type="hidden" name="target_type" value="comment">
                                                            <input type="hidden" name="target_id" value="<?= (int)$reply['id'] ?>">
                                                            <input type="text" name="reason" placeholder="Reason..." maxlength="255" required>
                                                            <button type="submit">Send</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($isLoggedIn): ?>
                                        <form method="POST" action="comment-add.php" class="comment-form" style="margin-top:0.5rem;">
                                            <?= Csrf::field() ?>
                                            <input type="hidden" name="post_id" value="<?= (int)$pid ?>">
                                            <input type="hidden" name="parent_id" value="<?= (int)$cid ?>">
                                            <textarea name="content" rows="1" placeholder="Reply..."></textarea>
                                            <button type="submit" style="margin-top:0.3rem;">Reply</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <div class="pagination" style="margin-top:1.5rem;">
                <?php
                $filterQuery = 'q=' . urlencode($search)
                              . '&category_id=' . urlencode($categoryId ?? '')
                              . '&tag_id=' . urlencode($tagId ?? '')
                              . '&author_id=' . urlencode($authorId ?? '')
                              . '&sort=' . urlencode($sort);
                ?>

                <?php if ($page > 1): ?>
                    <a href="posts.php?<?= $filterQuery ?>&page=<?= $page - 1 ?>">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p == $page): ?>
                        <strong><?= $p ?></strong>
                    <?php else: ?>
                        <a href="posts.php?<?= $filterQuery ?>&page=<?= $p ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="posts.php?<?= $filterQuery ?>&page=<?= $page + 1 ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>

    <script>
        function toggleSection(id) 
        {
            var el = document.getElementById(id);
            el.style.display = (el.style.display === 'block') ? 'none' : 'block';
        }
    </script>

</body>
</html>