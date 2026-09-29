<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireLogin();

    $canPosts = can('moderate_posts');
    $canComments = can('moderate_comments');

    if (!$canPosts && !$canComments) 
    {
        http_response_code(403);
        require(BASE_PATH . '/Views/403.php');
        exit;
    }

    $postReports = [];
    if ($canPosts) 
    {
        $postReports = $pdo->query(
            "SELECT reports.id, reports.target_id, reports.reason, reports.created_at,
                    reporter.name AS reporter_name,
                    author.name AS author_name,
                    posts.title, posts.content
            FROM reports
            JOIN posts ON posts.id = reports.target_id
            JOIN users AS reporter ON reporter.id = reports.reporter_id
            JOIN users AS author ON author.id = posts.user_id
            WHERE reports.target_type = 'post' AND posts.deleted_at IS NULL
            ORDER BY reports.created_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    $commentReports = [];
    if ($canComments) 
    {
        $commentReports = $pdo->query(
            "SELECT reports.id, reports.target_id, reports.reason, reports.created_at,
                    reporter.name AS reporter_name,
                    author.name AS author_name,
                    comments.content
            FROM reports
            JOIN comments ON comments.id = reports.target_id
            JOIN users AS reporter ON reporter.id = reports.reporter_id
            JOIN users AS author ON author.id = comments.user_id
            WHERE reports.target_type = 'comment' AND comments.deleted_at IS NULL
            ORDER BY reports.created_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    require_once(__DIR__ . '/../Views/moderator.php');
