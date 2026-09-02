<?php
    session_start();
    require('config/db.php');
    require('functions/functions.php');



    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $errors = [];
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if(empty($email) || empty($password))
        {
            $errors[] = 'email and password are required';
        }else{
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($user) || !password_verify($password, $user['password']))
            {
                $errors[] = 'Wrong email or password';
            }else{
                session_regenerate_id(TRUE);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];

                header('Location: dashboard.php');
                exit;
            }
        }
    }
  

?>

<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="assets/style.css">

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
