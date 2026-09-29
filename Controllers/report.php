<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireLogin();

    $userId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') 
    {
        header('Location: posts.php');
        exit;
    }

    $targetType = $_POST['target_type'] ?? '';
    $targetId = isset($_POST['target_id']) ? (int)$_POST['target_id'] : 0;
    $reason = trim($_POST['reason'] ?? '');

    if ($targetType !== 'post' && $targetType !== 'comment') 
    {
        http_response_code(400);
        die('Invalid report type.');
    }

    if ($reason === '' || strlen($reason) > 255) 
    {
        die('Please write a reason (max 255 characters). <a href="posts.php">Back</a>');
    }

    if ($targetType === 'post')
    {
        $stmt = $pdo->prepare("SELECT user_id, id AS post_id FROM posts WHERE id = ? AND deleted_at IS NULL");
    } else {
        $stmt = $pdo->prepare("SELECT user_id, post_id FROM comments WHERE id = ? AND deleted_at IS NULL");
    }
    $stmt->execute([$targetId]);
    $target = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$target) 
    {
        http_response_code(404);
        die('Content not found.');
    }

    if ((int)$target['user_id'] === $userId)
    {
        http_response_code(403);
        die('You cannot report your own content. <a href="posts.php">Back</a>');
    }

    $dupStmt = $pdo->prepare("SELECT 1 FROM reports WHERE reporter_id = ? AND target_type = ? AND target_id = ?");
    $dupStmt->execute([$userId, $targetType, $targetId]);
    if ($dupStmt->fetch()) 
    {
        die('You already reported this. <a href="posts.php?id=' . (int)$target['post_id'] . '">Back</a>');
    }

    $pdo->prepare("INSERT INTO reports (reporter_id, target_type, target_id, reason) VALUES (?, ?, ?, ?)")
        ->execute([$userId, $targetType, $targetId, $reason]);

    ActivityLogger::log($userId, 'content_reported', $targetType, $targetId);

    header('Location: posts.php?id=' . (int)$target['post_id']);
    exit;