<?php
    session_start();

    require_once(__DIR__ . '/../config/constants.php'); 

    require_once(BASE_PATH . '/config/db.php');
    require_once(BASE_PATH . '/functions/functions.php');
    require_once(BASE_PATH . '/functions/authorization.php');
    require_once(BASE_PATH . '/Models/user.php');
    require_once(BASE_PATH . '/Models/role.php');
    require_once(BASE_PATH . '/Models/activityLogger.php');
    require_once(BASE_PATH . '/Models/rateLimiter.php');
    

    isLogged();

    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $errors = [];
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $emailIdentifier = 'email:' . strtolower($email);
        $ipIdentifier = 'ip:' . $ip;

        if (RateLimiter::tooManyLogins($emailIdentifier) || RateLimiter::tooManyLogins($ipIdentifier)) 
        {
            http_response_code(429);
            $errors[] = 'Too many failed login attempts. Please try again in 15 minutes.';

        }elseif(empty($email) || empty($password))
        {
            $errors[] = 'Email and password are required';

        }else{

            $user = new User($pdo);
            $userRow = $user->getRow($email);

            if(!($user->emailExists($email)))
            {
                $errors[] = 'Wrong email or password';
                RateLimiter::recordLogin($emailIdentifier);
                RateLimiter::recordLogin($ipIdentifier);
                ActivityLogger::failedLogin(-1,"wrong_email",$email);

            }elseif(!password_verify($password, $userRow['password'])) {

                $errors[] = 'Wrong email or password';
                RateLimiter::recordLogin($emailIdentifier);
                RateLimiter::recordLogin($ipIdentifier);
                ActivityLogger::failedLogin($userRow["id"],"wrong_password",$email);

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

                ActivityLogger::Login($userRow['id']);

                header('Location: ../index.php');
                exit;
            }
        }
    }
    
    require(BASE_PATH . '/Views/login.php');

