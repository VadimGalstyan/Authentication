<?php
    session_start();
    require_once(__DIR__ . '/../config/db.php');
    require_once(__DIR__ . '/../config/constants.php');    
    require_once(__DIR__ . '/../functions/authorization.php');
    
    $canModeratePosts = can('moderate_posts');
    $canModerateComments = can('moderate_comments');

    $userId = $_SESSION['user_id'] ?? null;
    $isLoggedIn = $userId !== null;
    $highlightId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    $search = isset($_GET['q']) ? trim($_GET['q']) : '';
    if (strlen($search) > 100) 
    {
        $search = substr($search, 0, 100);
    }

    $categoryId = null;
    if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) 
    {
        $categoryId = (int)$_GET['category_id'];
    }

    $tagId = null;
    if (isset($_GET['tag_id']) && is_numeric($_GET['tag_id'])) 
    {
        $tagId = (int)$_GET['tag_id'];
    }

    $authorId = null;
    if (isset($_GET['author_id']) && is_numeric($_GET['author_id'])) 
    {
        $authorId = (int)$_GET['author_id'];
    }

    $sort = 'newest';
    if (isset($_GET['sort'])) 
    {
        if ($_GET['sort'] === 'newest' || $_GET['sort'] === 'most_liked' || $_GET['sort'] === 'most_commented')
        {
            $sort = $_GET['sort'];
        }
    }

    $page = 1;
    if (isset($_GET['page']) && is_numeric($_GET['page'])) 
    {
        $page = (int)$_GET['page'];
        if ($page < 1) 
        {
            $page = 1;
        }
    }

    $perPage = 10;

    $whereParts = [];
    $whereParts[] = "posts.deleted_at IS NULL";
    $whereParts[] = "post_status.status = 'published'";
    $params = [];

    if ($search !== '') 
    {
        $whereParts[] = "(posts.title LIKE ? OR posts.content LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    if ($categoryId !== null) 
    {
        $whereParts[] = "posts.category_id = ?";
        $params[] = $categoryId;
    }

    if ($authorId !== null) 
    {
        $whereParts[] = "posts.user_id = ?";
        $params[] = $authorId;
    }

    if ($tagId !== null) 
    {
        $whereParts[] = "posts.id IN (SELECT post_id FROM post_tag WHERE tag_id = ?)";
        $params[] = $tagId;
    }

    $whereSql = implode(' AND ', $whereParts);

    if ($sort === 'most_liked') 
    {
        $orderBySql = 'like_count DESC, posts.created_at DESC';
    } else if ($sort === 'most_commented') {
        $orderBySql = 'comment_count DESC, posts.created_at DESC';
    } else {
        $orderBySql = 'posts.created_at DESC';
    }

    $countStmt = $pdo->prepare(
        "SELECT COUNT(*) FROM posts
        JOIN categories ON categories.id = posts.category_id
        JOIN post_status ON post_status.id = posts.status_id
        WHERE $whereSql"
    );
    $countStmt->execute($params);
    $totalPosts = (int)$countStmt->fetchColumn();

    $totalPages = ceil($totalPosts / $perPage);
    if ($totalPages < 1) 
    {
        $totalPages = 1;
    }

    if ($page > $totalPages) 
    {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;

    $mainStmt = $pdo->prepare(
        "SELECT posts.id, posts.title, posts.content, posts.created_at,
                users.id AS author_id, users.name AS author_name,
                user_profiles.profile_picture,
                categories.name AS category_name,
                (SELECT COUNT(*) FROM post_likes WHERE post_likes.post_id = posts.id) AS like_count,
                (SELECT COUNT(*) FROM comments WHERE comments.post_id = posts.id AND comments.deleted_at IS NULL) AS comment_count
        FROM posts
        JOIN users ON users.id = posts.user_id
        LEFT JOIN user_profiles ON user_profiles.user_id = users.id
        JOIN categories ON categories.id = posts.category_id
        JOIN post_status ON post_status.id = posts.status_id
        WHERE $whereSql
        ORDER BY $orderBySql
        LIMIT $perPage OFFSET $offset"
    );
    $mainStmt->execute($params);
    $posts = $mainStmt->fetchAll(PDO::FETCH_ASSOC);

    $postIds = array_column($posts, 'id');
    $imagesByPost = [];
    $tagsByPost = [];
    $likedByMe = [];
    $topLevelByPost = [];
    $repliesByComment = [];

    if (!empty($postIds)) 
    {
        $placeholders = implode(',', array_fill(0, count($postIds), '?'));

        $imgStmt = $pdo->prepare("SELECT post_id, file_path FROM post_images WHERE post_id IN ($placeholders)");
        $imgStmt->execute($postIds);
        foreach ($imgStmt->fetchAll(PDO::FETCH_ASSOC) as $row) 
        {
            $imagesByPost[$row['post_id']][] = $row['file_path'];
        }

        $tagStmt = $pdo->prepare(
            "SELECT post_tag.post_id, tags.name
            FROM post_tag JOIN tags ON tags.id = post_tag.tag_id
            WHERE post_tag.post_id IN ($placeholders)"
        );
        $tagStmt->execute($postIds);
        foreach ($tagStmt->fetchAll(PDO::FETCH_ASSOC) as $row) 
        {
            $tagsByPost[$row['post_id']][] = $row['name'];
        }

        if ($isLoggedIn) 
        {
            $myLikesStmt = $pdo->prepare(
                "SELECT post_id FROM post_likes WHERE user_id = ? AND post_id IN ($placeholders)"
            );
            $myLikesStmt->execute(array_merge([$userId], $postIds));
            $likedByMe = array_fill_keys($myLikesStmt->fetchAll(PDO::FETCH_COLUMN), true);
        }

        $commentStmt = $pdo->prepare(
            "SELECT comments.id, comments.post_id, comments.user_id, comments.parent_id,
                    comments.content, comments.created_at,
                    users.name AS author_name
            FROM comments
            JOIN users ON users.id = comments.user_id
            WHERE comments.post_id IN ($placeholders) AND comments.deleted_at IS NULL
            ORDER BY comments.created_at ASC"
        );
        $commentStmt->execute($postIds);

        foreach ($commentStmt->fetchAll(PDO::FETCH_ASSOC) as $comment) 
        {
            if ($comment['parent_id'] === null) 
            {
                $topLevelByPost[$comment['post_id']][] = $comment;
            } else {
                $repliesByComment[$comment['parent_id']][] = $comment;
            }
        }
    }

    $categories = $pdo->query("SELECT id, name FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $allTags = $pdo->query("SELECT id, name FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    $authors = $pdo->query("SELECT DISTINCT users.id, users.name FROM users JOIN posts ON posts.user_id = users.id ORDER BY users.name")->fetchAll(PDO::FETCH_ASSOC);


    require(BASE_PATH . '/Views/posts.php');
