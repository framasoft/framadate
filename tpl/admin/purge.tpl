{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
    {if $message}
        <div class="alert alert-dismissible alert-info" role="alert">{$message|html}<button type="button" class="close" data-dismiss="alert" aria-label="{__('Generic', 'Close')}"><span aria-hidden="true">&times;</span></button></div>
    {/if}
    <form action="" method="POST">
        <input type="hidden" name="csrf" value="{$crsf}"/>
        <div class="text-center">
            <button type="submit" name="action" value="purge" class="btn btn-danger">
                {__('Admin', 'Purge the polls')}
                <i class="fa fa-fire" aria-hidden="true"></i>
            </button>
        </div>
    </form>
{/block}
