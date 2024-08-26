<?php
session_start();
session_regenerate_id(true);
error_log(print_r($_SESSION, true));
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/actions/helper.php';

if ($_SESSION['user']) {
    $userId = $_SESSION['user']['id_user'];
 
    $query = "SELECT * FROM users WHERE id_user = $userId";
    $result = $connect->query($query);

    if ($result->rowCount() > 0) {
        $userData = $result->fetch(pdo::FETCH_ASSOC);
        $_SESSION['user'] = $userData;
    }
}

$courseId = $_GET['id_course'];

// является ли текущий пользователь автором курса
$courseQuery = "SELECT * FROM courses WHERE id_course = $courseId AND author = $userId";
$courseResult = $connect->query($courseQuery);

if ($courseResult->rowCount() > 0) {
    // если пользователь является автором, удаляем курс
    $deleteCourseQuery = "DELETE FROM courses WHERE id_course = $courseId";
    $deleteCourseResult = $connect->query($deleteCourseQuery);

    if ($deleteCourseResult) {
        header("Location: /catalogue.php"); // перенаправляем пользователя после удаления
        exit();
    } else {
        echo "Ошибка при удалении курса: " . $e->getMessage();
    }
} else {
    echo "Невозможно удалить курс. Вы не являетесь автором.";
}
?>
