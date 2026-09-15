<?php
session_start();
  
    require_once(__DIR__ . '/../config/db.php'); 
    require_once(__DIR__ . '/../functions/authorization.php'); 

    requireLogin();

    $userId = $_SESSION["user_id"];
    $errors = [];

    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $removePicture = isset($_POST['remove_picture']) && $_POST['remove_picture'] === '1';
        $profilePhotoName = null;
        $clearPicture = false;

        if(!empty($_FILES["profile_picture"]["name"]))
        {
            echo("flag1");
            $allowedTypes = ["image/jpeg", "image/png", "image/webp"];
            $allowedExtensions = ["jpg", "jpeg", "png", "webp"];

            $tmpPath = $_FILES["profile_picture"]["tmp_name"];
            $originalName = $_FILES["profile_picture"]["name"];
            $uploadError = $_FILES["profile_picture"]["error"];
            $fileSize = $_FILES["profile_picture"]["size"];

            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $tmpPath);
            // finfo_close($finfo);

            if($uploadError != UPLOAD_ERR_OK)
            {
                $errors[] = "There was a problem uploading the file";
            }else if(!in_array($extension,$allowedExtensions) || !in_array($mimeType, $allowedTypes)) {
                $errors[] = "Allowed types of file is jpeg,png anf webp";
            }else if($fileSize > 2 * 1024 * 1024) {
                $errors[] = "Invalid file size(must be less than 2mb)";
            }else {
                echo("flag2");
                $oldPicStmt = $pdo->prepare("SELECT profile_picture FROM user_profiles WHERE user_id = ?");
                $oldPicStmt->execute([$userId]);
                $oldPicture = $oldPicStmt->fetchColumn();

                $profilePhotoName = bin2hex(random_bytes(16)) . '.' . $extension;
                $destination = __DIR__ . "/../uploads/profiles/" . $profilePhotoName;
                move_uploaded_file($tmpPath, $destination);
            }

        }else if ($removePicture) {
            echo("flag3");
            $oldPicStmt = $pdo->prepare("SELECT profile_picture FROM user_profiles WHERE user_id = ?");
            $oldPicStmt->execute([$userId]);
            $oldPicture = $oldPicStmt->fetchColumn();

            if (!empty($oldPicture)) 
            {
                $oldPath = __DIR__ . '/../uploads/profiles/' . $oldPicture;
                if (file_exists($oldPath)) 
                {
                    unlink($oldPath);
                }
            }

            $clearPicture = true;
        }
       


        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);
        $phone = trim($_POST["phone"]);
        $location = trim($_POST["location"]);
        $date_of_birth = trim($_POST["date_of_birth"]);
        $bio = trim($_POST["bio"]);

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
                if ($profilePhotoName !== null) 
                {
                    echo("flag4");

                    $stmt = $pdo->prepare(
                        "INSERT INTO user_profiles (user_id, first_name, last_name, phone, location, date_of_birth, bio, profile_picture)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
                            first_name = VALUES(first_name), last_name = VALUES(last_name),
                            phone = VALUES(phone), location = VALUES(location),
                            date_of_birth = VALUES(date_of_birth), bio = VALUES(bio),
                            profile_picture = VALUES(profile_picture)"
                    );
                    $stmt->execute([$userId, $first_name, $last_name, $phone, $location, $date_of_birth ?: null, $bio, $profilePhotoName]);

                } else if ($clearPicture) {
                    echo("flag5");
                    $stmt = $pdo->prepare(
                        "INSERT INTO user_profiles (user_id, first_name, last_name, phone, location, date_of_birth, bio, profile_picture)
                        VALUES (?, ?, ?, ?, ?, ?, ?, NULL) ON DUPLICATE KEY UPDATE
                            first_name = VALUES(first_name), last_name = VALUES(last_name),
                            phone = VALUES(phone), location = VALUES(location),
                            date_of_birth = VALUES(date_of_birth), bio = VALUES(bio),
                            profile_picture = NULL"
                    );

                    $stmt->execute([$userId, $first_name, $last_name, $phone, $location, $date_of_birth ?: null, $bio]);

                } else {
                    echo("flag6");
                    $stmt = $pdo->prepare(
                        "INSERT INTO user_profiles (user_id, first_name, last_name, phone, location, date_of_birth, bio)
                        VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE
                            first_name = VALUES(first_name), last_name = VALUES(last_name),
                            phone = VALUES(phone), location = VALUES(location),
                            date_of_birth = VALUES(date_of_birth), bio = VALUES(bio)"
                    );

                    $stmt->execute([$userId, $first_name, $last_name, $phone, $location, $date_of_birth ?: null, $bio]);
                }



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