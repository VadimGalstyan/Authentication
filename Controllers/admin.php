<?php
    session_start();
    require_once(__DIR__ . '/../config/constants.php'); 
    require_once(BASE_PATH . '/functions/authorization.php');

    requirePermission('access_admin_page');

    require(BASE_PATH . '/Views/admin.php');
?>