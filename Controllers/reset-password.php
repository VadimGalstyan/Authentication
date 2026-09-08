<?php
    require_once(__DIR__ . '/../config/constants.php');
    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/functions/functions.php');
    require_once(BASE_PATH . '/Models/user.php');

    $user = new User($pdo);
    $errors = [];
    $success = false;

    $token = $_GET['token'] ?? $_POST['token'] ?? null;

    $matchedUser = $token ? $user->findByResetToken($token) : false;

    if (!$token || !$matchedUser) {
        $errors[] = "This password reset link is invalid or has expired.";
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($password !== $confirmPassword) {
            $errors[] = "Passwords do not match.";
        }

        $passwordErrors = validatePassword($password); 
        $errors = array_merge($errors, $passwordErrors);

        if (empty($errors)) {
            $user->resetPassword($matchedUser['id'], $password);
            header('Location: login.php?reset=1');
            exit;
        }
    }

    require(BASE_PATH . '/Views/reset-password.php');
?>