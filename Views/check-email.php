<!DOCTYPE html>
<html>
<head>
    <title>Verify your email</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="card">
        <h1>Please verify your email</h1>

        <p class="footer-link" style="margin-top:0; text-align:left;">
            We sent a verification link to
            <strong><?= htmlspecialchars($email) ?></strong>.
            Click the link in that email to activate your account.
        </p>

        <form method="POST" action="resend-verification.php">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <button type="submit">Resend verification email</button>
        </form>

        <p class="footer-link">
            <a href="../Controllers/login.php">Back to login</a>
        </p>
    </div>
</body>
</html>