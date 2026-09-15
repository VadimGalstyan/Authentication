<?php
session_start();

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../functions/authorization.php');

requireLogin();

$userId = $_SESSION['user_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (strlen($title) > 150) {
        $errors[] = 'Title must be 150 characters or fewer.';
    }

    if ($content === '') {
        $errors[] = 'Content is required.';
    }

    if (empty($errors)) 
    {
        $stmt = $pdo->prepare(
            "INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)"
        );
        $stmt->execute([$userId, $title, $content]);

        header('Location: profile.php');
        exit;
    }
}

require_once(__DIR__ . '/../Views/post-create.php');