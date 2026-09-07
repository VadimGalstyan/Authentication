<?php
    session_start();
    require_once(__DIR__ . '/../config/constants.php'); 
    require_once(BASE_PATH . '/functions/authorization.php');

    requireLogin();
    requirePermission('view_dashboard');

    $id = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];
    $email = $_SESSION['user_email'];

    if (!$_SESSION['user_verified'])
    {
        require(BASE_PATH . '/Views/verification-page.php');
        exit;
    }



    require(BASE_PATH . '/Views/dashboard.php');
?>