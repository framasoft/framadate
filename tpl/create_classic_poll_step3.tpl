{extends file='page.tpl'}

{block name="header"}
    <script>
        window.date_formats = {
            DATE: '{__('Date', 'DATE')}',
            DATEPICKER: '{__('Date', 'datepicker')}'
        };
    </script>
    <script src="{'js/app/framadatepicker.js'|resource}"></script>
{/block}

{block name="main"}
    <form name="formulaire" method="POST" class="form-horizontal">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="well summary">
                    <h4>{__('Step 3', 'List of your choices')}</h4>
                    {$summary}
                </div>
                <div class="alert alert-info">
                    <p>{__('Step 3', 'Your poll will automatically be archived')} {$default_poll_duration} {__('Generic', 'days')} {__('Step 3', 'after the last date of your poll.')}
                        <br />{__('Step 3', 'You can set a closer archiving date for it.')}</p>
                    <div class="form-group">
                        <label for="enddate" class="col-sm-5 control-label">{__('Step 3', 'Archiving date:')}</label>
                        <div class="col-sm-6">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                                <input type="text" class="form-control" id="enddate" data-date-format="{__('Date', 'dd/mm/yyyy')}" aria-describedby="dateformat" name="enddate" value="{$end_date_str}" size="10" maxlength="10" placeholder="{__('Date', 'dd/mm/yyyy')}" />
                            </div>
                        </div>
                        <span id="dateformat" class="sr-only">{__('Date', 'dd/mm/yyyy')}</span>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <p>{__('Step 3', 'Once you have confirmed the creation of your poll, you will be automatically redirected on the administration page of your poll.')}</p>
                    {if $use_smtp}
                        <p>{__('Step 3', 'Then, you will receive quickly two emails: one contening the link of your poll for sending it to the voters, the other contening the link to the administration page of your poll.')}</p>
                    {/if}
                </div>
                <p class="text-right">
                    <button class="btn btn-default" onclick="javascript:window.history.back();" title="{__('Step 3', 'Back to step 2')}">{__('Generic', 'Back')}</button>
                    <button name="confirmation" value="confirmation" type="submit" class="btn btn-success">{__('Step 3', 'Create the poll')}</button>
                </p>
            </div>
        </div>
    </form>
{/block}
