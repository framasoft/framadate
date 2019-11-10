{extends file='page.tpl'}

{block name=main}
<form action="{poll_url id=$admin_poll_id admin=true}" method="POST">
    <div class="alert alert-danger text-center">
        <h2>{t('adminstuds', 'Confirm removal of your poll')}</h2>
        <p><button class="btn btn-default" type="submit" name="cancel">{t('adminstuds', 'Keep the poll')}</button>
            <button type="submit" name="confirm_delete_poll" class="btn btn-danger">{t('PollInfo', 'Remove the poll')}</button></p>
    </div>
</form>
{/block}
