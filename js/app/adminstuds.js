$(document).ready(function() {

    wrapper = new MDEWrapper($('.js-desc textarea')[0], $('#rich-editor-button'), $('#simple-editor-button'));
    var firstOpening = true;


    $('#title-form .btn-edit').on('click', function() {
        $('#title-form h3').hide();
        $('.js-title').removeClass('hidden');
        $('.js-title input').focus();
        return false;
    });

    $('#title-form .btn-cancel').on('click', function() {
        $('#title-form h3').show();
        $('#title-form .js-title').addClass('hidden');
        $('#title-form .btn-edit').focus();
        return false;
    });

    $('#name-form .btn-edit').on('click', function() {
        $('#name-form p').hide();
        $('.js-name').removeClass('hidden');
        $('.js-name input').focus();
        return false;
    });

    $('#name-form .btn-cancel').on('click', function() {
        $('#name-form p').show();
        $('#name-form .js-name').addClass('hidden');
        $('#name-form .btn-edit').focus();
        return false;
    });

    $('#email-form .btn-edit').on('click', function() {
        $('#email-form p').hide();
        $('#email-form .js-email').removeClass('hidden');
        $('.js-email input').focus();
        return false;
    });

    $('#email-form .btn-cancel').on('click', function() {
        $('#email-form p').show();
        $('#email-form .js-email').addClass('hidden');
        $('#email-form .btn-edit').focus();
        return false;
    });

    $('#description-form .btn-edit').on('click', function() {
        $('#description-form .well').hide();
        $('#description-form .control-label .btn-edit').hide();
        $('#description-form .js-desc').removeClass('hidden');
        $('.js-desc textarea').focus();
        if (firstOpening) {
            firstOpening = false;
            if ($('#rich-editor-button').hasClass('active')) {
                wrapper.enable();
            }
        }
        return false;
    });

    $('#description-form .btn-cancel').on('click', function() {
        $('#description-form .well').show();
        $('#description-form .control-label .btn-edit').show();
        $('#description-form .js-desc').addClass('hidden');
        $('.js-desc .btn-edit').focus();
        return false;
    });

    $('#poll-rules-form .btn-edit').on('click', function() {
        $('#poll-rules-form p').hide();
        $('#poll-rules-form .js-poll-rules').removeClass('hidden');
        $('.js-poll-rules select').focus();
        return false;
    });

    $('#poll-rules-form .btn-cancel').on('click', function() {
        $('#poll-rules-form p').show();
        $('#poll-rules-form .js-poll-rules').addClass('hidden');
        $('.js-poll-rules .btn-edit').focus();
        return false;
    });

    $('#poll-hidden-form .btn-edit').on('click', function() {
        $('#poll-hidden-form p').hide();
        $('#poll-hidden-form .js-poll-hidden').removeClass('hidden');
        $('.js-poll-hidden input[type=checkbox]').focus();
        return false;
    });

    $('#poll-hidden-form .btn-cancel').on('click', function() {
        $('#poll-hidden-form p').show();
        $('#poll-hidden-form .js-poll-hidden').addClass('hidden');
        $('.js-poll-hidden .btn-edit').focus();
        return false;
    });

    $('#expiration-form .btn-edit').on('click', function() {
        $('#expiration-form p').hide();
        $('.js-expiration').removeClass('hidden');
        $('.js-expiration input').focus();
        return false;
    });

    $('#expiration-form .btn-cancel').on('click', function() {
        $('#expiration-form p').show();
        $('#expiration-form .js-expiration').addClass('hidden');
        $('#expiration-form .btn-edit').focus();
        return false;
    });


    $('#password-form .btn-edit').on('click', function() {
        $('#password-form p').hide();
        $('#password-form .js-password').removeClass('hidden');
        $('#password').focus();
        return false;
    });

    $('#password-form .btn-cancel').on('click', function() {
        $('#password-form p').show();
        $('#password-form .js-password').addClass('hidden');
        $('.js-password .btn-edit').focus();
        return false;
    });

    // Hiding other field when the admin wants to remove the password protection
    var removePassword = $('#removePassword');
    removePassword.on('click', function() {
        var removeButton =  removePassword.siblings('button');
        if (removePassword.is(":checked")) {
            $('#password_information').addClass('hidden');
            removeButton.removeClass('hidden');
        } else {
            $('#password_information').removeClass('hidden');
            removeButton.addClass('hidden');
        }
        removeButton.focus();
    });


});