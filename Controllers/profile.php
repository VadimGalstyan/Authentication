<?php
    session_start();

    require_once(__DIR__ . '/../config/constants.php');
    require_once(BASE_PATH . '/Models/user.php'); 
    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/functions/authorization.php');

    requireLogin();

    $viewedUserId = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];
    $isOwnProfile = $viewedUserId === (int)$_SESSION['user_id'];

    $stmt = $pdo->prepare(
        "SELECT u.email, p.first_name, p.last_name, p.phone, p.location,
                p.date_of_birth, p.bio, p.profile_picture
        FROM users u
        LEFT JOIN user_profiles p ON p.user_id = u.id
        WHERE u.id = ?"
    );
    $stmt->execute([$viewedUserId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        http_response_code(404);
        die('User not found.');
    }

    //post
    $postsStmt = $pdo->prepare(
        "SELECT posts.id, posts.title, posts.content, posts.created_at, posts.updated_at,
                categories.name AS category_name,
                post_status.status AS status_name
        FROM posts
        JOIN categories ON categories.id = posts.category_id
        JOIN post_status ON post_status.id = posts.status_id
        WHERE posts.user_id = ? AND posts.deleted_at IS NULL
        ORDER BY posts.created_at DESC"
    );
    $postsStmt->execute([$viewedUserId]);
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

    $postIds = array_column($posts, 'id');
    $imagesByPost = [];
    $tagsByPost = [];

    if (!empty($postIds)) 
    {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));

        $imgStmt = $pdo->prepare("SELECT post_id, file_path FROM post_images WHERE post_id IN ($placeholders)");
        $imgStmt->execute($postIds);
        foreach ($imgStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $imagesByPost[$row['post_id']][] = $row['file_path'];
        }

        $tagStmt = $pdo->prepare(
            "SELECT post_tag.post_id, tags.name
            FROM post_tag
            JOIN tags ON tags.id = post_tag.tag_id
            WHERE post_tag.post_id IN ($placeholders)"
        );
        $tagStmt->execute($postIds);
        foreach ($tagStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $tagsByPost[$row['post_id']][] = $row['name'];
        }
    }

    //picture
    $picturePath = BASE_PATH . '/uploads/profiles/'. $profile['profile_picture'];
    $pictureUrl = '/uploads/profiles/' . ($profile['profile_picture'] ?? '');
    $defaultUrl = '/assets/default-avatar.png';

    $displayPicture = (!empty($profile['profile_picture']) && file_exists($picturePath)) ? $pictureUrl : $defaultUrl;

    require(BASE_PATH . '/Views/profile.php');
