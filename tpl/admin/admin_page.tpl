{extends 'page.tpl'}

{block 'main'}
    <div class="row">
        <div class="col-xs-12 lead">
            <a class="btn btn-default" href="{'admin'|resource}">{__('Admin', 'Back to administration')}</a>
        </div>
    </div>
    {block 'admin_main'}{/block}
{/block}
