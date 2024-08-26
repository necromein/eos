<?php
session_start();
require_once 'dbconnect.php';

if (!$_SESSION['user']) {
    header('Location: index.php');
}

$userRole = $_SESSION['user']['role'];

$courseId = $_GET['id_course'];
$courseQuery = "SELECT * FROM courses WHERE id_course = $courseId";
$courseResult = $connect->query($courseQuery);

if ($courseResult->rowCount() > 0) {
    $courseData = $courseResult->fetch(PDO::FETCH_ASSOC);
} else {
    echo 'Курс не найден';
}

$moduleQuery = "SELECT * FROM module WHERE parent_course = $courseId";
$moduleResult = $connect->query($moduleQuery);

$modulesData = [];
 
if ($moduleResult->rowCount() > 0) {
    while ($moduleData = $moduleResult->fetch(PDO::FETCH_ASSOC)) {
        $moduleId = $moduleData['id_module'];

        $lessonQuery = "SELECT * FROM lessons WHERE parent_module = $moduleId";
        $lessonResult = $connect->query($lessonQuery);

        $lessonsData = [];
        while ($lessonData = $lessonResult->fetch(PDO::FETCH_ASSOC)) {
            $lessonsData[] = $lessonData;
        }

        $moduleData['lessons'] = $lessonsData;
        $modulesData[] = $moduleData;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Редактирование курса</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/constructor.css">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>
<?php include 'header.php'; ?>
<style>
    .input-box {
        box-sizing: border-box;
        min-width: 14vw;
    }

    .auto-expand {
        resize: none;
        overflow: hidden;
    }


    select:hover,
    select:focus {
        border-radius: 30px !important;
    }
</style>

<body>
    <section id="constructor">
        <div class="topic-title">
            <img class="star-img" src="img/star.svg">
            <h2 class="regtext">Конструктор курсов</h2>
            <!-- <h2 class="inverse">курсов</h2> -->
        </div>
        <div>
            <h3>Редактировать курс</h3>
            <form action="source/course-update.php" method="POST">
                <div class="const-cont">
                    <div>
                        <!-- <label for="">Название курса</label> -->
                        <input class="input-box" type="text" name="course_title" value="<?php echo $courseData['course_title']; ?>" required>
                    </div>
                    <div>
                        <!-- <label for="">Краткое описание курса</label> -->
                        <textarea class="input-box auto-expand" style="min-height:4vh; width:60vw;" name="course_brief" required><?php echo $courseData['course_brief']; ?></textarea>
                    </div>
                    <div>
                        <!-- <label for="c_description">Полное описание курса</label> -->
                        <textarea class="input-box auto-expand" style="min-height:4vh; width:60vw;" name="course_description" id="c_description" required><?php echo $courseData['course_description']; ?></textarea>
                    </div>
                </div>
                <h3>Содержание курса</h3>
                <div>
                    <div class="const-cont">
                        <ol id="modulesList">
                            <?php foreach ($modulesData as $module): ?>
                                <li>
                                    <div style="margin: 1vh;">
                                        <input class="input-box" type="text" name="module_<?php echo $module[
                                            'id_module'
                                        ]; ?>" value="<?php echo $module[
                                             'module_title'
                                         ]; ?>" placeholder="Название модуля" data-module-number="<?php echo $module[
                                              'id_module'
                                          ]; ?>">
                                        <button type="button" class="remove_module button-box"
                                            data-module="<?php echo $module['id_module']; ?>">-</button>
                                    </div>
                                    <ol>
                                        <?php foreach ($module['lessons'] as $lesson): ?>
                                            <li>
                                                <div style="margin: 1vh;">
                                                    <input class="input-box" type="text" name="lesson_<?php echo $module[
                                                        'id_module'
                                                    ]; ?>_<?php echo $lesson[
                                                         'id_lesson'
                                                     ]; ?>" value="<?php echo $lesson[
                                                          'lesson_title'
                                                      ]; ?>" placeholder="Название урока" data-lesson-number="<?php echo $lesson[
                                                           'id_lesson'
                                                       ]; ?>">
                                                    <button type="button" class="add_lesson button-box" data-module="<?php echo $module[
                                                        'id_module'
                                                    ]; ?>" data-lesson="<?php echo $lesson[
                                                         'id_lesson'
                                                     ]; ?>">+</button>
                                                    <button type="button" class="remove_lesson button-box"
                                                        data-module="<?php echo $module['id_module']; ?>"
                                                        data-lesson="<?php echo $lesson['id_lesson']; ?>">-</button>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ol>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                        <div style="margin-bottom: 1vh;">
                            <button type="button" class="add_module button-box">Добавить модуль</button>
                        </div>
                    </div>

                </div>

                <button class="button-box" type="submit" name="update" value="<?php echo $courseId; ?>">Сохранить</button>
            </form>
        </div>
    </section>
    <?php include 'footer.php'; ?>
    <script src="js/addition.js"></script>
    <script>
        $(document).ready(function () {
            $('.auto-expand').on('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });

            $('.auto-expand').each(function () {
                this.style.height = (this.scrollHeight) + 'px';
            });
        });


        $(document).ready(function () {
            function adjustInputWidth(input) {
                var hiddenSpan = $('<span>').text(input.val()).css({
                    'position': 'absolute'
                });
                $('body').append(hiddenSpan);
                var textWidth = hiddenSpan[0].scrollWidth;
                hiddenSpan.remove();

                var viewportWidth = $(window).width();
                var maxWidthPercentage = 60;
                var padding = 20;
                var maxWidth = (viewportWidth * maxWidthPercentage) / 100 - padding;
                input.width(Math.min(textWidth, maxWidth));
            }

            $('input.input-box').on('input', function () {
                adjustInputWidth($(this));
            });
        });

        $(document).ready(function () {
            $('.auto-expand').on('input', function () {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        // удаление модулей и уроков
        $(document).ready(function () {
            $('.remove_module').click(function () {
                var moduleId = $(this).data('module');
                $.post('source/delete-course-item.php', { delete_module: moduleId }, function (data) {
                    console.log(data);
                    location.reload();
                });
            });

            $('.remove_lesson').click(function () {
                var moduleId = $(this).data('module');
                var lessonId = $(this).data('lesson');
                $.post('source/delete-course-item.php', { delete_lesson: lessonId }, function (data) {
                    console.log(data);
                    location.reload();
                });
            });
        });
    </script>
</body>

</html>
