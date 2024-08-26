<?php
session_start();
require_once __DIR__ . '/../boot.php';

$login = $_POST['login'];
$password = $_POST['password'];

$stmt = pdo()->prepare('SELECT * FROM `users` WHERE `login` = :login');
$stmt->execute(['login' => $_POST['login']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    flash('Пользователь с таким логином не зарегистрирован');
    header('Location: /authorization.php');
    die();

}

if (password_verify($password, $user['password'])) {
    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = pdo()->prepare('UPDATE `users` SET `password` = :password WHERE `login` = :login');
        $stmt->execute([
            'login' => $login,
            'password' => $newHash,
        ]); 
    }
    $_SESSION['user_id'] = $user['id_user'];
    $_SESSION['login'] = $user['login'];
    $_SESSION['user']['id_user'] = $user['id_user'];
    $_SESSION['user_role'] = $user['role'];
    // редирект
    if ($user['role'] == 0) {
        header('Location: /adminpanel.php');
    } else {
        if (!empty($_SESSION['previous_page'])) {
            header('Location: ' . $_SESSION['previous_page']);
        } else {
            header('Location: /index.php');
        }
    }
    die();
}

flash('Неверный пароль');
header('Location: /authorization.php');
?>