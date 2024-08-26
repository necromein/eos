<?php
session_start();
require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/actions/helper.php';

if (!isset($_SESSION['user'])) {
    //header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // получаем данные из формы
    $course_title = $_POST['course_title'];
    $course_brief = $_POST['course_brief'];
    $course_description = $_POST['course_description'];
    $difficulty_lvl = $_POST['difficulty_lvl'];
    $author_id = $_SESSION['user']['id_user'];

    // подготовленный запрос для вставки данных в таблицу courses
    $course_query = "INSERT INTO courses (course_title, course_brief, course_description, difficulty_lvl, author, views_count) 
                     VALUES (:course_title, :course_brief, :course_description, :difficulty_lvl, :author_id, 0)";
    $stmt = $connect->prepare($course_query);
    $stmt->execute([
        ':course_title' => $course_title,
        ':course_brief' => $course_brief,
        ':course_description' => $course_description,
        ':difficulty_lvl' => $difficulty_lvl,
        ':author_id' => $author_id
    ]);

    if ($stmt) {
        $parent_course_id = $connect->lastInsertId();

        $moduleCounter = 1;
        while (isset($_POST["module_$moduleCounter"])) {
            $module_title = $_POST["module_$moduleCounter"];
            $module_query = "INSERT INTO module (module_title, parent_course) VALUES (:module_title, :parent_course_id)";
            $stmt = $connect->prepare($module_query);
            $stmt->execute([
                ':module_title' => $module_title,
                ':parent_course_id' => $parent_course_id
            ]);

            if ($stmt) {
                $parent_module_id = $connect->lastInsertId();

                $lessonCounter = 1;
                while (isset($_POST["lesson_{$moduleCounter}_{$lessonCounter}"])) {
                    $lesson_title = $_POST["lesson_{$moduleCounter}_{$lessonCounter}"];
                    $lesson_query = "INSERT INTO lessons (lesson_title, parent_module) VALUES (:lesson_title, :parent_module_id)";
                    $stmt = $connect->prepare($lesson_query);
                    $stmt->execute([
                        ':lesson_title' => $lesson_title,
                        ':parent_module_id' => $parent_module_id
                    ]);

                    if (!$stmt) {
                        echo "не удалось добавить: " . $e->getMessage();
                    }

                    $lessonCounter++;
                }
            } else {
                echo "не удалось добавить: " . $e->getMessage();
            }

            $moduleCounter++;
        }

        header("Location: /course-info.php?course_id=$parent_course_id");
        exit();
    } else {
        echo "ошибка: " . $e->getMessage();
    }
}
?>