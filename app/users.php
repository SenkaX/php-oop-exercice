<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/vendor/autoload.php';

use Root\Html\Database;
use Root\Html\User;
use Root\Html\Post;

$db = new Database();
$userModel = new User($db);
$postModel = new Post($db);


function getPage(): int {
    return isset($_GET['page']) && ctype_digit($_GET['page']) ? (int)$_GET['page'] : 1;
}

function getLimit(): int {
    return isset($_GET['limit']) && ctype_digit($_GET['limit']) ? (int)$_GET['limit'] : 10;
}

function getPostsCount(int $userId): int {
    global $postModel;
    return count($postModel->getPostsByUserId($userId));
}

function getPagination(int $userId): array {
    $postsCount = getPostsCount($userId);
    $postsPerPage = getLimit();
    $pagesCount = max(1, ceil($postsCount / $postsPerPage));
    
    return [
        'pagesCount' => $pagesCount,
        'currentPage' => getPage(),
    ];
}

function getPaginatedPosts(int $userId): array {
    global $postModel;
    $page = getPage();
    $limit = getLimit();
    $offset = ($page - 1) * $limit;

    $allPosts = $postModel->getPostsByUserId($userId);
    return array_slice($allPosts, $offset, $limit);
}

function errorResponse(string $message, bool $jsonResponse) {
    if ($jsonResponse) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    } else {
        die("Erreur : " . $message);
    }
    exit;
}

// === LOGIQUE PRINCIPALE ===

$userId = $_GET['id'] ?? null;
$jsonResponse = isset($_GET['json']) && $_GET['json'] === 'true';

if ($userId === null || !ctype_digit($userId)) {
    errorResponse("ID de l'utilisateur invalide ou manquant.", $jsonResponse);
}

$user = $userModel->getUserById((int)$userId);

if (!$user) {
    errorResponse("Aucun utilisateur trouvé avec cet ID.", $jsonResponse);
}

$posts = getPaginatedPosts((int)$userId);
$pagination = getPagination((int)$userId);

if ($jsonResponse) {
    header('Content-Type: application/json');
    echo json_encode([
        'user' => $user,
        'posts' => $posts,
        'pagination' => $pagination
    ]);
    exit;
}  