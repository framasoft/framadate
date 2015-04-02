$(document).ready(function() {

    window.lang = $('html').attr('lang');

    /**
     *  adminstuds.php
     **/

    $('#title-form .btn-edit').on('click', function() {
        $('#title-form h3').hide();
        $('.js-title').removeClass("hidden");
        $('.js-title input').focus();
        return false;
    });

    $('#title-form .btn-cancel').on('click', function() {
        $('#title-form h3').show();
        $('#title-form .js-title').addClass("hidden");
        $('#title-form .btn-edit').focus();
        return false;
    });

    $('#name-form .btn-edit').on('click', function() {
        $('#name-form p').hide();
        $('.js-name').removeClass("hidden");
        $('.js-name input').focus();
        return false;
    });

    $('#name-form .btn-cancel').on('click', function() {
        $('#name-form p').show();
        $('#name-form .js-name').addClass("hidden");
        $('#name-form .btn-edit').focus();
        return false;
    });

    $('#email-form .btn-edit').on('click', function() {
        $('#email-form p').hide();
        $('#email-form .js-email').removeClass("hidden");
        $('.js-email input').focus();
        return false;
    });

    $('#email-form .btn-cancel').on('click', function() {
        $('#email-form p').show();
        $('#email-form .js-email').addClass("hidden");
        $('#email-form .btn-edit').focus();
        return false;
    });

    $('#description-form .btn-edit').on('click', function() {
        $('#description-form .well').hide();
        $('#description-form .js-desc').removeClass("hidden");
        $('.js-desc textarea').focus();
        return false;
    });

    $('#description-form .btn-cancel').on('click', function() {
        $('#description-form .well').show();
        $('#description-form .js-desc').addClass("hidden");
        $('.js-desc .btn-edit').focus();
        return false;
    });

    $('#poll-rules-form .btn-edit').on('click', function() {
        $('#poll-rules-form p').hide();
        $('#poll-rules-form .js-poll-rules').removeClass("hidden");
        $('.js-poll-rules select').focus();
        return false;
    });

    $('#poll-rules-form .btn-cancel').on('click', function() {
        $('#poll-rules-form p').show();
        $('#poll-rules-form .js-poll-rules').addClass("hidden");
        $('.js-poll-rules .btn-edit').focus();
        return false;
    });

    $('#expiration-form .btn-edit').on('click', function() {
        $('#expiration-form p').hide();
        $('.js-expiration').removeClass("hidden");
        $('.js-expiration input').focus();
        return false;
    });

    $('#expiration-form .btn-cancel').on('click', function() {
        $('#expiration-form p').show();
        $('#expiration-form .js-expiration').addClass("hidden");
        $('#expiration-form .btn-edit').focus();
        return false;
    });

    // Horizontal scroll buttons
    if($('.results').width() > $('.container').width()) {
        $('.scroll-buttons').removeClass('hidden');
    }

    var $scroll_page = 1;
    var $scroll_scale = $('#tableContainer').width()*2/3;

    $('.scroll-left').addClass('disabled');

    $('.scroll-left').click(function(){
        $('.scroll-right').removeClass('disabled');
        $( "#tableContainer" ).animate({
            scrollLeft: $scroll_scale*($scroll_page-1)
        }, 1000);
        if($scroll_page == 1) {
            $(this).addClass('disabled');
        } else {
            $scroll_page = $scroll_page-1;
        }
        return false;
    });
    $('.scroll-right').click(function(){
        $('.scroll-left').removeClass('disabled');
        $( "#tableContainer" ).animate({
            scrollLeft: $scroll_scale*($scroll_page)
        }, 1000);

        if($scroll_scale*($scroll_page+1) > $( ".results" ).width()) {
            $(this).addClass('disabled');
        } else {
            $scroll_page++;
        }
        return false;
    });

});

// Vote form moving to the top or to the bottom
$(window).scroll(function() {
    var $table_offset = $('.results thead').offset();
    if(($table_offset == undefined || $(window).scrollTop() > $table_offset.top+150) && ($('table.results').height()-150 > $(window).height())) {
        $('#addition').before($('#vote-form'));
        $('#tableContainer').after($('.scroll-buttons'));
    } else {
        $('.results tbody').prepend($('#vote-form'));
        $('#tableContainer').before($('.scroll-buttons'));
    }
});
