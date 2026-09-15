<?php
    session_start();

    require_once(__DIR__ . '/../config/constants.php'); 

    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/functions/functions.php');
    require_once(BASE_PATH . '/functions/authorization.php');
    require_once(BASE_PATH . '/Models/user.php');
    require_once(BASE_PATH . '/Models/role.php');

    isLogged();

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $errors = [];
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        if(empty($email) || empty($password)){
            $errors[] = 'Email and password are required';
        }else{
            $user = new User($pdo);
            $userRow = $user->getRow($email);

            if(!($user->emailExists($email)) || !password_verify($password, $userRow['password'])){
                
                $errors[] = 'Wrong email or password';

            }else{

                session_regenerate_id(TRUE);

                $_SESSION['user_id'] = $userRow['id'];
                $_SESSION['user_name'] = $userRow['name'];
                $_SESSION['user_email'] = $userRow['email'];
                $_SESSION['user_verified'] = ($userRow['email_verified_at'] !== NULL);

                $role = new Role($pdo);
                $userRole = $role->findById($userRow['role_id']);

                $_SESSION['user_role'] = $userRole['name'];
                $_SESSION['user_permissions'] = $role->getPermissionsForRole($userRow['role_id']);

                header('Location: ../index.php');
                exit;
            }
        }
    }
    
    require(BASE_PATH . '/Views/login.php');

?>