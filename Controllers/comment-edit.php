<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
    {
        header('Location: posts.php');
        exit;
    }

    Csrf::verify();

    $userId = $_SESSION['user_id'];
    $commentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $content = trim($_POST['content'] ?? '');

    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comment) 
    {
        http_response_code(404);
        die('Comment not found.');
    }

    if ((int)$comment['user_id'] !== $userId) 
    {
        http_response_code(403);
        die('You do not have permission to edit this comment.');
    }

    if ($content === '') 
    {
        header('Location: posts.php?id=' . $postId);
        exit;
    }

    $pdo->prepare("UPDATE comments SET content = ?, updated_at = NOW() WHERE id = ? AND user_id = ?")
        ->execute([$content, $commentId, $userId]);

    header('Location: posts.php?id=' . $postId);
    exit;