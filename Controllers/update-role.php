<?php
    session_start();
    require_once(__DIR__ . '/../config/constants.php'); 
    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/functions/authorization.php');
    require_once(BASE_PATH . '/Models/user.php');
    require_once(BASE_PATH . '/Models/role.php');

    requirePermission('manage_users');

    $userModel = new User($pdo);
    $roleModel = new Role($pdo);

    $targetUserId = $_POST['user_id'] ?? null;
    $newRoleId = $_POST['role_id'] ?? null;

    $targetUser = $targetUserId ? $userModel->findById($targetUserId) : false;
    $targetRole = $newRoleId ? $roleModel->findById($newRoleId) : false;

    if (!$targetUser || !$targetRole) {
        http_response_code(400);
        die("Invalid user or role.");
    }

    if ((int)$targetUserId === (int)$_SESSION['user_id']) {
        http_response_code(403);
        die("You cannot change your own role.");
    }

    $userModel->updateRole($targetUserId, $newRoleId);

    header('Location: admin-users.php');
    exit;
?>