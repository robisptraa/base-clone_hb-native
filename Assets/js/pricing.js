// Animasi saat scroll
$(document).ready(function() {
    $(window).scroll(function() {
        $('.pricing-card').each(function() {
            var cardPosition = $(this).offset().top;
            var scrollPosition = $(window).scrollTop() + $(window).height();
            
            if (scrollPosition > cardPosition) {
                $(this).addClass('animate__animated animate__fadeInUp');
            }
        });
    });
});