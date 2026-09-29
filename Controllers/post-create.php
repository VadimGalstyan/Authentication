<?php
session_start();

require_once(__DIR__ . '/../config/db.php');
require_once(__DIR__ . '/../functions/authorization.php');

requireLogin();

$userId = $_SESSION['user_id'];
$errors = [];

$identifier = "user:" . $userId;

if (RateLimiter::tooManyPosts($identifier)) 
{
    http_response_code(429);
    die('You have reached the daily limit of 10 posts. Please try again tomorrow.');
}

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$allTags = $pdo->query("SELECT id, name FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$statuses = $pdo->query("SELECT id, status FROM post_status ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

$defaultCategoryStmt = $pdo->query("SELECT id FROM categories WHERE name = 'Uncategorized' LIMIT 1");
$defaultCategoryId = $defaultCategoryStmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $statusId = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 1;
    $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : (int)$defaultCategoryId;
    $tagIds = $_POST['tag_ids'] ?? [];

    if ($title === '') 
    {
        $errors[] = 'Title is required.';
    } elseif (strlen($title) > 150) {
        $errors[] = 'Title must be 150 characters or fewer.';
    }

    if ($content === '') 
    {
        $errors[] = 'Content is required.';
    }

    $validStatusIds = array_column($statuses, 'id');
    if (!in_array($statusId, $validStatusIds)) 
    {
        $errors[] = 'Invalid status.';
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $validatedFiles = [];

    if (!empty($_FILES['post_images']['name'][0])) 
    {

        $fileCount = count($_FILES['post_images']['name']);

        if ($fileCount > 5) 
        {
            $errors[] = 'You can upload at most 5 photos per post.';
        }

        for ($i = 0; $i < $fileCount; $i++) 
        {
        if ($_FILES['post_images']['error'][$i] !== UPLOAD_ERR_OK) 
            {
                $errors[] = 'One of the uploaded files had a problem.';
                continue;
            }

            $tmpPath = $_FILES['post_images']['tmp_name'][$i];
            $originalName = $_FILES['post_images']['name'][$i];
            $fileSize = $_FILES['post_images']['size'][$i];

            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpPath);

            if (!in_array($extension, $allowedExtensions) || !in_array($mimeType, $allowedTypes)) 
            {
                $errors[] = "\"$originalName\" must be a JPG, PNG, or WebP image.";
                continue;
            }

            if ($fileSize > 2 * 1024 * 1024) 
            {
                $errors[] = "\"$originalName\" must be smaller than 2MB.";
                continue;
            }

            $validatedFiles[] = [
                'tmp_path' => $tmpPath,
                'filename' => bin2hex(random_bytes(16)) . '.' . $extension,
            ];
        }
    }

    if (empty($errors))     
    {
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO posts (user_id, title, content, status_id, category_id) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$userId, $title, $content, $statusId, $categoryId]);
            $postId = $pdo->lastInsertId();

            if (!empty($tagIds)) 
            {
                $tagStmt = $pdo->prepare("INSERT INTO post_tag (post_id, tag_id) VALUES (?, ?)");
                foreach ($tagIds as $tagId) 
                {
                    $tagStmt->execute([$postId, (int)$tagId]);
                }
            }

            if (!empty($validatedFiles)) 
            {
                $userFolder = __DIR__ . '/../uploads/posts/' . $userId;
                if (!is_dir($userFolder)) 
                {
                    mkdir($userFolder, 0755, true);
                }

                $imgStmt = $pdo->prepare("INSERT INTO post_images (post_id, file_path) VALUES (?, ?)");
                foreach ($validatedFiles as $file) 
                {
                    $destination = $userFolder . '/' . $file['filename'];
                    move_uploaded_file($file['tmp_path'], $destination);
                    $imgStmt->execute([$postId, $userId . '/' . $file['filename']]);
                }
            }

            $pdo->commit();

            RateLimiter::recordPost($identifier);
            ActivityLogger::postCreated($userId, $postId);

            header('Location: profile.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Something went wrong creating the post. Please try again.';
        }
    }
}

require_once(__DIR__ . '/../Views/post-create.php');