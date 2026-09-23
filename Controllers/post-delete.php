<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireLogin();

    $userId = $_SESSION['user_id'];
    $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) 
    {
        http_response_code(404);
        die('Post not found.');
    }

    if ((int)$post['user_id'] !== $userId) 
    {
        http_response_code(403);
        die('You do not have permission to delete this post.');
    }

    $stmt = $pdo->prepare("UPDATE posts SET deleted_at = NOW() WHERE id = ? AND user_id = ?");
    $stmt->execute([$postId, $userId]);

    header('Location: profile.php');
    exit;