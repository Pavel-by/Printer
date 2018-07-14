$('head').append("<link type='text/css' rel='stylesheet' href='/style/components/scroll-top-top.css'>");

$(document).ready(function() {
    var button = $("<div class='scroll-to-top-button'>");

    button.click(function() {
        $('body').animate({scrollTop: 0}, 500);
    });
    $('body').append(button);

    $(document).scroll(function() {
        var scrolled = window.pageYOffset || document.documentElement.scrollTop;
        if (scrolled > 100) {
            button.addClass("active");
        } else {
            button.removeClass('active');
        }
    });
});


