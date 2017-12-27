<div class="well">
    <form action="action/send_edit_link_by_email_action.php" method="POST" class="form-inline" id="send_edit_link_form">
        <p>{__('EditLink', "If you don't want to lose your personalized link, we can send it to your email.")}</p>
        <input type="hidden" name="token" value="{$token}"/>
        <input type="hidden" name="poll" value="{$poll_id}"/>
        <input type="hidden" name="editedVoteUniqueId" value="{$editedVoteUniqueId}"/>
        <div class="form-group">
            <label for="email" class="control-label">{__('PollInfo', 'Email')}</label>
            <input type="email" name="email" id="email" class="form-control" />
            <input type="submit" id="send_edit_link_submit" value="{__('EditLink', 'Send')}" class="btn btn-success">
        </div>
    </form>
    <div id="send_edit_link_alert"></div>
</div>

<script>
    $(document).ready(function () {

        var form = $('#send_edit_link_form');
        form.submit(function(event) {
            event.preventDefault();

            if ($('#email').val()) {
                //$('#send_edit_link_submit').attr("disabled", "disabled");
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(data)
                    {
                        var newMessage;
                        if (data.result) {
                            $('#send_edit_link_form').remove();
                            newMessage = $('#genericUnclosableSuccessTemplate').clone();
                        } else {
                            newMessage = $('#genericErrorTemplate').clone();
                        }
                        newMessage
                                .find('.contents')
                                .text(data.message.message);
                        newMessage.removeClass('hidden');
                        $('#send_edit_link_alert')
                                .empty()
                                .append(newMessage);
                    },
                    complete: function() {
                        $('#add_comment').removeAttr("disabled");
                    }
                });
            }

            return false;
        });
    });

</script>