<?php
    session_start();

    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../functions/authorization.php');

    requireRole('admin');

    $errors = [];

    function normalizeTagName($name)
    {
        return strtolower(trim($name));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') 
    {
        Csrf::verify();

        $name = normalizeTagName($_POST['name'] ?? '');

        if ($name === '') 
        {
            $errors[] = 'Tag name is required.';
        } else {
            $checkStmt = $pdo->prepare("SELECT id FROM tags WHERE name = ?");
            $checkStmt->execute([$name]);

            if ($checkStmt->fetch()) 
            {
                $errors[] = 'That tag already exists.';
            } else {
                $pdo->prepare("INSERT INTO tags (name) VALUES (?)")->execute([$name]);
                header('Location: tags-manage.php');
                exit;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rename') 
    {
        Csrf::verify();

        $tagId = (int)($_POST['tag_id'] ?? 0);
        $newName = normalizeTagName($_POST['name'] ?? '');

        if ($newName === '') 
        {
            $errors[] = 'Tag name is required.';
        } else {

            $checkStmt = $pdo->prepare("SELECT id FROM tags WHERE name = ? AND id != ?");
            $checkStmt->execute([$newName, $tagId]);

            if ($checkStmt->fetch()) 
            {
                $errors[] = 'Another tag already has that name.';
            } else {
                $pdo->prepare("UPDATE tags SET name = ? WHERE id = ?")->execute([$newName, $tagId]);
                header('Location: tags-manage.php');
                exit;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete')
    {
        Csrf::verify();

        $tagId = (int)($_POST['tag_id'] ?? 0);
        $pdo->prepare("DELETE FROM tags WHERE id = ?")->execute([$tagId]);
        header('Location: tags-manage.php');
        exit;
    }

    $tags = $pdo->query(
        "SELECT tags.id, tags.name, COUNT(post_tag.post_id) AS post_count
        FROM tags
        LEFT JOIN post_tag ON post_tag.tag_id = tags.id
        GROUP BY tags.id, tags.name
        ORDER BY tags.name"
    )->fetchAll(PDO::FETCH_ASSOC);

    require_once(__DIR__ . '/../Views/tags-manage.php'); 
