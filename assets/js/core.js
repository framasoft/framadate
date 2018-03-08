$(document).ready(function() {

    window.lang = $('html').attr('lang');

    // Horizontal scroll buttons
    if($('.results').width() > $('.container').width()) {
        $('.scroll-buttons').removeClass('hidden');
    }

    var $scroll_page = 1;
    var $scroll_scale = $('#tableContainer').width()*2/3;

    $('.scroll-left').addClass('disabled');

    $('.scroll-left').click(function(){
        var next = Math.floor($scroll_page);
        if(next == $scroll_page) {
            next--;
        }

        $('.scroll-right').removeClass('disabled');
        $('#tableContainer').animate({
            scrollLeft: $scroll_scale*(next - 1)
        }, 1000);
        if($scroll_page == 1) {
            $(this).addClass('disabled');
        } else {
            $scroll_page = next;
        }
        return false;
    });
    $('.scroll-right').click(function(){
        var next = Math.ceil($scroll_page);
        if(next == $scroll_page)
            next++;
        $('.scroll-left').removeClass('disabled');
        $('#tableContainer').animate({
            scrollLeft: $scroll_scale*(next - 1)
        }, 1000);

        if($scroll_scale*($scroll_page+1) > $('.results').width()) {
            $(this).addClass('disabled');
        } else {
            $scroll_page = next;
        }
        return false;
    });

    $('#tableContainer').scroll(function() {
        var position = $(this).scrollLeft();
        $scroll_page = position / $scroll_scale + 1;
        if(position == 0) {
            $('.scroll-left').addClass('disabled');
        } else {
            $('.scroll-left').removeClass('disabled');
        }

        if(position >= $('.results').width() - $('#tableContainer').width()) {
            $('.scroll-right').addClass('disabled');
        } else {
            $('.scroll-right').removeClass('disabled');
        }
    });
});
