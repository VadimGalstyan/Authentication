<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireLogin();

    $userId = $_SESSION['user_id'];
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;

    $postStmt = $pdo->prepare("SELECT id FROM posts WHERE id = ? AND deleted_at IS NULL");
    $postStmt->execute([$postId]);
    if (!$postStmt->fetch()) 
    {
        http_response_code(404);
        die('Post not found.');
    }

    $checkStmt = $pdo->prepare("SELECT 1 FROM post_likes WHERE post_id = ? AND user_id = ?");
    $checkStmt->execute([$postId, $userId]);

    if ($checkStmt->fetch())
    {
        $pdo->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?")
            ->execute([$postId, $userId]);
    } else {
        $pdo->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)")
            ->execute([$postId, $userId]);
    }

    $redirect = $_POST['redirect'] ?? '../posts.php';
    header('Location: ' . $redirect . '?id=' . $postId);
    exit;