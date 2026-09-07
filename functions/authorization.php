<?php
    require_once(BASE_PATH . '/Models/role.php');

    function requireLogin() : void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
    }

    function hasRole(string $roleName) : bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $roleName;
    }

    function requireRole(string $roleName) : void
    {
        requireLogin();
 
        if (!hasRole($roleName)) {
            http_response_code(403);
            require(BASE_PATH . '/Views/403.php');
            exit;
        }
    }

    function can(string $permissionName) : bool
    {
        return isset($_SESSION['user_permissions'])
            && in_array($permissionName, $_SESSION['user_permissions'], true);
    }

    function requirePermission(string $permissionName) : void
    {
        requireLogin();

        if (!can($permissionName)) {
            http_response_code(403);
            require(BASE_PATH . '/Views/403.php');
            exit;
        }
    }
?>