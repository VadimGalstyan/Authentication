<?php
    
    if (session_status() === PHP_SESSION_NONE) 
    {
       session_start();
    }   
    require_once(__DIR__ . '/../Models/ActivityLogger.php');
    require_once(__DIR__ . '/../Models/rateLimiter.php');
    require_once(__DIR__ . '/../Models/csrf.php');

    $dsn = "mysql:host=localhost;dbname=mywebsite;charset=utf8mb4;port=3306;";
    $login = "root";
    $password = "Kamrad44!!";

    try
    {
        $pdo = new PDO($dsn, $login, $password);
        ActivityLogger::init($pdo);
        RateLimiter::init($pdo);
    } catch (PDOException $e) {
        echo "". $e->getMessage();
    }

    