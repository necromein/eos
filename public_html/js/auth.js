document.addEventListener("DOMContentLoaded", function() {
    // Открытие модального окна
    document.getElementById("auth-button").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById("authorization-container").classList.add("open");
    });

    // Закрытие модального окна при клике за его пределами
    document.addEventListener("click", function(e) {
        if (!e.target.closest("#authorization-container") && !e.target.closest("#auth-button")) {
            document.getElementById("authorization-container").classList.remove("open");
        }
    });

    // Закрытие модального окна при нажатии клавиши Escape
    document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
            document.getElementById("authorization-container").classList.remove("open");
        }
    });
});

// переключение вкладок 
// $(document).ready(function () {
//     $(".auth-modal-box").on("click", ".tab", function () {
//         $(".auth-modal-box").find(".active").removeClass("active");
//         $(this).addClass("active");
//         $(".tab-form").eq($(this).index()).addClass("active");
//     });
// });



document.addEventListener("DOMContentLoaded", function () {
    // Функция для валидации email
    function isValidEmail(email) {
      // Регулярное выражение для проверки валидности email
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    }

    // Функция для валидации формы регистрации
    function validateRegistrationForm() {
      const rsurname = document.querySelector('input[name="rsurname"]').value;
      const rname = document.querySelector('input[name="rname"]').value;
      const rpatr = document.querySelector('input[name="rpatr"]').value;
      const remail = document.querySelector('input[name="remail"]').value;
      const rlogin = document.querySelector('input[name="rlogin"]').value;
      const rpassword = document.querySelector('input[name="rpassword"]').value;

      // все поля
      if (!rsurname || !rname || !rpatr || !remail || !rlogin || !rpassword) {
        alert("Пожалуйста, заполните все поля.");
        return false;
      }

      // email
      if (!isValidEmail(remail)) {
        alert("Пожалуйста, введите корректный адрес электронной почты.");
        return false;
      }

      return true;
    }

    // авторизация
    function validateSignInForm() {
      const login = document.querySelector('input[name="login"]').value;
      const password = document.querySelector('input[name="password"]').value;

      // Пример валидации: все поля должны быть заполнены
      if (!login || !password) {
        alert("Пожалуйста, заполните все поля.");
        return false;
      }

      return true;
    }

    document.querySelector('.signup-tab').addEventListener('submit', function (event) {
      if (!validateRegistrationForm()) {
        event.preventDefault(); // отмена отправки формы, если валидация не прошла
      }
    });

    // 
    document.querySelector('.signin-tab').addEventListener('submit', function (event) {
      if (!validateSignInForm()) {
        event.preventDefault(); // отмена отправки формы, если валидация не прошла
      }
    });
  });




  // сервер

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
