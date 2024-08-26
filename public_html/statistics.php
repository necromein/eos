<?php
session_start();
require_once 'dbconnect.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$userRole = $_SESSION['user']['role'];
$userId = $_SESSION['user']['id_user'];

$progressQuery = "SELECT * FROM user_progress 
                  JOIN courses ON user_progress.course_id = courses.id_course
                  WHERE user_id = :userId";
$stmt = $connect->prepare($progressQuery);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$progressData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$progressInProgress = [];
$progressArchived = [];

$highestProgressByCourse = [];

foreach ($progressData as $progress) {
    $courseId = $progress['id_course'];
    $progressPercentage = $progress['progress'];

    // Ограничение значения прогресса до максимума 100%
    $progressPercentage = min($progressPercentage, 100);

    // Если для данного курса еще нет записи о самом высоком прогрессе или текущий прогресс выше, обновляем информацию
    if (!isset($highestProgressByCourse[$courseId]) || $progressPercentage > $highestProgressByCourse[$courseId]['progress']) {
        $highestProgressByCourse[$courseId] = [
            'course_title' => $progress['course_title'],
            'course_brief' => $progress['course_brief'],
            'progress' => $progressPercentage
        ];
    }

    // Если прогресс достиг 100%, перемещаем курс из списка "В процессе" в список "Архив"
    if ($progressPercentage == 100) {
        unset($highestProgressByCourse[$courseId]);
        $progressArchived[$courseId] = [
            'course_title' => $progress['course_title'],
            'course_brief' => $progress['course_brief'],
            'progress' => $progressPercentage
        ];
    }
}

// Запрос для получения данных о созданных курсах и статистики
$createdCoursesQuery = "
    SELECT 
        c.id_course, 
        c.course_title, 
        c.views_count,
        COUNT(DISTINCT uc.user_id) AS enrolled_students,
        COUNT(DISTINCT CASE WHEN up.progress = 100 THEN up.user_id END) AS completed_students
    FROM 
        courses c
        LEFT JOIN user_course uc ON c.id_course = uc.course_id
        LEFT JOIN user_progress up ON c.id_course = up.course_id
    WHERE 
        c.author = :userId
    GROUP BY 
        c.id_course, c.course_title, c.views_count
";
$stmt = $connect->prepare($createdCoursesQuery);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$createdCoursesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Статистика</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stats.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/catalogue.css">
    <link rel="shortcut icon" href="img/star.svg" type="image/x-icon">
    <!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>
<style>
    .c-stat {
        display: flex;
        justify-content: space-between;
    }

    .c-stat p {
        margin-top: 0;
        margin-bottom: 1vh;
    }
</style>
<?php include 'header.php'; ?>

<body>
    <section>
        <div class="topic-title" style="margin: 4vh 0;">
            <img class="star-img" src="img/star.svg">
            <h2 class="regtext">Статистика</h2>
        </div>
        <h4 class="regtext">Созданные курсы</h4>
        <div class="courses-list">
            <?php foreach ($createdCoursesData as $course): ?>
                <a href="course-info.php?course_id=<?php echo $course['id_course']; ?>">
                    <div class="course-card" id="course-card">
                        <div class="course-desc"></div>
                        <p class="c-title"><?php echo $course['course_title']; ?></p>
                        <div class="c-stat"><p class="c-desc">Просмотров: </p><p><?php echo $course['views_count']; ?></p></div>
                        <div class="c-stat"><p class="c-desc">Записавшихся: </p><p><?php echo $course['enrolled_students']; ?></p></div>
                        <div class="c-stat"><p class="c-desc">Успешно прошедших: </p><p><?php echo $course['completed_students']; ?></p></div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <div>
            <h4 class="regtext">В процессе</h4>
            <div class="courses-list">
                <?php if (empty($highestProgressByCourse)): ?>
                    <p>Вы еще не начали ни одного курса.</p>
                <?php else: ?>
                    <?php foreach ($highestProgressByCourse as $courseId => $progressInfo): ?>
                        <a href="course-info.php?course_id=<?php echo $courseId; ?>">
                            <div class="course-card" id="course-card">
                                <div class="course-desc"></div>
                                <p class="c-title"><?php echo $progressInfo['course_title']; ?></p>
                                <p class="c-desc"><?php echo $progressInfo['course_brief']; ?></p>
                                <div class="col-sm-6 pl-progress-block">
                                    <div class="course-progress box-progress-wrapper">
                                        <p class="box-progress-header">Прогресс</p>
                                        <div class="progress box-progress-bar">
                                            <div class="progress-bar" role="progressbar"
                                                aria-valuenow="<?php echo $progressInfo['progress']; ?>" aria-valuemin="0"
                                                aria-valuemax="100" data-percent="<?php echo $progressInfo['progress']; ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <p class="box-progress-percentage"><?php echo $progressInfo['progress']; ?>%</p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </div>
        <div id="blur" style="left: 100%;"></div>
        <div>
            <h4 class="regtext">Архив</h4>
            <div class="courses-list" style="opacity: 0.7;">
                <?php if (empty($progressArchived)): ?>
                    <p>Вы еще не завершили ни одного курса.</p>
                <?php else: ?>
                    <?php foreach ($progressArchived as $progress): ?>
                        <a href="course-info.php?course_id=<?php echo $progress['id_course']; ?>">
                            <div class="course-card" id="course-card">
                                <div class="course-desc"></div>
                                <p class="c-title"><?php echo $progress['course_title']; ?></p>
                                <p class="c-desc"><?php echo $progress['course_brief']; ?></p>
                                <div class="col-sm-6 pl-progress-block">
                                    <div class="course-progress box-progress-wrapper">
                                        <p class="box-progress-header">Прогресс</p>
                                        <div class="progress box-progress-bar">
                                            <div class="progress-bar" role="progressbar"
                                                aria-valuenow="<?php echo $progress['progress']; ?>" aria-valuemin="0"
                                                aria-valuemax="100" data-percent="<?php echo $progress['progress']; ?>"></div>
                                        </div>
                                    </div>
                                    <p class="box-progress-percentage"><?php echo $progress['progress']; ?>%</p>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
    <script>
        (function ($) {
            $.fn.progress = function () {
                var percent = this.data("percent");
                this.css("width", percent + "%");
            };
        }(jQuery));

        $(document).ready(function () {
            $(".progress-bar").each(function () {
                $(this).progress();
            });
        });
    </script>
    <script src="js/script.js"></script>
</body>

</html>