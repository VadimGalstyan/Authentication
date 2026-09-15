<?php
    session_start();
    require_once(__DIR__ . '/../config/db.php');

    $stmt = $pdo->query(
        "SELECT posts.id, posts.title, posts.content, posts.created_at,
                users.id AS author_id, users.name AS author_name
        FROM posts
        JOIN users ON users.id = posts.user_id
        ORDER BY posts.created_at DESC"
    );
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $isLoggedIn = isset($_SESSION['user_id']);

    require_once(__DIR__ .'/../Views/posts.php');
