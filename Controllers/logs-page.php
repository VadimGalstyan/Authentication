<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireRole('admin'); 

    $filterUserId = null;
    if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) 
    {
        $filterUserId = (int)$_GET['user_id'];
    }

    $page = 1;
    if (isset($_GET['page']) && is_numeric($_GET['page']))
    {
        $page = (int)$_GET['page'];
        if ($page < 1) 
        {
            $page = 1;
        }
    }

    $perPage = 20;

    $whereSql = '';
    $params = [];

    if ($filterUserId !== null)
    {
        $whereSql = 'WHERE activity_log.user_id = ?';
        $params[] = $filterUserId;
    }

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM activity_log $whereSql");
    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();

    $totalPages = ceil($totalRows / $perPage);
    if ($totalPages < 1) 
    {
        $totalPages = 1;
    }

    if ($page > $totalPages) 
    {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;

    $stmt = $pdo->prepare(
        "SELECT activity_log.id, activity_log.action, activity_log.target_type,
                activity_log.target_id, activity_log.ip_address, activity_log.created_at,
                users.name AS user_name
        FROM activity_log
        LEFT JOIN users ON users.id = activity_log.user_id
        $whereSql
        ORDER BY activity_log.created_at DESC
        LIMIT $perPage OFFSET $offset"
    );
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $allUsers = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

    require_once(__DIR__ . '/../Views/logs-page.php'); 

