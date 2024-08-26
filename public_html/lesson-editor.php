<?php
session_start();
require_once 'dbconnect.php';

if ($_SESSION['user']) {
    $userId = $_SESSION['user']['id_user'];

    $query = "SELECT * FROM users WHERE id_user = $userId";
    $result = $connect->query($query);

    if ($result->rowCount() > 0) {
        $userData = $result->fetch(pdo::FETCH_ASSOC);
        $_SESSION['user'] = $userData;
    }
}

$userRole = $_SESSION['user']['role'];
$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];

if (isset($_GET['id_course']) && isset($_GET['id_lesson'])) {
    $courseId = $_GET['id_course'];
    $lessonId = $_GET['id_lesson'];

    // получаем данные о курсе
    $courseQuery = "SELECT course_title FROM courses WHERE id_course = $courseId";
    $courseResult = $connect->query($courseQuery);

    if ($courseResult->rowCount() > 0) {
        $courseData = $courseResult->fetch(pdo::FETCH_ASSOC);
        $courseTitle = $courseData['course_title'];
    } else {
        echo 'Курс не найден';
        exit();
    }

    // получаем данные об уроке
    $lessonQuery = "SELECT lesson_title FROM lessons WHERE id_lesson = $lessonId";
    $lessonResult = $connect->query($lessonQuery);

    if ($lessonResult->rowCount() > 0) {
        $lessonData = $lessonResult->fetch(pdo::FETCH_ASSOC);
        $lessonTitle = $lessonData['lesson_title'];
    } else {
        echo 'Урок не найден';
        exit();
    }
} else {
    echo 'Неверный запрос';
    exit();
}

$courseQuery = "SELECT * FROM courses WHERE id_course = $courseId";
$courseResult = $connect->query($courseQuery);

if ($courseResult->rowCount() > 0) {
    $courseData = $courseResult->fetch(pdo::FETCH_ASSOC);
    // является ли текущий пользователь автором курса
    if (isset($_SESSION['user']['id_user'])) {
        if ($courseData['author'] == $_SESSION['user']['id_user']) {
            $Author = true;
        } else {
            $Author = false;
        }
    }
} else {
    echo 'нет доступа';
}

$moduleQuery = "SELECT * FROM module WHERE parent_course = $courseId";
$moduleResult = $connect->query($moduleQuery);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактирование урока</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/lesson.css">
    <link rel="shortcut icon" href="img/star.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/ui/trumbowyg.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/trumbowyg.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.25.1/plugins/upload/trumbowyg.upload.min.js"></script>
</head>

<?php include 'header.php'; ?>
<style>
    .ql-align-center {
        text-align: center !important;
    }

    .ql-align-left {
        text-align: left !important;
    }

    .ql-align-right {
        text-align: right !important;
    }

    .ql-align-justify {
        text-align: justify !important;
    }
</style>

<body>
    <div class="heading">
        <p>
            <?php echo $lessonTitle; ?>
        </p>
    </div>
    <div class="lesson-main">
        <div class="sidebar">
            <div style="width: 100%;">
                <h4>
                    <?php echo $courseTitle; ?>
                </h4>
            </div>
            <div style="width: 100%;">
                <?php
                $moduleQuery = "SELECT * FROM module WHERE parent_course = $courseId";
                $moduleResult = $connect->query($moduleQuery);

                if ($moduleResult->rowCount() > 0) {
                    while ($moduleData = $moduleResult->fetch(pdo::FETCH_ASSOC)) {
                        echo '<div class="module">';
                        echo '<div class="module-link"><p class="module-t">' .
                            $moduleData['module_title'] .
                            '</p></div>';
                        $moduleId = $moduleData['id_module'];
                        $lessonQuery = "SELECT * FROM lessons WHERE parent_module = $moduleId";
                        $lessonResult = $connect->query($lessonQuery);

                        if ($lessonResult === false) {
                            echo 'Error in lesson query: ' . $e->getMessage();
                        } else {
                            while ($lessonData = $lessonResult->fetch(pdo::FETCH_ASSOC)) {
                                echo '<div class="lesson-link"><img src="img/document-text.svg"><a href="lesson.php?id_course=' .
                                    $courseId .
                                    '&id_lesson=' .
                                    $lessonData['id_lesson'] .
                                    '">
                            <p class="lesson-t">' .
                                    $lessonData['lesson_title'] .
                                    '</p></a></div>';
                            }
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
            <div class="content-container" style="height: fit-content;">
                <div class="content">
                    <h2>
                        Редактирование урока
                    </h2>
                    <div id="editor"></div>
                </div>
                <div>
                    <?php if ($userRole == '2') {
                        if ($Author) {
                            echo '<div><a onclick="saveLessonContent()" class="button-box">Сохранить</a></div>';
                        }
                    } ?>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>

    <script>
        $('#editor').trumbowyg({
            btns: [
                ['viewHTML'],
                ['formatting'],
                'btnGrp-semantic',
                ['superscript', 'subscript'],
                ['link'],
                ['insertImage'],
                ['insertVideo'],
                ['upload'],
                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                ['unorderedList', 'orderedList'],
                ['horizontalRule'],
                ['removeformat'],
                ['fullscreen']
            ],
            autogrow: true,
            plugins: {
                upload: {
                    serverPath: 'path_to_your_upload_handler',
                    fileFieldName: 'image'
                }
            }
        });

        function saveLessonContent() {
            var lessonContent = $('#editor').trumbowyg('html');

            $.ajax({
                type: "POST",
                url: "source/save_lesson_content.php",
                data: {
                    id_course: <?php echo $courseId; ?>,
                    id_lesson: <?php echo $lessonId; ?>,
                    lesson_content: lessonContent
                },
                success: function (response) {
                    alert('Сохранено успешно');
                    window.location.href = "lesson.php?id_course=<?php echo $courseId; ?>&id_lesson=<?php echo $lessonId; ?>";
                },
                error: function (error) {
                    alert('Ошибка при сохранении');
                }
            });
        }

        // Дополнительные стили для вставленных изображений
        $(document).on('tbwchange', function () {
            $('img').css({
                'max-width': '50%',
                'height': 'auto'
            });
        });
    </script>
</body>

</html>