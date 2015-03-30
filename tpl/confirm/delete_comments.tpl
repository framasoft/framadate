{extends file='page.tpl'}

{block name=main}
    <form action="{$admin_poll_id|poll_url:true|html}" method="POST">
        <div class="alert alert-danger text-center">
            <h2>{__('adminstuds', 'Confirm removal of all comments of the poll')}</h2>
            <p><button class="btn btn-default" type="submit" name="cancel">{__('adminstuds', 'Keep the comments')}</button>
                <button type="submit" name="confirm_remove_all_comments" class="btn btn-danger">{__('adminstuds', 'Remove the comments')}</button></p>
        </div>
    </form>
{/block}