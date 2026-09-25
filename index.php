<?php
    require_once(__DIR__ . '/config/constants.php');
    require_once(BASE_PATH . '/functions/authorization.php');
    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/Models/activityLogger.php');

    if (session_status() === PHP_SESSION_NONE) 
    {
       session_start();
    }  
    $isLoggedIn = isset($_SESSION['user_id']);

    require_once('index-view.php');