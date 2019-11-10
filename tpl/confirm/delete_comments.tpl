{extends file='page.tpl'}

{block name=main}
    <form action="{poll_url id=$admin_poll_id admin=true}" method="POST">
        <div class="alert alert-danger text-center">
            <h2>{t('adminstuds', 'Confirm removal of all comments')}</h2>
            <p><button class="btn btn-default" type="submit" name="cancel">{t('adminstuds', 'Keep the comments')}</button>
                <button type="submit" name="confirm_remove_all_comments" class="btn btn-danger">{t('adminstuds', 'Remove the comments')}</button></p>
        </div>
    </form>
{/block}
