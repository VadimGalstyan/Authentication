<?php
    session_start();
    require(__DIR__ . '/../config/constants.php'); 

    require(BASE_PATH . '/config/db.php');
    require(BASE_PATH . '/functions/functions.php');
    require(BASE_PATH . '/Models/user.php');

    $errors = [];

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $errors = validateRegistration($_POST);

        if(empty($errors))
        {
            $user = new User($pdo);

            if($user->findByEmail($_POST['email']))
            {
                $errors[] = "This email is already registered";
            }else{

                $user->registration($_POST["name"], $_POST["email"], $_POST["password"]);

                header('Location: login.php?registered=1');
                exit;
            }

        }

        
    }

    require(BASE_PATH . '/Views/register.php');
        
?>
