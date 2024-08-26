<?php
session_start();
require_once 'dbconnect.php';

// Сохраняем URL предыдущей страницы в сессии
if (!isset($_SESSION['previous_page'])) {
    $_SESSION['previous_page'] = $_SERVER['REQUEST_URI'];
}

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>eos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/auth.css">
  <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>

<?php include 'header.php'; ?>

<style>
/* Можно добавить пользовательские стили здесь */
</style>

<body>
<?php include 'modal.php'; ?>

<div class="auth-container">
  <div class='container'>
    <div class='main'>
      <div class='sign_up'>
        <div class='w_s_u_content'>
          <h4 class='w_title'>Нет аккаунта?</h4>
          <a href='#' class='w_button button-box'>Зарегистрироваться</a>
        </div>
      </div>
      <div class='sign_in'>
        <div class='w_s_i_content'>
          <h4 class='w_title'>Уже есть аккаунт?</h4>
          <a href='#' class='w_button button-box'>Войти</a>
        </div>
      </div>
      <div class='s_i_modal'>
        <div class='y_s_i_content'>
          <h4 class='y_title'>Авторизация</h4>
          <form class="" action="source/signin.php" method="POST">
            <div class="f-container">
              <div style="display: flex;flex-direction: column;justify-content: space-around;">
                <div>
                  <div class="auth-input-container">
                    <input class="input-box" type="text" name="login" placeholder="Логин">
                    <input class="input-box" type="password" name="password" placeholder="Пароль">
                    <?php if (isset($_SESSION['validation']['result'])) {
                        echo $_SESSION['validation']['result'];
                    } ?>
                  </div>
                </div>
                <div class="auth-submit-btn">
                  <button class="button-box" type="submit" name="send">Войти</button>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class='y_s_u_content'>
          <h4 class='y_title'>Регистрация</h4>
          <form class="" action="source/signup.php" method="POST">
            <div class="f-container">
              <div>
                <div class="auth-input-container">
                  <input class="input-box" type="text" name="rsurname" placeholder="Фамилия">
                  <?php if (isset($_SESSION['validation']['rsurnameError'])): ?>
        <div class="error-message"><?php echo $_SESSION['validation']['rsurnameError']; ?></div>
    <?php endif; ?>
                  <input class="input-box" type="text" name="rname" placeholder="Имя" >
                  <input class="input-box" type="text" name="rpatr" placeholder="Отчество" >
                  <input class="input-box" type="email" name="remail" placeholder="E-mail" >
                  <input class="input-box" type="text" name="rlogin" placeholder="Логин" >
                  <input class="input-box" type="password" name="rpassword" placeholder="Пароль" >
                </div>
                <div class="auth-submit-btn">
                  <button class="button-box" type="submit" name="send2">Создать аккаунт</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="js/script.js"></script>
<script src="js/auth.js"></script>

</body>

</html>
