<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/style.css">
    <title>Activity Log</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div class="brand">
            <a href="../index.php">Main</a>
        </div>
    </div>

    <div class="page-content">
        <h1>Activity Log</h1>

        <form method="GET" action="logs-page.php" style="margin-bottom:1.5rem;">
            <select name="user_id" onchange="this.form.submit()"
                    style="padding:0.5rem; border:1px solid var(--border); border-radius:6px;">
                <option value="">All users</option>
                <?php foreach ($allUsers as $u): ?>
                    <option value="<?= (int)$u['id'] ?>" <?php if ($filterUserId == $u['id']) echo 'selected'; ?>>
                        <?= htmlspecialchars($u['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if (empty($logs)): ?>
            <p style="color:var(--muted);">No log entries.</p>
        <?php else: ?>
            <div class="info-panel">
                <?php foreach ($logs as $log): ?>
                    <div class="info-row">
                        <div>
                            <div class="value">
                                <?= htmlspecialchars($log['user_name'] ?? 'Anonymous') ?>
                                &mdash; <?= htmlspecialchars($log['action']) ?>
                            </div>
                            <div class="label">
                                <?php if ($log['target_type']): ?>
                                    <?= htmlspecialchars($log['target_type']) ?> #<?= (int)$log['target_id'] ?> &middot;
                                <?php endif; ?>
                                IP: <?= htmlspecialchars($log['ip_address'] ?? 'unknown') ?>
                                &middot; <?= htmlspecialchars($log['created_at']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <div style="margin-top:1.5rem;">
                <?php if ($page > 1): ?>
                    <a href="logs-page.php?user_id=<?= urlencode($filterUserId ?? '') ?>&page=<?= $page - 1 ?>">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p == $page): ?>
                        <strong><?= $p ?></strong>
                    <?php else: ?>
                        <a href="logs-page.php?user_id=<?= urlencode($filterUserId ?? '') ?>&page=<?= $p ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="logs-page.php?user_id=<?= urlencode($filterUserId ?? '') ?>&page=<?= $page + 1 ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>