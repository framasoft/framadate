{extends file='page.tpl'}

{block name=main}
    <div class="alert alert-warning">
        <h2>{$error|html}</h2>
        <p>{_('Back to the homepage of')} <a href="{$SERVER_URL|html}">{$APPLICATION_NAME|html}</a></p>
    </div>
{/block}
