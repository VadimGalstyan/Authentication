<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
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

                    <?php if (can('manage_users')): ?>
                        <form method="POST" action="update-role.php">
                            <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                            <select name="role_id">
                                <?php foreach ($allRoles as $r): ?>
                                    <option value="<?= (int)$r['id'] ?>" <?= $r['id'] == $u['role_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit">Update Role</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>