<?php
    require_once(__DIR__ . '/../config/constants.php');
    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/Models/user.php');
    require_once(BASE_PATH . '/Models/mailer.php');

    $errors = [];
    $submitted = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $submitted = true;

        if (empty($email)) {
            $errors[] = "Please enter your email address.";
        } else {
            $user = new User($pdo);
            $token = $user->createPasswordResetToken($email);

            if ($token) {
                $userRow = $user->findByEmail($email);
                sendPasswordResetEmail($email, $userRow['name'], $token);
            }
        }
    }

    require(BASE_PATH . '/Views/forgot-password.php');
?>