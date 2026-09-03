<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="../assets/style.css">

    <title>Login</title>
    <style>
        form 
        {
            text-align: center;
        }
    </style>

</head>
<body>
    <div class="card">
        <h1>Log in</h1>

        <?php if (!empty($errors)): ?>
            <ul style="color: red;">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
            </div>


            <button type="submit">Log in</button>

            <div class="footer-link">
                Don't have an account? <a href="register.php">Register</a>
            </div>

        </form>
    </div>

</body>   
