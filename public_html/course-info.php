<?php
session_start();
require_once 'dbconnect.php';

if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id_user'];

    $query = "SELECT * FROM users WHERE id_user = $userId";
    $result = $connect->query($query);

    if ($result->rowCount() > 0) {
        $userData = $result->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user'] = $userData;
    }
}

if (isset($_SESSION['user']['role'])) {
    $userRole = $_SESSION['user']['role'];
}

$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
$previous_page = $_SESSION['previous_page'] ?? 'index.php';

$courseId = isset($_GET['course_id']) ? $_GET['course_id'] : null;

if (!$courseId) {
    echo "Не удалось получить идентификатор курса";
    exit;
}

// Увеличиваем счетчик просмотров
$updateViewsQuery = "UPDATE courses SET views_count = views_count + 1 WHERE id_course = :courseId";
$stmt = $connect->prepare($updateViewsQuery);
$stmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
$stmt->execute();

if ($stmt) {
    //echo "Счетчик просмотров успешно обновлен!";
} else {
    echo "Ошибка при обновлении счетчика просмотров";
}

$courseQuery = "SELECT * FROM courses WHERE id_course = $courseId";
$courseResult = $connect->query($courseQuery);

if ($courseResult->rowCount() > 0) {
    $courseData = $courseResult->fetch(PDO::FETCH_ASSOC);
    // является ли текущий пользователь автором курса
    if (isset($_SESSION['user']['id_user'])) {
        if ($courseData['author'] == $_SESSION['user']['id_user']) {
            $Author = true;
        } else {
            $Author = false;
        }
    }
} else {
    echo "нет доступа";
}

$moduleQuery = "SELECT * FROM module WHERE parent_course = $courseId";
$moduleResult = $connect->query($moduleQuery);

$isEnrolledQuery = "SELECT COUNT(*) FROM user_course WHERE user_id = :userId AND course_id = :courseId";
$isEnrolledStmt = $connect->prepare($isEnrolledQuery);
$isEnrolledStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$isEnrolledStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
$isEnrolledStmt->execute();
$isEnrolled = $isEnrolledStmt->fetchColumn() > 0;

$lastLessonQuery = "SELECT last_lesson_id FROM user_course WHERE user_id = :userId AND course_id = :courseId";
$lastLessonStmt = $connect->prepare($lastLessonQuery);
$lastLessonStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$lastLessonStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
$lastLessonStmt->execute();
$lastLessonId = $lastLessonStmt->fetchColumn();

if (isset($_POST['enroll_course'])) {
    if (!$isEnrolled) {
        // Добавляем запись пользователя на курс в базу данных
        $enrollCourseQuery = "INSERT INTO user_course (user_id, course_id, last_lesson_id) VALUES (:userId, :courseId, 0)";
        $enrollStmt = $connect->prepare($enrollCourseQuery);
        $enrollStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $enrollStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
        $enrollStmt->execute();
    }

    if ($enrollStmt || $isEnrolled) {
        // Получаем идентификатор первого урока курса
        if ($lastLessonId > 0) {
            $lessonId = $lastLessonId;
        } else {
            $firstLessonQuery = "SELECT id_lesson FROM lessons WHERE parent_module IN (SELECT id_module FROM module WHERE parent_course = $courseId) ORDER BY id_lesson ASC LIMIT 1";
            $firstLessonResult = $connect->query($firstLessonQuery);

            if ($firstLessonResult->rowCount() > 0) {
                $firstLessonData = $firstLessonResult->fetch(PDO::FETCH_ASSOC);
                $lessonId = $firstLessonData['id_lesson'];
            } else {
                echo "Уроки не найдены";
                exit;
            }
        }

        // Выполняем редирект на страницу урока
        header("Location: lesson.php?id_course=$courseId&id_lesson=$lessonId");
        exit;
    } else {
        echo "Ошибка при добавлении записи пользователя на курс";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?php echo $courseData['course_title']; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/catalogue.css">
    <link rel="shortcut icon" href="img/star.svg" type="image/x-icon">
    <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">-->
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>
<?php include 'header.php'; ?>

<body>
    <?php include 'modal.php'; ?>
    <section>
        <div class="topic-title">
            <img class="star-img" src="img/star.svg">
            <h3 class="regtext">
                <?php echo $courseData['course_title']; ?>
            </h3>
        </div>

        <div class="course-brief">
            <p style="text-align: justify;">
                <?php echo $courseData['course_brief']; ?>
            </p>
        </div>

        <div>
            <h4>О курсе</h4>
            <p style="text-align: justify;">
                <?php echo nl2br($courseData['course_description']); ?>
            </p>
        </div>

        <div class="content-container">
            <h4>Содержание</h4>
            <div class="modules-container">
                <?php
                if ($moduleResult->rowCount() > 0) {
                    $firstModule = true;
                    while ($moduleData = $moduleResult->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="module' . ($firstModule ? '' : ' hidden') . '">';
                        echo '<p class="module-t">' . $moduleData['module_title'] . '</p>';
                        $moduleId = $moduleData['id_module'];
                        $lessonQuery = "SELECT * FROM lessons WHERE parent_module = $moduleId";
                        $lessonResult = $connect->query($lessonQuery);
                        if ($lessonResult === false) {
                            echo "ошибка: " . $e->getMessage();
                        } else {
                            while ($lessonData = $lessonResult->fetch(PDO::FETCH_ASSOC)) {
                                echo '<a href="lesson.php?id_course=' . $courseId . '&id_lesson=' . $lessonData['id_lesson'] . '"><p class="lesson-t">' . $lessonData['lesson_title'] . '</p></a>';
                            }
                        }
                        echo '</div>';
                        $firstModule = false;
                    }
                } else {
                    echo "<p>Вы не добавили ни одного модуля<p>";
                }
                ?>
                <div class="arrow-container">
                    <a id="toggleArrow" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="Развернуть список"><img src="img/arrow.svg" height="10px"></a>
                    <a id="toggleArrowReversed" class="hidden"><img src="img/reversedarrow.svg" height="10px"></a>
                </div>
            </div>
        </div>

        <div class="course-book" style="display: flex; gap: 1vw;">
            <?php
            if (isset($userRole)) {
                switch ($userRole) {
                    case '1':
                    case '2':
                    case '0':
                        echo '<form method="post"><button type="submit" name="enroll_course" class="button-box">Пройти курс</button></form>';
                        if ($userRole == '2' && $Author) {
                            echo '<button class="button-box"><a href="course-editor.php?id_course=' . $courseId . '" >Редактировать</a></button>';
                            echo '<button class="button-box"><a href="source/delete-course.php?id_course=' . $courseId . '" >Удалить курс</a></button>';
                        }
                        break;
                    default:
                        break;
                }
            }
            ?>
        </div>

    </section>
    <?php include 'footer.php'; ?>
    <script src="js/script.js"></script>
    <script src="js/auth.js"></script>
    <script>
        var arrow = document.getElementById('toggleArrow');
        var reversedArrow = document.getElementById('toggleArrowReversed');
        var modules = document.querySelectorAll('.modules-container .module');

        arrow.addEventListener('click', function () {
            modules.forEach(function (module) {
                module.classList.remove('hidden');
            });

            arrow.classList.toggle('hidden');
            reversedArrow.classList.toggle('hidden');
        });

        reversedArrow.addEventListener('click', function () {
            for (var i = 1; i < modules.length; i++) {
                modules[i].classList.add('hidden');
            }

            arrow.classList.toggle('hidden');
            reversedArrow.classList.toggle('hidden');
        });

    </script>
</body>

</html>