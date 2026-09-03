<?php
    function validateRegistration(array $data)
    {
        $errors = [];

        $name = trim($data["name"] ?? '');
        $email = trim($data["email"] ?? '');
        $password = trim($data["password"] ?? '');

        if(empty($name))
        {
            $errors[] = "Input valid name";
        }
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $errors[] = "Input valid email";
        }

        if(strlen($password) < 8)
        {
            $errors[] = "Password must have at least 8 characters";
        }

        return $errors;
    }

?>