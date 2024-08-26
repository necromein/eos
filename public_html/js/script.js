// $(document).ready(function() {
//     $(window).scroll(function() {
//         $('.course-card').each(function() {
//             var position = $(this).offset().top;
//             var scrollPosition = $(window).scrollTop();
//             var windowHeight = $(window).height();
            
//             if (position < scrollPosition + windowHeight - 100) {
//                 $(this).addClass('show');
//             }
//         });
//     });
// });

// $(document).ready(function() {
//     setTimeout(function() {
//         $('.course-card').addClass('show');
//     }, 10); // задержка
// });

document.addEventListener("DOMContentLoaded", function() {
    var cards = document.querySelectorAll('.course-card');

    // Показываем карточки с задержкой
    setTimeout(function() {
        cards.forEach(function(card) {
            if (isElementInViewport(card)) {
                card.classList.add('show');
            }
        });
    }, 1);

    // Показываем карточки при скролле
    window.addEventListener('scroll', function() {
        cards.forEach(function(card) {
            if (isElementInViewport(card)) {
                card.classList.add('show');
            }
        });
    });

    // Функция для проверки, виден ли верхний край элемента в окне
    function isElementInViewport(el) {
        var rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.top <= (window.innerHeight || document.documentElement.clientHeight)
        );
    }
});