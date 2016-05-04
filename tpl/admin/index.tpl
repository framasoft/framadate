{extends 'admin/admin_page.tpl'}

{block 'main'}
<div class="row">
    <div class="col-md-6 col-xs-12">
        <a href="./polls.php"><h2>{__('Admin', 'Polls')}</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="./migration.php"><h2>{__('Admin', 'Migration')}</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="./purge.php"><h2>{__('Admin', 'Purge')}</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="./check.php"><h2>{__('Check', 'Installation checking')}</h2></a>
    </div>
    {if $logsAreReadable}
        <div class="col-md-6 col-xs-12">
            <a href="./logs.php"><h2>{__('Admin', 'Logs')}</h2></a>
        </div>
    {/if}
</div>
{/block}
