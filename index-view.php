<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <title>My Website</title>
</head>
<body class="dashboard">

    <div class="topbar">
        <div>
            <?php if ($isLoggedIn): ?>
                <a href="Controllers/dashboard.php">Dashboard</a>

                <?php if (hasRole('moderator') || hasRole('admin')): ?>
                    &nbsp;&nbsp;
                    <a href="Controllers/moderator-users.php">Moderate Users</a>
                <?php endif; ?>

                <?php if (hasRole('moderator') || hasRole('admin')): ?>
                    &nbsp;&nbsp;
                    <a href="Controllers/moderator.php">Moderate Posts</a>
                <?php endif; ?>

                <?php if (hasRole('admin')): ?>
                    &nbsp;&nbsp;
                    <a href="Controllers/admin.php">Admin</a>
                    &nbsp;&nbsp;
                    <a href="Controllers/admin-users.php">Manage Users</a>
                    &nbsp;&nbsp;
                    <a href="Controllers/admin-posts-deleted.php">Deleted Posts</a>
                    &nbsp;&nbsp;
                    <a href="Controllers/logs-page.php">Activity Log</a>
                <?php endif; ?>
                &nbsp;&nbsp;
                <a href="../Controllers/posts.php">Posts Feed</a>
            <?php else: ?>
                <a href="../Controllers/posts.php">Posts Feed</a>
            <?php endif; ?>
        </div>
        <div>
            <?php if ($isLoggedIn): ?>
                <a href="Controllers/profile.php">Profile</a>
                &nbsp;&nbsp;            
                <a href="Controllers/logout.php">Log out</a>
            <?php else: ?>
                <a href="Controllers/login.php">Log in</a>
                &nbsp;&nbsp;
                <a href="Controllers/register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="page-content">
        <?php if ($isLoggedIn): ?>
            <h1>Welcome back</h1>
            <p>Ready to share something?</p>
            <a href="Controllers/post-create.php">Create a new post</a>
        <?php else: ?>
            <h1>Welcome</h1>
            <p>Log in or register to start posting.</p>
        <?php endif; ?>
    </div>

</body>
</html>