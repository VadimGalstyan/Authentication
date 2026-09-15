<?php
    require_once(__DIR__ . '/config/constants.php');
    require_once(BASE_PATH . '/functions/authorization.php');

    session_start();
    $isLoggedIn = isset($_SESSION['user_id']);
    require_once('index-view.php');