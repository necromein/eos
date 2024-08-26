<?php
session_start();
require_once 'dbconnect.php';
if (!$_SESSION['user']) {
    header('Location: index.php');
}
$userRole = $_SESSION['user']['role'];

// echo "ид: " . $_SESSION['user']['id_user'] . "<br>";
// echo "роль: " . $_SESSION['user']['role'] . "<br>";


if (isset($_SESSION['user'])) {
    $userId = $_SESSION['user']['id_user'];

    $coursesQuery = "SELECT * FROM courses where author = $userId";
    $coursesResult = $connect->query($coursesQuery);
} 

// $coursesQuery = 'SELECT * FROM courses';
// $coursesQuery = "SELECT * FROM courses where author = $id";
// $coursesResult = $connect->query($coursesQuery);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/catalogue.css">
    <link rel="shortcut icon" href="img/star.svg" type="image/x-icon">
    <!-- <script src="https://code.jquery.com/jquery-3.4.1.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@accessible360/accessible-slick@1.0.1/slick/slick.min.js"></script>
</head>
<style>
    .wrapper #left {
        left: 12vw;
    }

    .wrapper #right {
        right: 12vw;
    }

    .wrapper span {
        cursor: pointer;
        position: absolute;
        text-align: center;
        line-height: 50px;
        transform: translateY(-50%);
    }

    .wrapper span img {
        height: 3vh;
    }

    .carousel {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: calc((100% / 4));
        align-items: center;

        overflow-x: auto;
        scroll-snap-type: x mandatory;
        scroll-behavior: smooth;
        scrollbar-width: none;

        height: 26vh;
        width: 100%;
    }
</style>
<?php include 'header.php'; ?>

<body>

    <section id="profile">

        <div class="topic-title">
            <img class="star-img" src="img/star.svg">
            <h2 class="regtext">личный кабинет</h2>
                <!-- <h2 class="inverse">кабинет</h1> -->
        </div>

        <div class="bio-container">
            <!-- <div class="avatar"><img src="img/avatar.svg" style="height: 30vh; width: 30vw;"> -->
            <?php
            if (isset($_SESSION['user']['avatar']) && !empty($_SESSION['user']['avatar'])) {
                $avatarPath = $_SESSION['user']['avatar'];
            } else {
                $avatarPath = 'img/avatar.svg';
            }
            ?>
<div class="avatar">

            <div class="avatar-container"><img class="avatar-image" src="<?= $avatarPath ?>" style=""></div>

                <div>
                    <a class="button-box" onclick="showEditForm()">Редактировать</a>
                </div>
            </div>
            <div class="bio-info">

                <div>
                    <p>Имя</p>
                    <p>
                        <b>
                            <?= $_SESSION['user']['surname'] ?>
                            <?= $_SESSION['user']['name'] ?>
                            <?= $_SESSION['user']['patronymic'] ?>
                        </b>
                    </p>
                </div>
                <div>
                    <p>Логин</p>

                    <p>
                        <b>
                            <?= $_SESSION['user']['login'] ?>
                        </b>
                    </p>

                </div>
                <div>
                    <p>Почта</p>
                    <p>
                        <b>
                            <?= $_SESSION['user']['email'] ?>
                        </b>
                    </p>
                </div>

            </div>

        </div>
        <div class="backdrop" id='backdrop'>
            <div id="EditForm">
                <div class="form-align">
                    <h4>Редактировать профиль</h4>
                    <form method="post" action="source/edit-profile.php" enctype="multipart/form-data">
                        <div class="add-container">
                            <div class="add-input">
                                <input class="input-box" type="text" name="surname" value="<?php echo $_SESSION[
                                    'user'
                                ]['surname']; ?>" placeholder="Фамилия"><br>
                                <input class="input-box" type="text" name="name" value="<?php echo $_SESSION[
                                    'user'
                                ]['name']; ?>" placeholder="Имя"><br>
                                <input class="input-box" type="text" name="patronymic" value="<?php echo $_SESSION[
                                    'user'
                                ]['patronymic']; ?>" placeholder="Отчество"><br>
                                <input class="input-box" type="email" name="email" value="<?php echo $_SESSION[
                                    'user'
                                ]['email']; ?>" placeholder="Email"><br>
                                <input class="input-box" type="text" name="login" value="<?php echo $_SESSION[
                                    'user'
                                ]['login']; ?>" placeholder="Логин"><br>
                                <input class="input-box" type="password" name="password" placeholder="Новый пароль"><br>
                                <input type="file" name="avatar">
                                <input class="button-box" type="submit" name="edit_user" onclick="confirmEdit()"
                                    style="margin-top:20px" value='Сохранить'>
                                <button class="button-box" type="button" onclick="closeEditForm()"
                                    style="margin-top:10px">Отмена</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div>
    </section>
    <section>
    <?php if ($userRole == '2') { ?>
<div class="" style="margin-top: 8vh;">
<!-- <img class="star-img" src="img/star.svg"> -->
<h4 class="regtext">созданные курсы</h4>
</div>
<div class="slider-container" style="display: flex;">
<div class="wrapper carousel">

    <?php
    // выводим курсы
    $coursesCount = 0;
    while ($course = $coursesResult->fetch(pdo::FETCH_ASSOC)) {
        // запрос для получения случайного изображения для каждого курса
        $sql = 'SELECT svg_code FROM images ORDER BY RAND() LIMIT 1';
        $result = $connect->query($sql);

        if ($result->rowCount() > 0) {
            $row = $result->fetch(pdo::FETCH_ASSOC);
            $svgCode = $row['svg_code'];

            echo '<a style="margin-right: 1vw; margin-left: 1vw;" href="course-info.php?course_id=' .
                $course['id_course'] .
                '">';
            echo '<div class="course-card" id="course-card">';
            echo '<div class="course-desc"></div>';
            echo '<p class="c-title">' . $course['course_title'] . '</p>';
            // echo '<p style="display: flex; justify-content: center; align-items: center; height: 30vh;">' .
            //     $svgCode .
            //     '</p>';
            echo '<p class="c-desc">' . $course['course_brief'] . '</p>';
            echo '<p class="c-lvl">' . $course['difficulty_lvl'] . '</p>';
            echo '</div></a>';

            $coursesCount++;

            // if ($coursesCount >= 9) {
            //     break;
            // }
        } else {
            echo 'Ошибка: ' . $e->getMessage();
        }
    }
    ?>
    <div>
        <span id="left" class=" "><img src="img/arrow-left.svg"></span>
        <span id="right" class=" "><img src="img/arrow-right.svg"></span>
    </div>

</div>
</div>
            <?php        } ?>
        <!-- </div> -->

    </section>
    <?php include 'footer.php'; ?>
    <script src="js/script.js"></script>
    <script>
        function addUser() {
            document.getElementById('EditForm').style.display = 'block';
        }

        // отобразить модальное окно
        function showEditForm() {
            document.getElementById('EditForm').style.display = 'block';
            document.getElementById('backdrop').style.display = 'block';
        }

        // закрыть модальное окно
        function closeEditForm() {
            document.getElementById('EditForm').style.display = 'none';
            document.getElementById('backdrop').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const leftArrow = document.getElementById('left');
            const rightArrow = document.getElementById('right');
            const carousel = document.querySelector('.carousel');

            const cardWidth = carousel.offsetWidth * 0.25;

            rightArrow.addEventListener('click', function () {
                carousel.scrollBy({
                    left: cardWidth,
                    behavior: 'smooth'
                });
            });

            leftArrow.addEventListener('click', function () {
                carousel.scrollBy({
                    left: -cardWidth,
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>