<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireRole('admin');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restore_id'])) 
    {
        Csrf::verify();
        
        $restoreId = (int)$_POST['restore_id'];
        $pdo->prepare("UPDATE posts SET deleted_at = NULL WHERE id = ?")->execute([$restoreId]);
        ActivityLogger::postRestoredByAdmin($_SESSION["user_id"],$restoreId);
        header('Location: admin-posts-deleted.php');
        exit;
    }

    $stmt = $pdo->query(
        "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.deleted_at,
                users.id AS author_id, users.name AS author_name,
                categories.name AS category_name,
                post_status.status AS status_name
        FROM posts
        JOIN users ON users.id = posts.user_id
        JOIN categories ON categories.id = posts.category_id
        JOIN post_status ON post_status.id = posts.status_id
        WHERE posts.deleted_at IS NOT NULL
        ORDER BY posts.deleted_at DESC"
    );
    $deletedPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    
    $postIds = array_column($deletedPosts, 'id');
    $imagesByPost = [];
    $tagsByPost = [];

    if (!empty($postIds)) 
    {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));

        $imgStmt = $pdo->prepare("SELECT post_id, file_path FROM post_images WHERE post_id IN ($placeholders)");
        $imgStmt->execute($postIds);
        foreach ($imgStmt->fetchAll(PDO::FETCH_ASSOC) as $row) 
        {
            $imagesByPost[$row['post_id']][] = $row['file_path'];
        }

        $tagStmt = $pdo->prepare(
            "SELECT post_tag.post_id, tags.name
            FROM post_tag JOIN tags ON tags.id = post_tag.tag_id
            WHERE post_tag.post_id IN ($placeholders)"
        );

        $tagStmt->execute($postIds);

        foreach ($tagStmt->fetchAll(PDO::FETCH_ASSOC) as $row) 
        {
            $tagsByPost[$row['post_id']][] = $row['name'];
        }
    }

    require(BASE_PATH . '/Views/admin-posts-deleted.php');
