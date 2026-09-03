<?php
    session_start();

    require(__DIR__ . '/../config/constants.php'); 

    require(BASE_PATH . '/config/db.php');
    require(BASE_PATH . '/functions/functions.php');
    require(BASE_PATH . '/Models/user.php');



    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $errors = [];
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if(empty($email) || empty($password))
        {
            $errors[] = 'Email and password are required';
        }else{
            $user = new User($pdo);
            $userRow = $user->getRow($email);

            if(!($user->emailExists($email)) || !password_verify($password, $userRow['password']))
            {
                $errors[] = 'Wrong email or password';
            }else{
                session_regenerate_id(TRUE);

                $_SESSION['user_id'] = $userRow['id'];
                $_SESSION['user_name'] = $userRow['name'];
                $_SESSION['user_email'] = $userRow['email'];

                header('Location: dashboard.php');
                exit;
            }
        }
    }
    
    require(BASE_PATH . '/Views/login.php');

?>