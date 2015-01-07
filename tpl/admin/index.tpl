{extends 'admin/admin_page.tpl'}

{block 'main'}
<div class="row">
    <div class="col-md-6 col-xs-12">
        <a href="./polls.php"><h2>{_('Polls')}</h2></a>
    </div>
    <div class="col-md-6 col-xs-12">
        <a href="./migration.php"><h2>{_('Migration')}</h2></a>
    </div>
</div>
{/block}