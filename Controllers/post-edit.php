<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireLogin();

    $userId = $_SESSION['user_id'];
    $postId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $errors = [];

    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND deleted_at IS NULL");
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
        die('You do not have permission to edit this post.');
    }

    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $allTags = $pdo->query("SELECT id, name FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $statuses = $pdo->query("SELECT id, status FROM post_status ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

    $existingImagesStmt = $pdo->prepare("SELECT id, file_path FROM post_images WHERE post_id = ?");
    $existingImagesStmt->execute([$postId]);
    $existingImages = $existingImagesStmt->fetchAll(PDO::FETCH_ASSOC);

    $currentTagsStmt = $pdo->prepare("SELECT tag_id FROM post_tag WHERE post_id = ?");
    $currentTagsStmt->execute([$postId]);
    $currentTagIds = $currentTagsStmt->fetchAll(PDO::FETCH_COLUMN);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        Csrf::verify();


        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $statusId = isset($_POST['status_id']) ? (int)$_POST['status_id'] : $post['status_id'];
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : $post['category_id'];
        $tagIds = $_POST['tag_ids'] ?? [];
        $removeImageIds = $_POST['remove_image_ids'] ?? []; // checkboxes for existing images to delete

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

        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $validatedNewFiles = [];

        $existingCountAfterRemoval = count($existingImages) - count($removeImageIds);
        $newFileCount = !empty($_FILES['post_images']['name'][0]) ? count($_FILES['post_images']['name']) : 0;

        if (($existingCountAfterRemoval + $newFileCount) > 5) 
        {
            $errors[] = 'A post can have at most 5 photos total.';
        }

        if ($newFileCount > 0) 
        {
            for ($i = 0; $i < $newFileCount; $i++) 
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

                $validatedNewFiles[] = [
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
                    "UPDATE posts SET title = ?, content = ?, status_id = ?, category_id = ?, updated_at = NOW()
                    WHERE id = ? AND user_id = ?"
                );
                $stmt->execute([$title, $content, $statusId, $categoryId, $postId, $userId]);

                $pdo->prepare("DELETE FROM post_tag WHERE post_id = ?")->execute([$postId]);
                if (!empty($tagIds))
                {
                    $tagStmt = $pdo->prepare("INSERT INTO post_tag (post_id, tag_id) VALUES (?, ?)");
                    foreach ($tagIds as $tagId) {
                        $tagStmt->execute([$postId, (int)$tagId]);
                    }
                }

                if (!empty($removeImageIds)) 
                {
                    foreach ($removeImageIds as $imgId) 
                    {
                        $imgId = (int)$imgId;
                        $findStmt = $pdo->prepare("SELECT file_path FROM post_images WHERE id = ? AND post_id = ?");
                        $findStmt->execute([$imgId, $postId]);
                        $filePath = $findStmt->fetchColumn();

                        if ($filePath) 
                        {
                            $fullPath = __DIR__ . '/../uploads/posts/' . $filePath;
                            if (file_exists($fullPath)) 
                            {
                                unlink($fullPath);
                            }
                            $pdo->prepare("DELETE FROM post_images WHERE id = ?")->execute([$imgId]);
                        }
                    }
                }

                if (!empty($validatedNewFiles)) 
                {
                    $userFolder = __DIR__ . '/../uploads/posts/' . $userId;
                    if (!is_dir($userFolder)) 
                    {
                        mkdir($userFolder, 0755, true);
                    }

                    $imgStmt = $pdo->prepare("INSERT INTO post_images (post_id, file_path) VALUES (?, ?)");

                    foreach ($validatedNewFiles as $file) 
                    {
                        $destination = $userFolder . '/' . $file['filename'];
                        move_uploaded_file($file['tmp_path'], $destination);
                        $imgStmt->execute([$postId, $userId . '/' . $file['filename']]);
                    }
                }

                $pdo->commit();
                
                ActivityLogger::postUpdated($userId, $postId);

                header('Location: profile.php');
                exit;

            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = 'Something went wrong updating the post. Please try again.';
            }
        }
    }

    require(__DIR__ . '/../Views/post-edit.php');