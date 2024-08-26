<?php
session_start();
require_once __DIR__ . '/../dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $courseId = $_POST['id_course'];
    $lessonId = $_POST['id_lesson'];
    $lessonContent = $_POST['lesson_content'];

    // Очистка данных
    // $lessonContent = htmlspecialchars($lessonContent, ENT_QUOTES, 'UTF-8');

    // Сохранение данных в базу данных
    $query = "UPDATE lessons SET lesson_content = :lesson_content WHERE id_lesson = :id_lesson";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':lesson_content', $lessonContent);
    $stmt->bindParam(':id_lesson', $lessonId);
    if ($stmt->execute()) {
        echo 'Сохранено успешно';
    } else {
        echo 'Ошибка при сохранении';
    }
}
?>