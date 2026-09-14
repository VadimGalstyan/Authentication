<?php
session_start();
  
    require_once(__DIR__ . '/../config/db.php'); 
    require_once(__DIR__ . '/../functions/authorization.php'); 

    requireLogin();

    $userId = $_SESSION["user_id"];
    $errors = [];

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);
        $phone = trim($_POST["phone"]);
        $location = trim($_POST["location"]);
        $date_of_birth = trim($_POST["date_of_birth"]);
        $bio = trim($_POST["bio"]);
        $profile_picture = trim($_POST["profile_picture"]);

        if($first_name === "")
        {
            $errors[] = "First name is required.";
        }
        if($last_name === "")
        {
            $errors[] = "Last name is required.";
        }
        if ($date_of_birth !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_birth)) 
        {
            $errors[] = 'Date of birth must be in YYYY-MM-DD format.';
        }

        if (empty($errors)) 
        {
            $stmt = $pdo->prepare(
                "INSERT INTO user_profiles (user_id, first_name, last_name, phone, location, date_of_birth, bio)
                VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
                    first_name = VALUES(first_name),
                    last_name = VALUES(last_name),
                    phone = VALUES(phone),
                    location = VALUES(location),
                    date_of_birth = VALUES(date_of_birth),
                    bio = VALUES(bio)"
            );

            $stmt->execute([$userId, $first_name, $last_name, $phone, $location, $date_of_birth ?: null, $bio]);

            $fullName = trim($first_name . ' ' . $last_name);
            $stmt2 = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmt2->execute([$fullName, $userId]);

            header('Location: profile.php');
            exit;
        }
    }



    $stmt = $pdo->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
    $stmt->execute([$userId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];


    require(__DIR__ . '/../Views/profile-edit.php');