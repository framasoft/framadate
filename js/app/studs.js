$(document).ready(function() {

    $("#poll_form").submit(function( event ) {
        var name = $("#name").val();
        var regexContent = $("#parameter_name_regex").text().split("/");
        var regex = new RegExp(regexContent[1], regexContent[2]);
        if (name.length == 0 || !regex.test(name)) {
            event.preventDefault();
            var errorMessage = $("#parameter_name_error").text();
            var addedDiv = "<div class='alert alert-dismissible alert-danger' role='alert'>";
            addedDiv += errorMessage;
            addedDiv += "<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
            $("#message-container").empty();
            $("#message-container").append(addedDiv);
            $('html, body').animate({
                scrollTop: $("#message-container").offset().top
            }, 750);
        }
    });

});