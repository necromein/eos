<head>
  <meta charset="UTF-8">
  <title>eos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/auth.css">
  <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
</head>
<div class="auth-modal-container" id="authorization-container">
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
  <script>
  $(document).ready(function() {
    // Функция для открытия модального окна в зависимости от параметра в URL
    function openModalFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        const modalParam = urlParams.get('modal');
        console.log("modalParam:", modalParam);
        if (modalParam === 'sign_in') {
            $('.s_i_modal').addClass('s_i_modal_anim');
            $('.y_s_i_content').addClass('y_s_i_content_anim');
            $('.w_s_i_content').addClass('w_s_i_content_anim');
            $('.w_s_u_content').addClass('w_s_u_content_anim');
            $('.y_s_u_content').addClass('y_s_u_content_anim');
        }
    }

    // Вызываем функцию при загрузке страницы
    openModalFromUrl();

    $('.w_button').on('click', function() {

    $('.s_i_modal').toggleClass('s_i_modal_anim');
    $('.y_s_i_content').toggleClass('y_s_i_content_anim');
    $('.w_s_i_content').toggleClass('w_s_i_content_anim');
    $('.w_s_u_content').toggleClass('w_s_u_content_anim');
    $('.y_s_u_content').toggleClass('y_s_u_content_anim');
    return false;

  });

});

document.getElementById("myForm").addEventListener("submit", function(event) {
    event.preventDefault();

    var login = document.getElementById("login").value;
    var password = document.getElementById("password").value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "validate.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.loginError) {
                document.getElementById("loginError").innerHTML = response.loginError;
            } else {
                document.getElementById("loginError").innerHTML = "";
            }
            if (response.passwordError) {
                document.getElementById("passwordError").innerHTML = response.passwordError;
            } else {
                document.getElementById("passwordError").innerHTML = "";
            }
        }
    };
    xhr.send("login=" + login + "&password=" + password);
});
  </script>
</div>
