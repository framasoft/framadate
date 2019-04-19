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


var form;

$(document).ready(function () {

    // Flag for onbeforeunload event
    var isSubmittingVote = false;

    /**
     * Save a list of polls inside LocalStorage
     * @param polls
     */
    function setPolls(polls) {
        localStorage.setItem('polls', JSON.stringify(polls));
    }

    /**
     * Add an poll inside LocalStorage
     * @param poll
     */
    function addPoll(poll) {
        var polls = localStorage.getItem('polls');
        if (polls === null) {
            polls = [];
        } else {
            polls = JSON.parse(polls);
        }

        /**
         * Test if the poll is already inside the list
         */
        var index = polls.findIndex(function (existingPoll) {
            return existingPoll.url === poll.url;
        });
        if (index === -1) {
            polls.push(poll);
        } else { // if the poll is already present, we need to update the last access date
            polls[index] = poll;
        }
        setPolls(polls);
    }

    var poll = {
        url: window.location.href,
        title: $('#title-form h3').get(0).childNodes[0].nodeValue,
        accessed: (new Date()).toISOString()
    };

    function isAdmin() {
        return $('.jumbotron').hasClass('bg-danger');
    }

    if (!isAdmin()) {
        if (!localStorage.getItem('polls')) {
            setPolls([poll]);
        } else {
            addPoll(poll);
        }
    }

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
        } else {
            isSubmittingVote = true;
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


    form = $('#comment_form');


    checkCommentSending();

    $("#comment_name").on("keyup change", checkCommentSending);
    $("#comment").on("keyup change", checkCommentSending);

    $("#comment_name").on("change", formatValues);
    $("#comment").on("change", formatValues);


    form.submit(function(event) {
        event.preventDefault();

        if ($('#comment').val()) {
            $('#add_comment').attr("disabled", "disabled");
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                dataType: 'json',
                success: function(data)
                {
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

    $(window).on('beforeunload', function(e) {
        var name = $('#name').val().trim();
        var comment = $('#comment').val().trim();

        if ((!isSubmittingVote && name.length > 0) || comment.length > 0) {
            var confirmationMessage = $('#preventLeaving').text();
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        }
    });
});


function formatValues() {
    var value = $(this).val().trim();

    if (0 === value.length) {
        $(this).val("");
    }
}

function checkCommentSending() {

    var button = $("#add_comment");

    // on page load, "textSend" is not set
    if ("undefined" === typeof button.data("textSend")) {
        button.data("textSend", button.text());
    }

	if (form.get(0) && !form.get(0).checkValidity()) {
		button.prop("disabled", true);
		button.text(button.data("textWait"));
	} else {
		button.prop("disabled", false);
		button.text(button.data("textSend"));
	}
}
