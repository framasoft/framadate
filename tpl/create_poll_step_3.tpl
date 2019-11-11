{extends file='page.tpl'}

{block name="header"}
    <script type="text/javascript">
        window.date_formats = {
            DATE: '{t('Date', '%Y-%m-%d')}',
            DATEPICKER: '{t('Date', 'yyyy-mm-dd')}'
        };
    </script>
    <script type="text/javascript" src="{'js/app/framadatepicker.js'|resource}"></script>
{/block}

{block name="main"}
    <form name="formulaire" method="POST" class="form-horizontal" role="form">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well summary">
                    <h4>{t('Step 3', 'List of options')}</h4>
                    {$summary}
                </div>
                <div class="alert alert-info">
                    <p>{t('Step 3', 'Your poll will automatically be archived')} {$default_poll_duration} {t('Generic', 'days')} {t('Step 3', 'after the last date of your poll.')}
                        <br />{t('Step 3', 'You can set a specific expiry date for the poll.')}</p>
                    <div class="form-group">
                        <label for="enddate" class="col-sm-5 control-label">{t('Step 3', 'Expiry date:')}</label>
                        <div class="col-sm-6">
                            <div class="input-group date">
                                <span class="input-group-addon" aria-hidden="true">
                                    <i class="fa fa-calendar text-info"></i>
                                </span>
                                <input type="text" class="form-control" id="enddate" data-date-format="{t('Date', 'yyyy-mm-dd')}" aria-describedby="dateformat" name="enddate" value="{$end_date_str}" size="10" maxlength="10" placeholder="{t('Date', 'yyyy-mm-dd-for-humans')}" />
                            </div>
                        </div>
                        <span id="dateformat" class="sr-only">{t('Date', 'yyyy-mm-dd')}</span>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <p>{t('Step 3', 'Once you have confirmed the creation of your poll, you will automatically be redirected to the poll\'s administration page.')}</p>
                    {if $use_smtp}
                        <p>{t('Step 3', 'Then you will receive two emails: one containing the link of your poll for sending to the participants, the other containing the link to the poll administration page.')}</p>
                    {/if}
                </div>
                {if !empty($errors)}
                <div class="alert alert-danger">
                    {foreach $errors as $error}
                        <p>{$error}</p>
                    {/foreach}
                </div>
                {/if}
                <p class="text-right">
                    <button class="btn btn-default" onclick="javascript:window.history.back();" title="{t('Step 3', 'Back to step 2')}">{t('Generic', 'Back')}</button>
                    <button name="confirmation" value="confirmation" type="submit" class="btn btn-success">{t('Step 3', 'Create the poll')}</button>
                </p>
            </div>
        </div>
    </form>
{/block}
