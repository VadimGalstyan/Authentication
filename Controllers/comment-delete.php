<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireLogin();

    $userId = $_SESSION['user_id'];
    $commentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $postId = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

    $stmt = $pdo->prepare("SELECT user_id, parent_id FROM comments WHERE id = ?");
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
        die('You do not have permission to delete this comment.');
    }

    $pdo->beginTransaction();

    try {

        $pdo->prepare("UPDATE comments SET deleted_at = NOW() WHERE id = ? AND user_id = ?")
            ->execute([$commentId, $userId]);

        if ($comment['parent_id'] === null) 
        {
            $pdo->prepare("UPDATE comments SET deleted_at = NOW() WHERE parent_id = ? AND deleted_at IS NULL")
                ->execute([$commentId]);
        }

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
    }

    header('Location: posts.php?id=' . $postId);
    exit;