<?php
    require_once(__DIR__ . '/../config/constants.php'); 

    $email = $_GET['email'] ?? '';

    require(BASE_PATH . '/Views/check-email.php');
