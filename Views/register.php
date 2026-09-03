<!DOCTYPE html>
<html lang="en">  


<head>  
    <link rel="stylesheet" href="../assets/style.css">
    <title>Registration</title>
     <style>
        form 
        {
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="card">
        <h1>Register</h1>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="field">
                <label>Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <button type="submit">Create account</button>
        </form>

        <div class="footer-link">
            Already have an account? <a href="../Controllers/login.php">Log in</a>
            
        </div>
    </div>

</body>

</html>