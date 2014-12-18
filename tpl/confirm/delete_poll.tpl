{extends file='page.tpl'}

{block name=main}
<form action="{$admin_poll_id|poll_url:true}" method="POST">
    <div class="alert alert-danger text-center">
        <h2>{_("Confirm removal of your poll")}</h2>
        <p><button class="btn btn-default" type="submit" name="cancel">{_("Keep this poll")}</button>
            <button type="submit" name="confirm_delete_poll" class="btn btn-danger">{_("Remove this poll!")}</button></p>
    </div>
</form>
{/block}