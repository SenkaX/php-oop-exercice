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

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

$db = new Database();
$userModel = new User($db);

function login(string $email, string $password): bool {
    global $userModel;
    $user = $userModel->getUserByEmail($email);

    if (!$user) {
        return false;
    }

    if (!password_verify($password, $user['password'])) {
        return false;
    }

    $_SESSION['user_id'] = $user['id'];
    header('Location: /profile.php');
    exit;
}

$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $success = login($email, $password);
}
?>

<!doctype html>
<html class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-full">
    <main class="min-h-full">
        <div class="flex flex-col min-h-full w-full items-center justify-start">
            <div class="flex flex-row w-full h-24 bg-gray-900 items-center justify-center">
                <div class="w-11/12 flex flex-row items-center justify-end space-x-4">
                    <a href="/" class="text-white">Homepage</a>
                    <?php if (isLoggedIn()): ?>
                        <a href="/blogs/new.php" class="text-white">Create post</a>
                        <a href="/profile.php" class="text-white">Profile</a>
                        <a href="/logout.php" class="text-white">Logout</a>
                    <?php else: ?>
                        <a href="/login.php" class="text-white">Login</a>
                        <a href="/register.php" class="text-white">Register</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex flex-col w-11/12 items-center justify-start">
                <h1 class="text-4xl">Login</h1>
                <form action="/login.php" method="post" class="flex flex-col w-1/2 space-y-4">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="p-2 border border-gray-300 rounded" required>
                    
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="p-2 border border-gray-300 rounded" required>
                    
                    <button type="submit" class="p-2 bg-blue-500 text-white rounded">Login</button>
                </form>
                <?php if ($success === false): ?>
                    <p class="text-red-500">Invalid email or password</p>
                <?php endif; ?>
            </div>
        </div>        
    </main>
</body>
</html>