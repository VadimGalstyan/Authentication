<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="card">
        <h1>Forgot your password?</h1>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php elseif ($submitted): ?>
            <p class="footer-link" style="margin-top:0; text-align:left;">
                If an account with that email exists, we've sent a password reset link.
            </p>
        <?php else: ?>
            <form method="POST" action="forgot-password.php">
                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">Send reset link</button>
            </form>
        <?php endif; ?>

        <p class="footer-link">
            <a href="login.php">Back to login</a>
        </p>
    </div>
</body>
</html>