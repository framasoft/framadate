{extends file='page.tpl'}

{block name=main}
<form action="{poll_url id=$admin_poll_id admin=true}" method="POST">
    <div class="alert alert-danger text-center">
        <h2>{__('adminstuds', 'Confirm close of the poll')}</h2>
        <p><button class="btn btn-default" type="submit" name="cancel">{__('adminstuds', 'Keep the poll opened')}</button>
            <button type="submit" name="confirm_close_poll" class="btn btn-danger">{__('PollInfo', 'Close the poll')}</button></p>
    </div>
</form>
{/block}
