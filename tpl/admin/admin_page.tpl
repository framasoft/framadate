{extends 'page.tpl'}

{block 'main'}
    <div class="row">
        <div class="col-xs-12">
            <a href="{'admin'|resource}">{_('Back to administration')}</a>
        </div>
    </div>
    {block 'admin_main'}{/block}
{/block}