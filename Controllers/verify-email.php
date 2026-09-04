<?php
    require_once(__DIR__ . '/../config/constants.php');
    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/Models/user.php');

    $user = new User($pdo);

    $token = $_GET['token'] ?? null;
    $result = null;

    if (!$token) {

        $result = 'missing_token';

    } else {

        $matchedUser = $user->findByToken($token);

        if (!$matchedUser) {

            $result = 'invalid_token';

        } else {

            $user->verifyEmail($matchedUser['id']);
            $result = 'success';

        }
    }

    require(BASE_PATH . '/Views/verify-email.php');
?>