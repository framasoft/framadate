{extends file='page.tpl'}

{block name=main}
    <div class="alert alert-success text-center">
        <h2>{$message}</h2>
        <p>{__('Generic', 'Back to the homepage of')} <a href="{$SERVER_URL|html}">{$APPLICATION_NAME|html}</a></p>
    </div>
{/block}