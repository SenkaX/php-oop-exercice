<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/vendor/autoload.php';

use Root\Html\Database;
use Root\Html\Post;
use Root\Html\Comment;
use Root\Html\User;
use Root\Html\Utils;

$db = new Database();
$postModel = new Post($db);
$commentModel = new Comment($db);
$userModel = new User($db);

$postId = $_GET['id'] ?? null;

if ($postId === null || !ctype_digit($postId)) {
    http_response_code(400);
    echo json_encode(["error" => "ID de l'article invalide ou manquant."]);
    exit;
}

$postId = (int) $postId;

$post = $postModel->getPostById($postId);

if ($post === null) {
    http_response_code(404);
    echo json_encode(["error" => "Aucun article trouvé avec cet ID."]);
    exit;
}

$author = $userModel->getUserById($post['user_id']);
$comments = $commentModel->getCommentsByPostId($post['id']);

if (isset($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode([
        "post" => $post,
        "author" => $author,
        "comments" => $comments
    ], JSON_PRETTY_PRINT);
    exit;
}

function postComment(string $content) {
    global $commentModel, $post;
    if (Utils::isLoggedIn() === false) {
        return;
    }

    $commentModel->createComment($content, $post['id'], $_SESSION['user_id']);
    header('Location: /blogs/index.php?id=' . $post['id']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    postComment($_POST['comment']);
}

var_dump($post);
var_dump($author);
var_dump($comments);
die;
