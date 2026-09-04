<?php
    session_start();
    require_once(__DIR__ . '/../config/constants.php'); 



    if (!isset($_SESSION["user_id"]))
    {
        header('Location: login.php');
        exit;
    }
    else
    {
        $email = $_SESSION['user_email'];
        if (!$_SESSION['user_verified'])
        {

            require(BASE_PATH . '/Views/verification-page.php');
            exit;
        }
        
    }

    $id = $_SESSION['user_id'];
    $userName = $_SESSION['user_name'];
    $email = $_SESSION['user_email'];

    require(BASE_PATH . '/Views/dashboard.php');
?>

