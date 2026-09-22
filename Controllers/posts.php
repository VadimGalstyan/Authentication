<?php
    session_start();
    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../config/constants.php');

    $userId = $_SESSION['user_id'] ?? null;
    $isLoggedIn = $userId !== null;
    $highlightId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $stmt = $pdo->prepare(
        "SELECT posts.id, posts.title, posts.content, posts.created_at,
                users.id AS author_id, users.name AS author_name,
                user_profiles.profile_picture,
                categories.name AS category_name,
                post_status.status AS status_name
        FROM posts
        JOIN users ON users.id = posts.user_id
        LEFT JOIN user_profiles ON user_profiles.user_id = users.id
        JOIN categories ON categories.id = posts.category_id
        JOIN post_status ON post_status.id = posts.status_id
        WHERE posts.deleted_at IS NULL AND post_status.status = 'published'
        ORDER BY (posts.id = ?) DESC, posts.created_at DESC"
    );
    $stmt->execute([$highlightId]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $postIds = array_column($posts, 'id');
    $imagesByPost = [];
    $tagsByPost = [];
    $likeCountByPost = [];
    $likedByMe = [];
    $topLevelByPost = [];
    $repliesByComment = [];

    if (!empty($postIds)) {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));

        // Images
        $imgStmt = $pdo->prepare("SELECT post_id, file_path FROM post_images WHERE post_id IN ($placeholders)");
        $imgStmt->execute($postIds);
        foreach ($imgStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $imagesByPost[$row['post_id']][] = $row['file_path'];
        }

        // Tags
        $tagStmt = $pdo->prepare(
            "SELECT post_tag.post_id, tags.name
            FROM post_tag JOIN tags ON tags.id = post_tag.tag_id
            WHERE post_tag.post_id IN ($placeholders)"
        );
        $tagStmt->execute($postIds);
        foreach ($tagStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $tagsByPost[$row['post_id']][] = $row['name'];
        }

        // Like counts
        $likeStmt = $pdo->prepare(
            "SELECT post_id, COUNT(*) AS cnt FROM post_likes WHERE post_id IN ($placeholders) GROUP BY post_id"
        );
        $likeStmt->execute($postIds);
        foreach ($likeStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $likeCountByPost[$row['post_id']] = $row['cnt'];
        }

        
        if ($isLoggedIn) 
        {
            $myLikesStmt = $pdo->prepare(
                "SELECT post_id FROM post_likes WHERE user_id = ? AND post_id IN ($placeholders)"
            );
            $myLikesStmt->execute(array_merge([$userId], $postIds));
            $likedByMe = array_fill_keys($myLikesStmt->fetchAll(PDO::FETCH_COLUMN), true);
        }

        $commentStmt = $pdo->prepare(
            "SELECT comments.id, comments.post_id, comments.user_id, comments.parent_id,
                    comments.content, comments.created_at, comments.deleted_at,
                    users.name AS author_name
            FROM comments
            JOIN users ON users.id = comments.user_id
            WHERE comments.post_id IN ($placeholders)
            ORDER BY comments.created_at ASC"
        );
        $commentStmt->execute($postIds);

        foreach ($commentStmt->fetchAll(PDO::FETCH_ASSOC) as $comment) {
            if ($comment['parent_id'] === null) {
                $topLevelByPost[$comment['post_id']][] = $comment;
            } else {
                $repliesByComment[$comment['parent_id']][] = $comment;
            }
        }
    }

    function displayName($postProfilePicture) {
        return !empty($postProfilePicture) ? '../uploads/profiles/' . $postProfilePicture : '../assets/default-avatar.png';
    }

    require(BASE_PATH . '/Views/posts.php');
