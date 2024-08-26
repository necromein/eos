<?php
session_start();
require_once __DIR__ . '/../boot.php';

$rsurname = $_POST['rsurname'] ?? null;
$rname = $_POST['rname'] ?? null;
$rpatr = $_POST['rpatr'] ?? null;
$remail = $_POST['remail'] ?? null;
$rlogin = $_POST['rlogin'] ?? null;
$rpassword = $_POST['rpassword'] ?? null;
$send2 = $_POST['send2'] ?? null;

$_SESSION['validation'] = [];

if (isset($send2)) {
    $stmt = pdo()->prepare('SELECT * FROM `users` WHERE `login` = :login');
    $stmt->execute(['login' => $rlogin]);
    if ($stmt->rowCount() > 0) {
        flash('Это имя пользователя уже занято.');
        header('Location: /authorization.php');
        die();
    }

    $stmt = pdo()->prepare(
        'INSERT INTO `users` (`surname`, `name`, `patronymic`, `email`, `login`, `password`, `role`, `avatar`) VALUES (:surname, :name, :patronymic, :email, :login, :password, :role, null)'
    );
    $stmt->execute([
        'surname' => $rsurname,
        'name' => $rname,
        'patronymic' => $rpatr,
        'email' => $remail,
        'login' => $rlogin,
        'password' => password_hash($rpassword, PASSWORD_DEFAULT),
        'role' => 1, // роль по умолчанию
    ]);

    $user_id = pdo()->lastInsertId();

    // обновляем данные пользователя в сессии
    $_SESSION['user'] = [
        "id_user" => pdo()->lastInsertId(),
        "role" => '1',  // роль по умолчанию
        "name" => $rname,
        "surname" => $rsurname,
        "patronymic" => $rpatr,
        "login" => $rlogin,
        "password" => $rpassword,
        "email" => $remail 
    ];

    // редирект

    if (!empty($_SESSION['previous_page'])) {
        header('Location: ' . $_SESSION['previous_page']);
    } else {
        header('Location: /index.php');
    }
}
?>