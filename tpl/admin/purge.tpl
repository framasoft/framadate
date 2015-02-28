{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
    {if $message}
        <div class="alert alert-dismissible alert-info" role="alert">{$message|html}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
    {/if}
    <form action="" method="POST">
        <input type="hidden" name="csrf" value="{$crsf}"/>
        <div class="text-center">
            <button type="submit" name="action" value="purge" class="btn btn-warning">{_('Purge all polls')} <span class="glyphicon glyphicon-trash text-danger"></span><span class="sr-only">{_('Purge all polls')}</span></button>
        </div>
    </form>
{/block}