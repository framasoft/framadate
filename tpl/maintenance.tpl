{extends file='page.tpl'}

{block name=main}
    <div class="alert alert-warning text-center">
        <h2>{t('Maintenance', 'The application')} {$APPLICATION_NAME} {t('Maintenance', 'is currently under maintenance.')}</h2>
        <p>{t('Maintenance', 'Thank you for your understanding.')}</p>
    </div>
{/block}
