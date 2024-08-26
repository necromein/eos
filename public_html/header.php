<style>
  @media (min-width: 992px) {
    header {
        padding-bottom: 1vh;
        padding-top: 1vh;
        width: 100%;
        border-bottom: 0.7px solid #ccc;
        background-color: #ffffff;
        position: fixed;
        z-index: 9999;
        top: 0;
    }
    .menu {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-left: 300px;
        margin-right: 300px;
    }
}

#hamburger {
    width: 30px;
    position: relative;
    margin: auto;
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
    -webkit-transition: .5s ease-in-out;
    -moz-transition: .5s ease-in-out;
    -o-transition: .5s ease-in-out;
    transition: .5s ease-in-out;
    cursor: pointer;
}

#hamburger span {
    display: block;
    position: absolute;
    height: 5px;
    width: 100%;
    background: #333;
    border-radius: 9px;
    opacity: 1;
    left: 7px;
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
    -webkit-transition: .25s ease-in-out;
    -moz-transition: .25s ease-in-out;
    -o-transition: .25s ease-in-out;
    transition: .25s ease-in-out;
}

#hamburger span:nth-child(1) {
    top: 0px;
}

#hamburger span:nth-child(2) {
    top: 11px;
}

#hamburger span:nth-child(3) {
    top: 22px;
}

#hamburger.open span:nth-child(1) {
    top: 11px;
    -webkit-transform: rotate(135deg);
    -moz-transform: rotate(135deg);
    -o-transform: rotate(135deg);
    transform: rotate(135deg);
}

#hamburger.open span:nth-child(2) {
    opacity: 0;
    left: -30px;
}

#hamburger.open span:nth-child(3) {
    top: 11px;
    -webkit-transform: rotate(-135deg);
    -moz-transform: rotate(-135deg);
    -o-transform: rotate(-135deg);
    transform: rotate(-135deg);
}

.menu-icon {
    display: none;
}

@media (max-width: 991px) {
    .menu {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-left: 8vw;
        margin-right: 8vw;
    }
    .menu .menu-icon {
        width: 30px;
        height: 25px;
        padding-top: 0;
        margin-top: 7px;
        display: block;
        -webkit-tap-highlight-color: transparent;
        padding: 5px;
        padding-top: 1px;
        padding-bottom: 12px;
        margin-bottom: 10px;
        cursor: pointer;
    }
    .menu .sitenavigation ul {
        display: none;
        max-width: none !important;
    }
    .menu .sitenavigation.is-tapped>ul {
        display: block;
        /* position: absolute; */
        z-index: 100;
        padding: 0;

        display: flex;
    flex-direction: column;
    gap: 2vh;
    }
    .menu .sitenavigation.is-tapped>ul a {
        width: 100%;
        display: block;
        border: none !important;
    }

    .header-act {
        position: absolute; right: 8vw;
    }
}

    
</style>
<header>
  <div class="menu">
    <div class="sitenavigation">
    <span class="menu-icon">
  <a href="#" class="menu example5"><span></span></a>
      <div id="hamburger">
        <span></span>
        <span></span>
        <span></span>
      </div>
      </span>
      <ul style="padding: 0;">
        <a href="index.php">Главная</a>
        <a href="catalogue.php">Каталог</a>
      
      <?php
      if (isset($userRole)) {
        switch ($userRole) {
          case '1':
            echo '<a href="statistics.php">Статистика</a>';
            break;
          case '2':
            echo '<a href="statistics.php">Статистика</a>';
            echo '<a href="constructor.php">Конструктор курсов</a>';
            break;
          case '0':
            echo '<a href="adminpanel.php">Панель администратора</a>';
            break;
          default:
            break;
        }
      }
      ?>
      </ul>
    </div>
 
    <div class="header-act" style="display: flex; gap: 1vh; ">
      <?php
      if (isset($_SESSION['user'])) {
        
        echo '<div class="auth-button-box" id=" "><a href="profile.php">Профиль</a></div>';
        echo '<div class="auth-button-box" id=" "><a href="source/exit.php">Выйти</a></div>';
      } else {
        echo '<div class="auth-button-box" id=" "><a id="auth-button" href="#">Вход</a></div>';
      }
      ?>
    </div>
  </div>
</header>
<script>
   // on document ready
$(document).ready(function() {

// show/hide the mobile menu based on class added to container
$('.menu-icon').click(function() {
    $(this).parent().toggleClass('is-tapped');
    $('#hamburger').toggleClass('open');
});

// handle touch device events on drop down, first tap adds class, second navigates
$('.touch .sitenavigation li.nav-dropdown > a').on('touchend',
    function(e) {
        if ($('.menu-icon').is(':hidden')) {
            var parent = $(this).parent();
            $(this).find('.clicked').removeClass('clicked');
            if (parent.hasClass('clicked')) {
                window.location.href = $(this).attr('href');
            } else {
                $(this).addClass('linkclicked');

                // close other open menus at this level
                $(this).parent().parent().find('.clicked').removeClass('clicked');

                parent.addClass('clicked');
                e.preventDefault();
            }
        }
    });

// handle the expansion of mobile menu drop down nesting
$('.sitenavigation li.nav-dropdown').click(
    function(event) {
        if (event.stopPropagation) {
            event.stopPropagation();
        } else {
            event.cancelBubble = true;
        }

        if ($('.menu-icon').is(':visible')) {
            $(this).find('> ul').toggle();
            $(this).toggleClass('expanded');
        }
    }
);

// prevent links for propagating click/tap events that may trigger hiding/unhiding
$('.sitenavigation a.nav-dropdown, .sitenavigation li.nav-dropdown a').click(
    function(event) {
        if (event.stopPropagation) {
            event.stopPropagation();
        } else {
            event.cancelBubble = true;
        }
    }
);

// javascript fade in and out of dropdown menu
$('.no-touch .sitenavigation li').hover(
    function() {
        if (!$('.menu-icon').is(':visible')) {
            $(this).find('> ul').fadeIn(100);
        }
    },
    function() {
        if (!$('.menu-icon').is(':visible')) {
            $(this).find('> ul').fadeOut(100);
        }
    }
);
});
</script>