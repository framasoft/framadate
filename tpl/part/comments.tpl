<hr role="presentation" id="comments" class="hidden-print"/>
<form action="{if $admin}{poll_url id=$admin_poll_id admin=true}{else}{poll_url id=$poll_id}{/if}#comments" method="POST">

    {* Comment list *}

    {if $comments|count > 0}
        <h3>{__('Comments', 'Comments of polled people')}</h3>
        {foreach $comments as $comment}
            <div class="comment">
                {if $admin && !$expired}
                    <button type="submit" name="delete_comment" value="{$comment->id|html}" class="btn btn-link" title="{__('Comments', 'Remove the comment')}"><span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">{__('Generic', 'Remove')}</span></button>
                {/if}
                <b>{$comment->name|html}</b>&nbsp;
                <span class="comment">{$comment->comment|escape|nl2br}</span>
            </div>
        {/foreach}
    {/if}

    {* Add comment form *}
    {if $active && !$expired}
        <div class="hidden-print jumbotron">
            <div class="col-md-6 col-md-offset-3">
                <fieldset id="add-comment"><legend>{__('Comments', 'Add a comment to the poll')}</legend>
                    <div class="form-group">
                        <label for="comment_name" class="control-label">{__('Generic', 'Your name')}</label>
                        <input type="text" name="name" id="comment_name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="comment" class="control-label">{__('Comments', 'Your comment')}</label>
                        <textarea name="comment" id="comment" class="form-control" rows="2" cols="40"></textarea>
                    </div>
                    <div class="pull-right">
                        <input type="submit" name="add_comment" value="{__('Comments', 'Send the comment')}" class="btn btn-success">
                    </div>
                </fieldset>
            </div>
            <div class="clearfix"></div>
        </div>
    {/if}
</form>