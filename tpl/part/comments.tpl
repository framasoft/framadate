<hr role="presentation" id="comments"/>
<form action="#comments" method="POST">

    {* Comment list *}

    {if $comments|count > 0}
        <h3>{_("Comments of polled people")}</h3>
        {foreach $comments as $comment}
            <div class="comment">
                {if $admin}
                    <button type="submit" name="delete_comment" value="{$comment->id_comment}" class="btn btn-link" title="{_('Remove the comment')}"><span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">{_('Remove')}</span></button>
                {/if}
                <b>{$comment->usercomment}</b>&nbsp;
                <span class="comment">{nl2br($comment->comment)}</span>
            </div>
        {/foreach}
    {/if}

    {* Add comment form *}
    {if $active}
        <div class="hidden-print alert alert-info">
            <div class="col-md-6 col-md-offset-3">
                <fieldset id="add-comment"><legend>{_("Add a comment to the poll")}</legend>
                    <div class="form-group">
                        <label for="name" class="control-label">{_("Your name")}</label>
                        <input type="text" name="name" id="name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="comment" class="control-label">{_("Your comment")}</label>
                        <textarea name="comment" id="comment" class="form-control" rows="2" cols="40"></textarea>
                    </div>
                    <div class="pull-right">
                        <input type="submit" name="add_comment" value="{_("Send the comment")}" class="btn btn-success">
                    </div>
                </fieldset>
            </div>
            <div class="clearfix"></div>
        </div>
    {/if}
</form>