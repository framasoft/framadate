{extends file='page.tpl'}

{block name=main}
<form action="{$admin_poll_id|poll_url:true|html}" method="POST">
    <div class="alert alert-danger text-center">
        <h2>{__('adminstuds\\Confirm removal of the poll')}</h2>
        <p><button class="btn btn-default" type="submit" name="cancel">{__('adminstuds\\Keep the poll')}</button>
            <button type="submit" name="confirm_delete_poll" class="btn btn-danger">{__('PollInfo\\Remove the poll')}</button></p>
    </div>
</form>
{/block}