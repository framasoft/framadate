



<div id="comments_list">
    <form action="{if $admin}{poll_url id=$admin_poll_id admin=true}{else}{poll_url id=$poll_id}{/if}" method="POST">
    {if $comments|count > 0}
        <h3>{__('Comments', 'Comments of polled people')}</h3>
        {foreach $comments as $comment }


            <div class="comment"  >

                {if $admin && !$expired}
                    <button type="submit" name="delete_comment" value="{$comment->id|html}" class="btn btn-link" title="{__('Comments', 'Remove the comment')}"><span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">{__('Generic', 'Remove')}</span></button>

                  <button class="btn btn-link btn-sm btn-edit" value="{$comment->id|html}" title="{__('Comments', 'Edit the comment' )}" ><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span></button>
                {/if}
                {if $admin && !$expired}
                <div class="hidden js-comment">

                    <div class="input-group">
                        <input type="text" class="form-control" id="newcomment" name="newcomment" size="40" value="{$comment->comment|html}" />
                        <input type="hidden"  id="edit_id" name="edit_id" value="{$comment->id|html}" />

                        <span class="input-group-btn">
                        <button type="submit" class="btn btn-success" name="edit_comment" value="comment"  title="{__('PollInfo', 'Save the new com')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Save')}</span></button>
                        <button class="btn btn-link btn-cancel" title="{__('PollInfo', 'Cancel the name edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{__('Generic', 'Cancel')}</span></button>
                        </span>
                    </div>
                </div>

                {/if}

                <span class="comment_date">{$comment->date|date_format:$date_format['txt_datetime_short']}</span>
                <b>{$comment->name|html}</b>&nbsp;
                <span>{$comment->comment|escape|nl2br}</span>


              </div>


        {/foreach}

    {/if}
    </form>
    <div id="comments_alerts"></div>
</div>
