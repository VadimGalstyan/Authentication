<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');


    requireLogin();

    if (empty($_SESSION['user_verified'])) 
    {
        http_response_code(403);
        die('You must verify your email before posting.');
    }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
    {
        header('Location: posts.php');
        exit;
    }
    
    Csrf::verify();

    $userId = $_SESSION['user_id'];

    $identifier = "user:" . $userId;

    if (RateLimiter::tooManyComments($identifier)) 
    {
        http_response_code(429);
        die('You are commenting too fast. Please wait a moment and try again.');
    }

    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $content = trim($_POST['content'] ?? '');

    $postStmt = $pdo->prepare("SELECT id FROM posts WHERE id = ? AND deleted_at IS NULL");
    $postStmt->execute([$postId]);

    if (!$postStmt->fetch()) 
    {
        http_response_code(404);
        die('Post not found.');
    }

    //reply
    if ($parentId !== null) 
    {
        $parentStmt = $pdo->prepare(
            "SELECT id, parent_id FROM comments WHERE id = ? AND post_id = ? AND deleted_at IS NULL"
        );
        $parentStmt->execute([$parentId, $postId]);
        $parent = $parentStmt->fetch(PDO::FETCH_ASSOC);

        if (!$parent) 
        {
            http_response_code(404);
            die('Comment being replied to was not found.');
        }

        if ($parent['parent_id'] !== null) 
        {
            http_response_code(400);
            die('Cannot reply to a reply. This site only supports 2 levels of comments.');
        }
    }

    if ($content === '') 
    {
        header('Location: /../Controllers/posts.php?id=' . $postId);
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO comments (post_id, user_id, parent_id, content) VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([$postId, $userId, $parentId, $content]);

    $newCommentId = (int)$pdo->lastInsertId();

    RateLimiter::recordComment($identifier);
    ActivityLogger::addComment($userId,$newCommentId);

    header('Location: /../Controllers/posts.php?id=' . $postId);
    exit;