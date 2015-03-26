$(document).ready(function() {

    $("#poll_form").submit(function( event ) {
        var name = $("#name").val();
        var regexContent = $("#parameter_name_regex").text().split("/");
        var regex = new RegExp(regexContent[1], regexContent[2]);
        if (name.length == 0 || !regex.test(name)) {
            event.preventDefault();
            var newMessage =  $("#nameErrorMessage").clone();
            $("#message-container").empty();
            $("#message-container").append(newMessage);
            newMessage.removeClass("hidden");
            $('html, body').animate({
                scrollTop: $("#message-container").offset().top
            }, 750);
        }
    });

});