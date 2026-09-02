<?php
    session_start();
    require('config/db.php');
    require('functions/functions.php');

    $errors = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $errors = validateRegistration($_POST);

        if(empty($errors))
        {
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$_POST['email']]);

            if($stmt->rowCount() > 0)
            {
                $errors[] = "This email is already registered";
            }else{
                $name = $_POST["name"];
                $email = $_POST["email"];
                $password = $_POST["password"];

                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES(?,?,?)");
                $stmt->execute([$name, $email,$hashedPassword]);

                header('Location: login.php?registered=1');
                exit;
            }

        }
    }

?>


<!DOCTYPE html>
<html lang="en">  


<head>  
    <link rel="stylesheet" href="assets/style.css">
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
            Already have an account? <a href="login.php">Log in</a>
        </div>
    </div>

</body>

</html>