<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireRole('admin');

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') 
    {
        Csrf::verify();

        $name = trim($_POST['name'] ?? '');

        if ($name === '') 
        {
            $errors[] = 'Category name is required.';
        } else {
            $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
            $checkStmt->execute([$name]);

            if ($checkStmt->fetch())
            {
                $errors[] = 'That category already exists.';
            } else {
                $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
                header('Location: categories-manage.php');
                exit;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rename') 
    {
        Csrf::verify();

        $categoryId = (int)($_POST['category_id'] ?? 0);
        $newName = trim($_POST['name'] ?? '');

        if ($newName === '') 
        {
            $errors[] = 'Category name is required.';
        } else {
            $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?")->execute([$newName, $categoryId]);
            header('Location: categories-manage.php');
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') 
    {
        Csrf::verify();

        $categoryId = (int)($_POST['category_id'] ?? 0);

        $defaultStmt = $pdo->prepare("SELECT id FROM categories WHERE name = 'Uncategorized'");
        $defaultStmt->execute();
        $defaultId = $defaultStmt->fetchColumn();

        if (!$defaultId) 
        {
            $pdo->prepare("INSERT INTO categories (name) VALUES ('Uncategorized')")->execute();
            $defaultId = $pdo->lastInsertId();
        }

        if ($categoryId == $defaultId)
        {
            $errors[] = 'The "Uncategorized" category cannot be deleted.';
        } else {
            $pdo->beginTransaction();

            try 
            {
                $pdo->prepare("UPDATE posts SET category_id = ? WHERE category_id = ?")
                    ->execute([$defaultId, $categoryId]);

                $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$categoryId]);

                $pdo->commit();
                header('Location: categories-manage.php');
                exit;

            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = 'Something went wrong deleting the category.';
            }
        }
    }

    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

    require_once(__DIR__ . '/../Views/categories-manage.php'); 
