<?php
    session_start();
    require_once(__DIR__ . '/../config/constants.php'); 
    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/functions/authorization.php');
    require_once(BASE_PATH . '/Models/user.php');
    require_once(BASE_PATH . '/Models/role.php');

    requirePermission('access_admin_page');

    $userModel = new User($pdo);
    $roleModel = new Role($pdo);

    $users = $userModel->getAllWithRoles();
    $allRoles = $roleModel->getAllRoles();

    require(BASE_PATH . '/Views/admin-users.php');
?>