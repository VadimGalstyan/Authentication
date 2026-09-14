<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
    header('Location: Controllers/login.php');
    exit;
    }

    require_once(__DIR__ . '/../config/constants.php');
    require_once(BASE_PATH . '/Models/user.php'); 
    require_once(BASE_PATH . '/config/db.php');

    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");

    $stmt->execute([$_SESSION['user_id']]);

    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    $picturePath = BASE_PATH . '/uploads/profiles/'. $profile['profile_picture'];
    $pictureUrl = '/uploads/profiles/' . ($profile['profile_picture'] ?? '');
    $defaultUrl = '/assets/default-avatar.png';

    $displayPicture = (!empty($profile['profile_picture']) && file_exists($picturePath)) ? $pictureUrl : $defaultUrl;

    require(BASE_PATH . '/Views/profile.php');
?>