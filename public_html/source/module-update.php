<?php
session_start();
require_once 'dbconnect.php';

if (!$_SESSION['user']) {
    header('Location: index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $courseId = $_POST['update'];

    $moduleQuery = "SELECT * FROM module WHERE parent_course = $courseId";
    $moduleResult = $connect->query($moduleQuery);

    $modulesData = array();

    if ($moduleResult === false) {
        die("Error: " . $e->getMessage());
    }

    if ($moduleResult->rowCount() > 0) {
        while ($moduleData = $moduleResult->fetch(pdo::FETCH_ASSOC)) {
            $moduleId = $moduleData['id_module'];

            // получаем данные об уроках для каждого модуля
            $lessonQuery = "SELECT * FROM lessons WHERE parent_module = $moduleId";
            $lessonResult = $connect->query($lessonQuery);

            if ($lessonResult === false) {
                die("Error: " . $e->getMessage());
            }

            $lessonsData = array();
            while ($lessonData = $lessonResult->fetch(pdo::FETCH_ASSOC)) {
                $lessonsData[] = $lessonData;
            }

            // сохраняем данные о модуле и его уроках
            $moduleData['lessons'] = $lessonsData;
            $modulesData[] = $moduleData;
        }
    }

    foreach ($modulesData as $module) {
        $moduleId = $module['id_module'];
        $moduleTitle = $_POST["module_$moduleId"];

        // название модуля
        $updateModuleQuery = "UPDATE module SET module_title = '$moduleTitle' WHERE id_module = $moduleId";
        $result = $connect->query($updateModuleQuery);

        if ($result === false) {
            die("Error: " . $e->getMessage());
        }

        // уроки внутри модуля
        foreach ($module['lessons'] as $lesson) {
            $lessonId = $lesson['id_lesson'];
            $lessonTitle = $_POST["lesson_$moduleId_$lessonId"];

            // название урока
            $updateLessonQuery = "UPDATE lessons SET lesson_title = '$lessonTitle' WHERE id_lesson = $lessonId";
            $result = $connect->query($updateLessonQuery);

            if ($result === false) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    echo "Курс успешно обновлен!";
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_module'])) {
    $moduleId = $_POST['remove_module'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_module'])) {
        $newModuleTitle = $_POST['new_module_title'];
    
        // добавляем новый модуль
        $insertModuleQuery = "INSERT INTO module (parent_course, module_title) VALUES ($courseId, '$newModuleTitle')";
        $connect->query($insertModuleQuery);
    
        echo "Модуль успешно добавлен!";
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_lesson'])) {
        $moduleId = $_POST['add_lesson'];
        $newLessonTitle = $_POST["new_lesson_title_$moduleId"];
    
        // добавляем новый урок в модуль
        $insertLessonQuery = "INSERT INTO lessons (parent_module, lesson_title) VALUES ($moduleId, '$newLessonTitle')";
        $connect->query($insertLessonQuery);
    
        echo "Урок успешно добавлен!";
        exit;
    }


    // удаляем модуль
    $deleteModuleQuery = "DELETE FROM module WHERE id_module = $moduleId";
    $result = $connect->query($deleteModuleQuery);

    if ($result === false) {
        die("ошибка удаления модуля: " . $e->getMessage());
    }

    // удаляем уроки связанные с модулем
    $deleteLessonsQuery = "DELETE FROM lessons WHERE parent_module = $moduleId";
    $result = $connect->query($deleteLessonsQuery);

    if ($result === false) {
        die("ошибка удаления уроков: " . $e->getMessage());
    }

    echo "Модуль успешно удален!";
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_lesson'])) {
    $lessonId = $_POST['remove_lesson'];
 
    // удаляем урок
    $deleteLessonQuery = "DELETE FROM lessons WHERE id_lesson = $lessonId";
    $result = $connect->query($deleteLessonQuery);

    if ($result === false) {
        die("ошибка удаления урока: " . $e->getMessage());
    }

    echo "Урок успешно удален!";
} else {
    echo "Неверный запрос.";
}
?>
