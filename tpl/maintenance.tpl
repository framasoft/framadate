{extends file='page.tpl'}

{block name=main}
    <div class="alert alert-warning text-center">
        <h2>{__('Maintenance', 'The application')} {$APPLICATION_NAME} {__('Maintenance', 'is currently under maintenance.')}</h2>
        {if isset($error)}
            <pre>{$error}</pre>
        {/if}
        <p>{__('Maintenance', 'Thank you for your understanding.')}</p>
    </div>
{/block}
