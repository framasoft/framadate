{extends file='page.tpl'}

{block name=main}
    <div class="alert alert-warning">
        <h2>{$error}</h2>
        <p>{_('Back to the homepage of')} <a href="{$SERVER_URL}">{$APPLICATION_NAME}</a></p>
    </div>
{/block}
