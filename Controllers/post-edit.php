<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireLogin();

    $userId = $_SESSION['user_id'];
    $postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $errors = [];

    // Load the post first, before anything else
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        http_response_code(404);
        die('Post not found.');
    }

    if ((int)$post['user_id'] !== $userId) 
    {
        http_response_code(403);
        die('You do not have permission to edit this post.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

        if (empty($errors)) {
            $stmt = $pdo->prepare(
                "UPDATE posts SET title = ?, content = ?, updated_at = NOW() WHERE id = ? AND user_id = ?"
            );
            $stmt->execute([$title, $content, $postId, $userId]);

            header('Location: profile.php');
            exit;
        }
    }

    require_once(__DIR__ .'/../Views/post-edit.php');
