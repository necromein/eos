<?php
$response = array();

$login = $_POST["login"];
$password = $_POST["password"];

if (empty($login)) {
    $response["loginError"] = "Имя пользователя не может быть пустым";
} else if (strlen($login) < 2) {
    $response["loginError"] = "Имя пользователя должно содержать минимум 2 символа";
}

if (empty($password)) {
    $response["passwordError"] = "Пароль не может быть пустым";
} else if (strlen($password) < 6) {
    $response["passwordError"] = "Пароль должен содержать минимум 6 символов";
}

$email = $_POST["email"];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response["emailError"] = "Некорректный формат email";
}

echo json_encode($response);
?>
