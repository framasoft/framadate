{extends file='page.tpl'}

{block name=main}
    <form action="{poll_url id=$admin_poll_id admin=true}" method="POST">
        <div class="alert alert-danger text-center">
            <h2>{t('adminstuds', 'Confirm removal of all votes')}</h2>
            <p><button class="btn btn-default" type="submit" name="cancel">{t('adminstuds', 'Keep votes')}</button>
                <button type="submit" name="confirm_remove_all_votes" class="btn btn-danger">{t('adminstuds', 'Remove the votes')}</button></p>
        </div>
    </form>
{/block}
