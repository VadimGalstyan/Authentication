<?php
    $dsn = "mysql:host=localhost;dbname=mywebsite;charset=utf8mb4;port=3306;";
    $login = "root";
    $password = "Kamrad44!!";

    try {
        $pdo = new PDO($dsn, $login, $password);
    } catch (PDOException $e) {
        echo "". $e->getMessage();
    }
?>