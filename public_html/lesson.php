<?php
session_start();
require_once 'dbconnect.php';

// Проверка наличия пользователя в сессии
if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id_user'];

    // Запрос на получение данных пользователя
    $query = "SELECT * FROM users WHERE id_user = :userId";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user'] = $userData;
    }
}

if (isset($_SESSION['user']['role'])) {
    $userRole = $_SESSION['user']['role'];
}
$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
$previous_page = $_SESSION['previous_page'] ?? 'index.php';

// Обработка POST-запроса для завершения курса
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'])) {
    $courseId = $_POST['course_id'];
    $lessonId = $_POST['lesson_id'];

    // Обновляем прогресс пользователя до 100%
    $updateProgressQuery = "UPDATE user_progress SET progress = 100 WHERE user_id = :userId AND course_id = :courseId";
    $updateProgressStmt = $connect->prepare($updateProgressQuery);
    $updateProgressStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $updateProgressStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
    $updateProgressStmt->execute();

    // Перенаправление на страницу курса
    header("Location: course-info.php?course_id=$courseId");
    exit();
}

if (isset($_GET['id_course']) && isset($_GET['id_lesson'])) {
    $courseId = $_GET['id_course'];
    $lessonId = $_GET['id_lesson'];

    // Получаем данные о курсе
    $courseQuery = "SELECT course_title FROM courses WHERE id_course = :courseId";
    $courseStmt = $connect->prepare($courseQuery);
    $courseStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
    $courseStmt->execute();

    if ($courseStmt->rowCount() > 0) {
        $courseData = $courseStmt->fetch(PDO::FETCH_ASSOC);
        $courseTitle = $courseData['course_title'];
    } else {
        echo 'Курс не найден';
        exit();
    }

    // Получаем данные об уроке
    $lessonQuery = "SELECT lesson_title, lesson_content FROM lessons WHERE id_lesson = :lessonId";
    $lessonStmt = $connect->prepare($lessonQuery);
    $lessonStmt->bindParam(':lessonId', $lessonId, PDO::PARAM_INT);
    $lessonStmt->execute();

    if ($lessonStmt->rowCount() > 0) {
        $lessonData = $lessonStmt->fetch(PDO::FETCH_ASSOC);
        $lessonTitle = $lessonData['lesson_title'];
        $lessonContent = $lessonData['lesson_content'];
    } else {
        echo 'Урок не найден';
        exit();
    }
} else {
    echo 'Неверный запрос';
    exit();
}

// Получаем данные о курсе и проверяем является ли текущий пользователь автором курса
$courseQuery = "SELECT * FROM courses WHERE id_course = :courseId";
$courseStmt = $connect->prepare($courseQuery);
$courseStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
$courseStmt->execute();

if ($courseStmt->rowCount() > 0) {
    $courseData = $courseStmt->fetch(PDO::FETCH_ASSOC);
    $Author = ($courseData['author'] == $userId);
} else {
    echo 'нет доступа';
    exit();
}

$moduleQuery = "SELECT * FROM module WHERE parent_course = :courseId";
$moduleStmt = $connect->prepare($moduleQuery);
$moduleStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
$moduleStmt->execute();
$moduleResult = $moduleStmt;

// Запрос для получения количества уроков в курсе
$totalLessonsQuery = "SELECT COUNT(*) as total_lessons FROM lessons WHERE parent_module IN (SELECT id_module FROM module WHERE parent_course = :courseId)";
$totalLessonsStmt = $connect->prepare($totalLessonsQuery);
$totalLessonsStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
$totalLessonsStmt->execute();
$totalLessonsData = $totalLessonsStmt->fetch(PDO::FETCH_ASSOC);
$totalLessons = $totalLessonsData['total_lessons'];

// Запрос для получения количества завершенных уроков пользователя в данном курсе
$completedLessonsQuery = "SELECT COUNT(*) as completed_lessons FROM user_progress WHERE user_id = :userId AND course_id = :courseId";
$completedLessonsStmt = $connect->prepare($completedLessonsQuery);
$completedLessonsStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$completedLessonsStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
$completedLessonsStmt->execute();
$completedLessonsData = $completedLessonsStmt->fetch(PDO::FETCH_ASSOC);
$completedLessons = $completedLessonsData['completed_lessons'];

// Рассчитываем процент прогресса
$progressPercentage = ($totalLessons > 0) ? ($completedLessons / $totalLessons) * 100 : 0;

// Обновляем запись о прогрессе пользователя для текущего урока
$updateProgressQuery = "INSERT INTO user_progress (user_id, course_id, lesson_id, progress) VALUES (:userId, :courseId, :lessonId, :progress) ON DUPLICATE KEY UPDATE progress = VALUES(progress)";
$updateProgressStmt = $connect->prepare($updateProgressQuery);
$updateProgressStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$updateProgressStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
$updateProgressStmt->bindParam(':lessonId', $lessonId, PDO::PARAM_INT);
$updateProgressStmt->bindParam(':progress', $progressPercentage, PDO::PARAM_INT);
$updateProgressStmt->execute();

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title><?php echo $lessonData['lesson_title']; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/lesson.css">
    <link rel="shortcut icon" href="img/star.svg" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <style>
        /* Стили для кнопок стрелок */
        .toggle-sidebar {
            position: absolute;
            top: 50%;
            left: 1vw; /* Расстояние от левого края экрана */
            transform: translateY(-50%);
            background-color: #fff;
            padding: 10px;
            cursor: pointer;
            z-index: 1000;
            display: block;
            /* Всегда отображаем кнопки */
        }

        .toggle-sidebar img {
            height: 30px;
        }

        .toggle-sidebar.show {
            display: block;
            /* Показываем кнопки при определенных условиях */
        }
    </style>
</head>
<?php include 'header.php'; ?>

<body>
    <div class="toggle-sidebar" id="showSidebar">
        <img src="img/arrow-right.svg" alt="Показать меню">
    </div>
    <div class="toggle-sidebar" id="hideSidebar" style="left: 18vw;">
        <img src="img/arrow-left.svg" alt="Скрыть меню">
    </div>
    <div class="heading">
        <p><?php echo $lessonTitle; ?></p>
    </div>
    <div class="lesson-main">
        <div class="sidebar">
            <div style="width: 100%; display: flex; align-items: center; margin: 10px 10px;">
                <a href="course-info.php?course_id=<?php echo $courseId; ?>"><span id="left" class=" "><img
                            src="img/arrow-left.svg" style="height: 1.5rem;"></span></a>
                <h4><?php echo $courseTitle; ?></h4>
            </div>
            <div style="width: 100%;">
                <?php
                if ($moduleResult->rowCount() > 0) {
                    while ($moduleData = $moduleResult->fetch(PDO::FETCH_ASSOC)) {
                        echo '<div class="module">';
                        echo '<div class="module-link"><p class="module-t">' . $moduleData['module_title'] . '</p></div>';
                        $moduleId = $moduleData['id_module'];
                        $lessonQuery = "SELECT * FROM lessons WHERE parent_module = :moduleId";
                        $lessonStmt = $connect->prepare($lessonQuery);
                        $lessonStmt->bindParam(':moduleId', $moduleId, PDO::PARAM_INT);
                        $lessonStmt->execute();
                        $lessonResult = $lessonStmt;

                        if ($lessonResult->rowCount() > 0) {
                            while ($lessonData = $lessonResult->fetch(PDO::FETCH_ASSOC)) {
                                // Проверка роли пользователя перед выводом ссылок на уроки
                                if ($userRole != '1') {
                                    echo '<div class="lesson-link"><img src="img/document-text.svg"><a href="lesson.php?id_course=' . $courseId . '&id_lesson=' . $lessonData['id_lesson'] . '">
                        <p class="lesson-t">' . $lessonData['lesson_title'] . '</p></a></div>';
                                } else {
                                    echo '<div class="lesson-link"><img src="img/document-text.svg">
                        <p class="lesson-t">' . $lessonData['lesson_title'] . '</p></div>';
                                }
                            }
                        } else {
                            echo 'ошибка: ' . $e->getMessage();
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<p>Вы не добавили ни одного модуля<p>';
                }
                ?>
            </div>
        </div>
        <div class="content-section">
            <div class="content-container">
                <div class="content">
                    <?php
                    echo $lessonContent ? $lessonContent : '<br><p>Здесь пока пусто.<p>';
                    ?>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <?php if ($userRole == '2' && $Author) { ?>
                            <a href="lesson-editor.php?id_course=<?php echo $courseId; ?>&id_lesson=<?php echo $lessonId; ?>"
                                class="button-box">Редактировать</a>
                        <?php } ?>
                    </div>
                    <div>
                        <?php
                        // Проверяем, есть ли следующий урок в курсе
                        $nextLessonQuery = "SELECT id_lesson FROM lessons WHERE parent_module IN (SELECT id_module FROM module WHERE parent_course = :courseId) AND id_lesson > :lessonId ORDER BY id_lesson ASC LIMIT 1";
                        $nextLessonStmt = $connect->prepare($nextLessonQuery);
                        $nextLessonStmt->bindParam(':courseId', $courseId, PDO::PARAM_INT);
                        $nextLessonStmt->bindParam(':lessonId', $lessonId, PDO::PARAM_INT);
                        $nextLessonStmt->execute();
                        $nextLessonData = $nextLessonStmt->fetch(PDO::FETCH_ASSOC);

                        if ($nextLessonData) {
                            echo '<a href="lesson.php?id_course=' . $courseId . '&id_lesson=' . $nextLessonData['id_lesson'] . '" class="button-box">Следующий урок</a>';
                        } else {
                            echo '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post" style="display: inline;">
                                    <input type="hidden" name="course_id" value="' . $courseId . '">
                                    <input type="hidden" name="lesson_id" value="' . $lessonId . '">
                                    <button type="submit" class="button-box">Завершить курс</button>
                                  </form>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
<script>
    $(document).ready(function () {
        // Показать боковое меню
        $('#showSidebar').click(function () {
            $('.sidebar').show();
            $('.toggle-sidebar').hide();
            $('#hideSidebar').show();
        });

        // Скрыть боковое меню
        $('#hideSidebar').click(function () {
            $('.sidebar').hide();
            $('.toggle-sidebar').hide();
            $('#showSidebar').show();
        });

        // Проверяем размер экрана при загрузке страницы
        checkWindowSize();

        // Функция для проверки размера экрана и скрытия меню при необходимости
        function checkWindowSize() {
            if ($(window).width() < 1200) {
                $('.sidebar').hide();
                $('#showSidebar').show(); // Показываем кнопку для показа меню
                $('#hideSidebar').hide(); // Скрываем кнопку для скрытия меню
            } else {
                $('.sidebar').show();
                $('#showSidebar').hide();
                $('#hideSidebar').hide();
            }
        }

        // Обработчик изменения размера окна
        $(window).resize(function () {
            checkWindowSize();
        });
    });
</script>

</html>
