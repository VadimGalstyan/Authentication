<?php
    session_start();
    require_once(__DIR__ . '/../config/constants.php'); 

    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/functions/functions.php');
    require_once(BASE_PATH . '/Models/user.php');
    require_once(BASE_PATH . '/Controllers/mailer.php');

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

                $name = $_POST["name"];
                $email = $_POST["email"];

                $verificationToken = $user->registration($name, $email, $_POST["password"]);

                sendVerificationEmail($email, $name, $verificationToken);


                // header('Location: login.php?registered=1');
                // exit;
                header('Location: check-email.php?email=' . urlencode($email));
                exit;
            }

        }

        
    }

    require(BASE_PATH . '/Views/register.php');
        
?>
