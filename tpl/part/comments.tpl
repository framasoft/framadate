<hr role="presentation" id="comments" class="hidden-print"/>

{* Comment list *}
{include 'part/comments_list.tpl'}

{* Add comment form *}
{if $active && !$expired && $accessGranted}
    <form action="{'action/add_comment.php'|resource}" method="POST" id="comment_form">

        <input type="hidden" name="poll" value="{$poll_id}"/>
        {if !empty($admin_poll_id)}
            <input type="hidden" name="poll_admin" value="{$admin_poll_id}"/>
        {/if}
        <div class="hidden-print jumbotron">
            <div class="col-md-6 col-md-offset-3">
                <fieldset id="add-comment"><legend>{__('Comments', 'Add a comment to the poll')|html}</legend>
                    <div class="form-group">
                        <label for="comment_name" class="control-label">{__('Generic', 'Your name')|html}</label>
                        <input type="text" name="name" id="comment_name" class="form-control" maxlength="60" required>
                    </div>
                    <div class="form-group">
                        <label for="comment" class="control-label">{__('Comments', 'Your comment')|html}</label>
                        <textarea name="comment" id="comment" class="form-control" rows="2" cols="40" required></textarea>
                    </div>
                    <div class="pull-right">
                        <input
	                        type="submit"
	                        id="add_comment"
	                        name="add_comment"
	                        value="{__('Comments', 'Send the comment')|html}"
	                        class="btn btn-success"
	                        data-text-wait="{__('Comments', 'Type your name and a comment to send it')|html}"
                        >
                    </div>
                </fieldset>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
{/if}
