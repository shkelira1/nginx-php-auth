//= jquery-3.2.1.min.js

$(document).ready(function () {
    var i = 0;
    $(".login-providers-list li").each(function () {
        i++;
        $(this).attr("id", +i);
    });
    $('#2').hover(function () {
            $('.site').addClass('otp');
        },
        function () {
            $('.site').removeClass('otp');
        });
    if ($('#2').hasClass('active')) {
        $('.site').addClass('otp-new');
    }
    ;

    $('#3').hover(function () {
            $('.site').addClass('google');
        },
        function () {
            $('.site').removeClass('google');
        });
    if ($('#3').hasClass('active')) {
        $('.site').addClass('google-new');
    }
    $('#4').hover(function () {
            $('.site').addClass('telegram');
        },
        function () {
            $('.site').removeClass('telegram');
        });
    if ($('#4').hasClass('active')) {
        $('.site').addClass('telegram-new');
    }
    $('#5').hover(function () {
            $('.site').addClass('ip');
        },
        function () {
            $('.site').removeClass('ip');
        });
    if ($('#5').hasClass('active')) {
        $('.site').addClass('ip-new');
    }
})


