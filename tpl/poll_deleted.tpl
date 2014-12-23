{extends file='page.tpl'}

{block name=main}
    <div class="alert alert-success text-center">
        <h2>{_("Your poll has been removed!")}</h2>
        <p>{_('Back to the homepage of')} <a href="{$SERVER_URL}">{$APPLICATION_NAME}</a></p>
    </div>
{/block}