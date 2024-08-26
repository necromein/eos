<?php
session_start();
require_once 'dbconnect.php';

if (isset($_SESSION['user'])) {
  $userId = $_SESSION['user']['id_user'];

  $query = "SELECT * FROM users WHERE id_user = $userId";
  $result = $connect->query($query);

  if ($result->rowCount() > 0) {
    $userData = $result->fetch(pdo::FETCH_ASSOC);
    $_SESSION['user'] = $userData;
  }
}

if (isset($_SESSION['user']['role'])) {
  $userRole = $_SESSION['user']['role'];
}
$_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];

$search = isset($_GET['search']) ? $_GET['search'] : '';
$level = isset($_GET['level']) ? $_GET['level'] : '';

$sql = "SELECT * FROM courses WHERE id_course != 214";

if (!empty($search) || !empty($level)) {
  $sql .= " AND (";

  if (!empty($search)) {
    $sql .= "course_title LIKE '%$search%' OR course_brief LIKE '%$search%' OR course_description LIKE '%$search%'";
  }

  if (!empty($search) && !empty($level)) {
    $sql .= " AND ";
  }

  if (!empty($level)) {
    $sql .= "difficulty_lvl = '$level'";
  }

  $sql .= ")";
}

// сортировка по времени создания
$sql .= " ORDER BY id_course DESC";


$coursesResult = $connect->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Каталог курсов</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/auth.css">
  <link rel="stylesheet" href="css/catalogue.css">
  <link rel="shortcut icon" href="img/star.svg" type="image/x-icon">
  <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>

<?php include 'header.php'; ?>

<body>

  <?php include 'modal.php'; ?>

  <section>

    <div class="topic-title">
      <img class="star-img" src="img/star.svg">
      <h2 class="regtext">ОБУЧАЮЩИЕ курсы</h2>
      <!-- <h2 class="inverse">КУРСЫ</h2> -->
    </div>


    <div class="filter-key-words">
      <div class=key-word-list>
        <a href="?search=дизайн" class="button-box">Дизайн</a>
        <a href="?search=UX/UI" class="button-box">UX/UI</a>
        <a href="?search=графика" class="button-box">Графика</a>
        <a href="?level=Для новичков" class="button-box">Для новичков</a>
        <a href="?level=Для продвинутых" class="button-box">Для продвинутых</a>
        <a href="?search=" class="button-box">Все курсы</a>
      </div>
      <form method="get" action="" class="search-container">
        <div class="search-box">
          <input class="input-box" type="text" name="search" value="<?= $search ?>" placeholder="Что ищете?"
            style="width: 12vw;">
        </div>
      </form>
    </div>

    <div id="blur" style="left: 100%;"></div>

    <div class="slider-container">
    <div class="courses-list">
                <?php
                // Проверяем, есть ли результаты запроса
                if ($coursesResult->rowCount() > 0) {
                    // Если есть результаты, выводим курсы
                    while ($course = $coursesResult->fetch(PDO::FETCH_ASSOC)) {
                        // ваш код для вывода курсов
                        // получение случайного изображения для каждого курса
                        $sql = 'SELECT svg_code FROM images ORDER BY RAND() LIMIT 1';
                        $result = $connect->query($sql);

                        if ($result->rowCount() > 0) {
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                            $svgCode = $row['svg_code'];

                            echo '<a href="course-info.php?course_id=' . $course['id_course'] . '">';
                            echo '<div class="course-card" id="course-card">';
                            echo '<div class="course-desc"></div>';
                            echo '<p class="c-title">' . $course['course_title'] . '</p>';
                            echo '<p class="c-img" >' .
                                $svgCode .
                                '</p>';
                            echo '<p class="c-desc">' . $course['course_brief'] . '</p>';
                            echo '<p class="c-lvl">' . $course['difficulty_lvl'] . '</p>';
                            echo '</div></a>';
                        } else {
                            echo 'Ошибка: ' . $e->getMessage();
                        }
                    }
                } else {
                    // Если результаты не найдены, выводим сообщение об этом
                    echo "<p>По вашему запросу ничего не найдено.</p>";
                }
                ?>
            </div>
    </div>

  </section>
  <?php include 'footer.php'; ?>
  <script src="js/auth.js"></script>
  <script src="js/script.js"></script>

</body>

</html>