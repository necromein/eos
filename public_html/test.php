<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/style.css">
<script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <script src=""></script>
    <script src=""></script>
</head>
<style>
* {
  margin: 0 auto;
  padding: 0;
}

*:focus {
  outline: none;
}

body {
  background-color: #f4f4f4;
  font-size: 62.5%;
}

h1, a, .login_field {
  font-weight: 600;
}

h1 {
  font-size: 2.0em;
  color: #000;
}

p {
  font-size: 1.5em;
  color: #989898;
  font-weight: 400;
}

a {
  font-size: 1.4em;
  color: #000;
  /*text-transform: uppercase;*/
  text-decoration: none;
}

.container {
  margin-top: 10vh;
  position: relative;
  width: 960px;
  height: 560px;
  padding-top: 30px;
  
}

.main {
  width: 900px;
  height: 500px;
  box-shadow: 0 15px 60px rgba(0,0,0, 0.20);
  background-color: #fff;
  border-radius:30px;
}

.sign_up, .sign_in {
  width: 450px;
  height: 500px;
  float: left;
}

.sign_in {
  position: relative;
}

.w_s_i_content, .w_s_u_content {
  transition: all 0.3s ease;
      display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
    gap: 3vh;

}

.w_s_i_content {
  opacity: 0;
  padding-top: 15px;
}

.w_title {
    
}

.w_s_i_content_anim {
  opacity: 1;
  padding-top: 0;
}

.w_s_u_content_anim {
  opacity: 0;
  padding-top: 15px;
}

.s_i_modal {
  position: absolute;
  top: 0;
  left: 480px;
  width: 415px;
  height: 560px;
  background-color: #fff;
  transition: all 0.4s ease;
  border-radius: 30px;
  box-shadow: 0 15px 60px rgba(0,0,0, 0.20);

}

.s_i_modal_anim {
  left: 65px;
}

.y_s_i_content, .y_s_u_content {
  position: absolute;
  top: 0;
  left: 17%;
  left: 17%;
}

.y_s_i_content {
  opacity: 1;
  
  display: flex;
  flex-direction: column;
  height: 100%;
  justify-content: center;
  gap: 3vh;
}

.y_s_i_content_anim {
  opacity: 0;
  display: none;
}

.y_s_u_content {
  opacity: 0;
  display: none;
}

.y_s_u_content_anim {
  opacity: 1;
  display: block;
  
    display: flex;
  flex-direction: column;
  height: 100%;
  justify-content: center;
  gap: 3vh;
}

.y_title {

}   

.lil_text {
  padding: 16px 0 0 70px;
  line-height: 2.2em;
  font-size: 1.2em;
  color: #000;
  opacity: 0.35;
}

.w_button, .y_button {
  /*margin-left: 70px;*/
  /*padding: 10px 20px 11px 20px;*/
}

.w_button {
  transition: all 0.2s;
}

.y_button {
  line-height: 8em;
  color: #fff;
  font-weight: 500;
  background-color: #000;
}

.y_button:hover {
  cursor: default;
}

.login_field {
  margin: 10px 0 0 70px;
  width: 270px;
  line-height: 43px;
  font-size: 1.4em;
  color: #000;
  background-color: #f4bc00;
  border: 1px solid #f4bc00;
  border-bottom: 1px solid #dba800;
}

::-webkit-input-placeholder {
  color: #000;
  opacity: 0.5;
}

.login_field:focus {
  border-bottom: 1px solid #000;
  transition: 0.4s;
}
</style>
<body>
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
              <input class="input-box" type="text" name="rsurname" placeholder="Фамилия" required>
              <input class="input-box" type="text" name="rname" placeholder="Имя" required>
              <input class="input-box" type="text" name="rpatr" placeholder="Отчество" required>
              <input class="input-box" type="email" name="remail" placeholder="E-mail" required>
              <input class="input-box" type="text" name="rlogin" placeholder="Логин" required>
              <input class="input-box" type="password" name="rpassword" placeholder="Пароль" required>
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
  <script>
      $(document).ready(function() {

  $('.w_button').on('click', function() {

    $('.s_i_modal').toggleClass('s_i_modal_anim');
    $('.y_s_i_content').toggleClass('y_s_i_content_anim');
    $('.w_s_i_content').toggleClass('w_s_i_content_anim');
    $('.w_s_u_content').toggleClass('w_s_u_content_anim');
    $('.y_s_u_content').toggleClass('y_s_u_content_anim');
    return false;

  });

});
  </script>
</body>
</html>
