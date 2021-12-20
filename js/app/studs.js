/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

$(document).ready(function () {

    $('#poll_form').submit(function (event) {
        var name = $('#name').val().trim();

        if (name.length == 0) {
            event.preventDefault();
            var newMessage = $('#nameErrorMessage').clone();
            var messageContainer = $('#message-container');
            messageContainer
                .empty()
                .append(newMessage);
            newMessage.removeClass('hidden');
            $('html, body').animate({
                scrollTop: messageContainer.offset().top
            }, 750);
        }
    });

    $('.choice input:radio').on('change', function(){
      $(this).parent().parent().find('.startunchecked').removeClass('startunchecked');
    });
    $('.startunchecked').on('click', function(){
      $(this).removeClass('startunchecked');
    });
    $('.no input').on('focus', function(){
      $(this).next().removeClass('startunchecked');
    });

    $('.remove-column').on('click', function(e){
        var confirmTranslation = $(this).data('remove-confirmation');
        if (confirm(confirmTranslation)) {
            return true;
        } else {
            e.stopPropagation();
            return false
        }
    });

    var form = $('#comment_form');
    form.submit(function(event) {
        event.preventDefault();

        if ($('#comment').val()) {
            $('#add_comment').attr("disabled", "disabled");
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(data) {
                    $('#comment').val('');
                    if (data.result) {
                        $('#comments_list')
                            .replaceWith(data.comments);
                        var lastComment = $('#comments_list')
                            .find('div.comment')
                            .last();
                        // TODO : replace old jQuery UI Effect with Modern CSS
                        // lastComment.effect('highlight', {color: 'green'}, 401);
                        $('html, body').animate({
                            scrollTop: lastComment.offset().top
                        }, 750);
                    } else {
                        var newMessage = $('#genericErrorTemplate').clone();
                        newMessage
                            .find('.contents')
                            .text(data.message.message);
                        newMessage.removeClass('hidden');
                        var commentsAlert = $('#comments_alerts');
                        commentsAlert
                            .empty()
                            .append(newMessage);
                        $('html, body').animate({
                            scrollTop: commentsAlert.offset().top
                        }, 750);
                    }
                },
                error: function (data) {
                    console.error(data);
                },
                complete: function() {
                    $('#add_comment').removeAttr("disabled");
                }
            });
        }

        return false;
    });

    /**
     * Disable view public results option when there's a password and the poll is not hidden
     */
    $('#password').on('keyup change', function () {
        if($('#password').val() && !($('#hidden').attr('checked'))){
            $('#resultsPubliclyVisible').removeAttr('disabled');
        } else {
            $('#resultsPubliclyVisible').attr('disabled','disabled');
        }
    });
});
