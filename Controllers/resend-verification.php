<?php
    require_once(__DIR__ . '/../config/constants.php');
    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/Models/user.php');
    require_once(BASE_PATH . '/Controllers/mailer.php');

    $email = $_POST['email'] ?? '';

    if ($email) {

        $user = new User($pdo);
        $newToken = $user->regenerateVerificationToken($email);

        if ($newToken) {
            $userRow = $user->findByEmail($email);
            sendVerificationEmail($email, $userRow['name'], $newToken);
        }

    }

    header('Location: check-email.php?email=' . urlencode($email));
    exit;