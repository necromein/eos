<?php
session_start();
session_regenerate_id(true);
error_log(print_r($_SESSION, true));
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/actions/helper.php';

if (!$_SESSION['user']) {
    header('Location: index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $courseId = $_POST['update'];
    $courseTitle = $_POST['course_title'];
    $courseBrief = $_POST['course_brief'];
    $courseDescription = $_POST['course_description'];

    try {
        // создаем подключение PDO
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // обновляем информацию о курсе
        $updateCourseQuery = "UPDATE courses SET course_title = :courseTitle, course_brief = :courseBrief, course_description = :courseDescription WHERE id_course = :courseId";
        $updateCourseStmt = $pdo->prepare($updateCourseQuery);
        $updateCourseStmt->execute(array(
            ':courseTitle' => $courseTitle,
            ':courseBrief' => $courseBrief,
            ':courseDescription' => $courseDescription,
            ':courseId' => $courseId
        ));

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'module_') === 0) {
                $moduleId = substr($key, 7);
                $moduleTitle = $value;

                if ($moduleId == 'new') {
                    // добавление нового модуля
                    $insertModuleQuery = "INSERT INTO module (module_title, parent_course) VALUES (:moduleTitle, :courseId)";
                    $insertModuleStmt = $pdo->prepare($insertModuleQuery);
                    $insertModuleStmt->execute(array(
                        ':moduleTitle' => $moduleTitle,
                        ':courseId' => $courseId
                    ));
                    $moduleId = $pdo->lastInsertId();
                } else {
                    // обновление существующего модуля
                    $updateModuleQuery = "UPDATE module SET module_title = :moduleTitle WHERE id_module = :moduleId AND parent_course = :courseId";
                    $updateModuleStmt = $pdo->prepare($updateModuleQuery);
                    $updateModuleStmt->execute(array(
                        ':moduleTitle' => $moduleTitle,
                        ':moduleId' => $moduleId,
                        ':courseId' => $courseId
                    ));
                }
            } elseif (strpos($key, 'lesson_') === 0) {
                $parts = explode('_', substr($key, 7));
                $moduleId = $parts[0];
                $lessonId = $parts[1];
                $lessonTitle = $value;

                if ($moduleId == 'new' && $lessonId == 'new') {
                    // добавление нового урока для нового модуля
                    $insertLessonQuery = "INSERT INTO lessons (lesson_title, parent_module) VALUES (:lessonTitle, :moduleId)";
                    $insertLessonStmt = $pdo->prepare($insertLessonQuery);
                    $insertLessonStmt->execute(array(
                        ':lessonTitle' => $lessonTitle,
                        ':moduleId' => $moduleId
                    ));
                } else {
                    // обновление существующего урока
                    $updateLessonQuery = "UPDATE lessons SET lesson_title = :lessonTitle WHERE id_lesson = :lessonId AND parent_module = :moduleId";
                    $updateLessonStmt = $pdo->prepare($updateLessonQuery);
                    $updateLessonStmt->execute(array(
                        ':lessonTitle' => $lessonTitle,
                        ':lessonId' => $lessonId,
                        ':moduleId' => $moduleId
                    ));
                }
            }
        }

        header("Location: /course-info.php?course_id=$courseId");
        exit();
    } catch (PDOException $e) {
        echo "не удалось обновить курс: " . $e->getMessage();
    }
} else {
    header('Location: index.php');
    exit();
}
?>
