<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="dashboard">
    <div class="topbar">

        <div class="brand">
            <a href="../index.php">Main</a>
        </div>

    </div>
    
    <div class="page-content">
        <h1>Users</h1>

        <div class="info-panel">
            <?php foreach ($users as $u): ?>
                <div class="info-row">
                    <div>
                        <div class="value"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</div>
                        <div class="label">
                            ID: <?= (int)$u['id'] ?> ·
                            Role: <?= htmlspecialchars($u['role_name']) ?> ·
                            Verified: <?= $u['email_verified_at'] ? 'Yes' : 'No' ?> ·
                            Joined: <?= htmlspecialchars($u['created_at']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>