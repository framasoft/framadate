{extends file='page.tpl'}

{block name=main}
    <form action="{$admin_poll_id|poll_url:true|html}" method="POST">
        <div class="alert alert-danger text-center">
            <h2>{_('Confirm removal of all comments of the poll')}</h2>
            <p><button class="btn btn-default" type="submit" name="cancel">{_('Keep comments')}</button>
                <button type="submit" name="confirm_remove_all_comments" class="btn btn-danger">{_('Remove all comments!')}</button></p>
        </div>
    </form>
{/block}