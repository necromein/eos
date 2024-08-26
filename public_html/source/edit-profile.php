<?php
session_start();
session_regenerate_id(true);
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/actions/helper.php';

if (isset($_POST['edit_user'])) {
    $userId = $_SESSION['user']['id_user'];

    // Обновление информации о пользователе
    updateUserInfo($connect, $userId);

    // Обновление аватара
    updateAvatar($connect, $userId);

    header('Location: /profile.php');
    exit();
}

function updateUserInfo($connect, $userId) {
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $password = $_POST['password'];

    $updateUserQuery = "UPDATE users SET surname = '$surname', name = '$name', patronymic = '$patronymic', email = '$email', login = '$login'";

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $updateUserQuery .= ", password = '$hashedPassword'";
    }

    $updateUserQuery .= " WHERE id_user = $userId";
    $connect->query($updateUserQuery);

    $_SESSION['user']['surname'] = $surname;
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['patronymic'] = $patronymic;
}

function updateAvatar($connect, $userId) {
    if ($_FILES['avatar']['size'] > 0) {
        $avatarFileName = $_FILES['avatar']['name'];
        $avatarTempName = $_FILES['avatar']['tmp_name'];
        $avatarUploadDir = __DIR__ . '/../uploads/';
        $avatarUploadPath = $avatarUploadDir . $avatarFileName;

        if (move_uploaded_file($avatarTempName, $avatarUploadPath)) {
            $avatarUploadPath = '/uploads/' . $avatarFileName; // путь для сохранения загруженного аватара
            $updateAvatarQuery = "UPDATE users SET avatar = '$avatarUploadPath' WHERE id_user = $userId";
            $connect->query($updateAvatarQuery);

            $_SESSION['user']['avatar'] = $avatarUploadPath;
        } else {
            echo "Ошибка при загрузке файла.";
            exit();
        }
    }
}
?>
