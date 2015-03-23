{extends file='page.tpl'}

{block name=main}
    <div class="alert alert-success text-center">
        <h2>{__('adminstuds\\The poll has been deleted')}</h2>
        <p>{__('Generic\\Back to the homepage of')} <a href="{$SERVER_URL|html}">{$APPLICATION_NAME|html}</a></p>
    </div>
{/block}