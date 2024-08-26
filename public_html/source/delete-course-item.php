<?php
session_start();
require_once __DIR__ . '/../dbconnect.php';

if (isset($_POST['delete_module'])) {
    $moduleId = $_POST['delete_module'];
    
    // удаляем модуль из бд
    $deleteModuleQuery = "DELETE FROM module WHERE id_module = $moduleId";
    $connect->query($deleteModuleQuery);
 
    // удаляем связанные с этим модулем уроки
    $deleteLessonsQuery = "DELETE FROM lessons WHERE parent_module = $moduleId";
    $connect->query($deleteLessonsQuery);

    echo "модуль удален успешно";
}

if (isset($_POST['delete_lesson'])) {
    $lessonId = $_POST['delete_lesson'];
    
    // удаляем урок из бд
    $deleteLessonQuery = "DELETE FROM lessons WHERE id_lesson = $lessonId";
    $connect->query($deleteLessonQuery);

    echo "урок удален успешно";
}
?> 