<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="card">
        <?php if ($result === 'success'): ?>
            <h1>Email verified</h1>
            <p class="footer-link" style="margin-top:0; text-align:left;">
                Your email has been verified. You can now log in.
            </p>
            <a href="../Controllers/login.php"><button type="button">Go to login</button></a>

        <?php elseif ($result === 'invalid_token'): ?>
            <h1>Invalid link</h1>
            <p class="footer-link" style="margin-top:0; text-align:left;">
                This verification link is invalid or has already been used.
            </p>

        <?php else: ?>
            <h1>Missing link</h1>
            <p class="footer-link" style="margin-top:0; text-align:left;">
                No verification token was provided.
            </p>

        <?php endif; ?>
    </div>
</body>
</html>