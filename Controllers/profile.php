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
        "SELECT id, title, content, created_at, updated_at
        FROM posts
        WHERE user_id = ?
        ORDER BY created_at DESC"
    );
    $postsStmt->execute([$viewedUserId]);
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

    //picture
    $picturePath = BASE_PATH . '/uploads/profiles/'. $profile['profile_picture'];
    $pictureUrl = '/uploads/profiles/' . ($profile['profile_picture'] ?? '');
    $defaultUrl = '/assets/default-avatar.png';

    $displayPicture = (!empty($profile['profile_picture']) && file_exists($picturePath)) ? $pictureUrl : $defaultUrl;

    require(BASE_PATH . '/Views/profile.php');
?>