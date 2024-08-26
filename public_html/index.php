<?php
session_start();
require_once 'dbconnect.php';

if (isset($_SESSION['user'])) {
  $userId = $_SESSION['user']['id_user'];

  $query = "SELECT * FROM users WHERE id_user = :userId";
  $stmt = $connect->prepare($query);
  $stmt->bindParam(':userId', $userId);
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

$coursesQuery = 'SELECT * FROM courses where id_course != 214';
$coursesResult = $connect->query($coursesQuery);


// echo "ид: ";
// var_dump($_SESSION['user']['id_user']);
// echo "<br> роль: ";
// var_dump($_SESSION['user']['role']);
?>
 
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Главная страница</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <!--<link rel="stylesheet" href="css/auth.css">-->
  <link rel="stylesheet" href="css/catalogue.css">
  <link rel="shortcut icon" href="img/star.svg" type="image/x-icon">
  <!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css"> -->
  <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>
<?php include 'header.php'; ?>

<body>


  <?php include 'modal.php'; ?>

  <section id="main-page">

    <!-- <div class="blur">
      <img class="blur-1" src="img/blurr.svg">
    </div> -->
    <div id="blob"></div>
    <div class="main-title">
      <img src="img/star.svg">
      <h1 class="regtext">digi<span class="inverse">train</span></h1>

      <!--<h1 class="inverse">заголовок</h1> -->
    </div>

    <div>
      <p>Здесь начинается ваш путь к&nbsp;миру веб-дизайна. <br>
        Эта обучающая платформа была создана, чтобы вы открыли для&nbsp;себя бескрайние возможности для&nbsp;творчества
        и&nbsp;профессионального роста.</p>
    </div>

    <div class="arrow">
      <a href="#main-courses"><img src="img/arrow.svg"></a>

    </div>
    <p></p>
  </section>

  <section id="main-courses">

    <!-- <div id="blur"></div> -->

    <div class="topic-title">
      <img class="star-img" src="img/star.svg">
      <h2 class="regtext">ОБУЧАЮЩИЕ курсы</h2>
      <!-- <h2 class="inverse">КУРСЫ</h2> -->
    </div>

    <div class="slider-container">
      <div class="courses-list">
        <?php
        // выводим курсы
        $coursesCount = 0; // переменная для подсчета выводимых курсов
        while ($course = $coursesResult->fetch(pdo::FETCH_ASSOC)) {
          // запрос для получения случайного изображения для каждого курса
          $sql = 'SELECT svg_code FROM images ORDER BY RAND() LIMIT 1';
          $stmt = $connect->query($sql);

          if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(pdo::FETCH_ASSOC);
            $svgCode = $row['svg_code'];

            echo '<a href="course-info.php?course_id=' .
              $course['id_course'] .
              '">';
            echo '<div class="course-card" id="course-card">';
            echo '<div class="course-desc"></div>';
            echo '<p class="c-title">' . $course['course_title'] . '</p>';
            echo '<p class="c-img">' .
              $svgCode .
              '</p>';
            echo '<p class="c-desc">' . $course['course_brief'] . '</p>';
            echo '<p class="c-lvl">' . $course['difficulty_lvl'] . '</p>';
            echo '</div></a>';

            $coursesCount++;

            // количество выводимых курсов
            if ($coursesCount >= 3) {
              break;
            }
          } else {
            echo 'Ошибка: ' . $e->getMessage();
          }
        }
        ?>
      </div>
    </div>
    <p class="post-script" style="text-align: justify;">Выбирайте курсы, которые подходят именно вам, и&nbsp;начните
      свое увлекательное путешествие в&nbsp;сфере веб&#8209;дизайна. Это&nbsp;ваш ключ к вдохновению и креативному
      самовыражению.
      <!-- <a href='catalogue.php' style="color: #292929; text-decoration: underline;">Больше&nbsp;курсов&nbsp;в&nbsp;каталоге.</a>  -->
    </p>
    <div style="display: flex; justify-content: center;">
      <div><a href='catalogue.php' class="btn-box">Каталог курсов</a></div>
    </div>
  </section>
  <?php include 'footer.php'; ?>
  <script src="js/auth.js"></script>
  <script src="js/script.js"></script>
  <script>
    const blob = document.getElementById("blob");

    window.onpointermove = event => {
      const { clientX, clientY } = event;

      blob.animate({
        left: `${clientX}px`,
        top: `${clientY}px`
      }, { duration: 10000, fill: "forwards" });
    }</script>
</body>

</html>