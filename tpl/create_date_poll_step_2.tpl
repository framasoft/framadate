{extends file='page.tpl'}

{block name="header"}
    <script type="text/javascript">
        window.date_formats = {
            DATE: '{t('Date', '%Y-%m-%d')}',
            DATEPICKER: '{t('Date', 'yyyy-mm-dd')}'
        };
    </script>
    <script type="text/javascript" src="{'js/app/framadatepicker.js'|resource}"></script>
    <script type="text/javascript" src="{'js/app/date_poll.js'|resource}"></script>
{/block}

{block name=main}
    <form name="formulaire" action="" method="POST" class="form-horizontal" role="form">
        <div class="row" id="selected-days">
            <div class="col-md-10 col-md-offset-1">
                <h3>{t('Step 2 date', 'Choose dates for your poll')}</h3>

                {if $error != null}
                <div class="alert alert-danger">
                    <p>{$error}</p>
                </div>
                {/if}

                <div class="alert alert-info">
                    <p>{t('Step 2 date', 'To schedule an event you need to provide at least two choices (e.g., two time slots on one day or two days).')}</p>

                    <p>{t('Step 2 date', 'You can add or remove additional days and times with the buttons')}
                        <i class="fa fa-minus text-info" aria-hidden="true"></i>
                        <span class="sr-only">{t('Generic', 'Remove')}</span>
                        <i class="fa fa-plus text-success" aria-hidden="true"></i>
                        <span class="sr-only">{t('Generic', 'Add')}</span>
                    </p>

                    <p>{t('Step 2 date', 'For each selected day, you are free to suggest meeting times (e.g., "8h", "8:30", "8h-10h", "evening", etc.)')}</p>
                </div>

                <div id="days_container">
                    {foreach $choices as $i=>$choice}
                        {if $choice->getName()}
                            {$day_value = $choice->getName()|timestamp_to_date|date_format_translation}
                        {else}
                            {$day_value = ''}
                        {/if}
                        <fieldset>
                            <div class="form-group">
                                <legend>
                                    <label class="sr-only" for="day{$i}">{t('Generic', 'Day')} {$i+1}</label>

                                    <div class="col-xs-10 col-sm-11">
                                        <div class="input-group date">
                                            <span class="input-group-addon" aria-hidden="true">
                                                <i class="fa fa-calendar-plus-o text-info"></i>
                                            </span>
                                            <input type="text" class="form-control" id="day{$i}" title="{t('Generic', 'Day')} {$i+1}"
                                                   data-date-format="{t('Date', 'yyyy-mm-dd')}" aria-describedby="dateformat{$i}" name="days[]" value="{$day_value}"
                                                   size="10" maxlength="10" placeholder="{t('Date', 'yyyy-mm-dd-for-humans')}" autocomplete="off"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 col-sm-1">
                                        <button type="button" title="{t('Step 2 date', 'Remove this day')}" class="remove-day btn btn-sm btn-link">
                                            <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                                            <span class="sr-only">{t('Step 2 date', 'Remove this day')}</span>
                                        </button>
                                    </div>

                                    <span id="dateformat{$i}" class="sr-only">({t('Date', 'yyyy-mm-dd')})</span>
                                </legend>

                                {foreach $choice->getSlots() as $j=>$slot}
                                    <div class="col-sm-2">
                                        <label for="d{$i}-h{$j}" class="sr-only control-label">{t('Generic', 'Time')} {$j+1}</label>
                                        <input type="text" class="form-control hours" title="{$day_value} - {t('Generic', 'Time')} {$j+1}"
                                               placeholder="{t('Generic', 'Time')} {$j+1}" id="d{$i}-h{$j}" name="horaires{$i}[]" value="{$slot|html_special_chars}"/>
                                    </div>
                                {/foreach}

                                <div class="col-sm-2">
                                    <div class="btn-group btn-group-xs" style="margin-top: 5px;">
                                        <button type="button" title="{t('Step 2 date', 'Remove a time slot')}" class="remove-an-hour btn btn-default">
                                            <i class="fa fa-minus text-info" aria-hidden="true"></i>
                                            <span class="sr-only">{t('Step 2 date', 'Remove a time slot')}</span>
                                        </button>
                                        <button type="button" title="{t('Step 2 date', 'Add a time slot')}" class="add-an-hour btn btn-default">
                                            <i class="fa fa-plus text-success" aria-hidden="true"></i>
                                            <span class="sr-only">{t('Step 2 date', 'Add a time slot')}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    {/foreach}
                </div>


                <div class="col-md-4">
                    <button type="button" id="copyhours" class="btn btn-default disabled" title="{t('Step 2 date', 'Copy times from the first day')}">
                        <i class="fa fa-history fa-flip-horizontal text-info"></i>
                        <span class="sr-only">{t('Step 2 date', 'Copy times from the first day')}</span>
                    </button>
                    <div class="btn-group btn-group">
                        <button type="button" id="remove-a-day" class="btn btn-default disabled" title="{t('Step 2 date', 'Remove a day')}">
                            <i class="fa fa-minus text-info" aria-hidden="true"></i>
                            <span class="sr-only">{t('Step 2 date', 'Remove a day')}</span>
                        </button>
                        <button type="button" id="add-a-day" class="btn btn-default" title="{t('Step 2 date', 'Add a day')}">
                            <i class="fa fa-plus text-success" aria-hidden="true"></i>
                            <span class="sr-only">{t('Step 2 date', 'Add a day')}</span>
                        </button>
                    </div>
                    <a href="" data-toggle="modal" data-target="#add_days" class="btn btn-default" title="{t('Date', 'Add range dates')}">
                        <i class="fa fa-plus text-success" aria-hidden="true"></i>
                        <i class="fa fa-plus text-success" aria-hidden="true"></i>
                        <span class="sr-only">{t('Date', 'Add range dates')}</span>
                    </a>
                </div>
                <div class="col-md-8 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                            {t('Generic', 'Remove')} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a id="resetdays" href="javascript:void(0)">{t('Step 2 date', 'Remove all days')}</a></li>
                            <li><a id="resethours" href="javascript:void(0)">{t('Step 2 date', 'Remove all times')}</a></li>
                        </ul>
                    </div>
                    <a class="btn btn-default" href="{$SERVER_URL}create_poll.php?type=date"
                       title="{t('Step 2', 'Return to step 1')}">{t('Generic', 'Back')}</a>
                    <button name="choixheures" value="{t('Generic', 'Next')}" type="submit" class="btn btn-success disabled"
                            title="{t('Step 2', 'Go to step 3')}">{t('Generic', 'Next')}</button>
                </div>
            </div>
        </div>
    </form>

    <div id="add_days" class="modal fade">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">{t('Date', 'Add range dates')}</h4>
                </div>
                <div class="modal-body row">
                    <div class="col-xs-12">
                        <div class="alert alert-info">
                            {t('Date', 'You can select at most 4 months')}
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="range_start">{t('Date', 'Start date')}</label>
                        <div class="input-group date">
                            <span class="input-group-addon" aria-hidden="true">
                                <i class="fa fa-calendar-plus-o text-info"></i>
                            </span>
                            <input type="text" class="form-control" id="range_start"
                                   data-date-format="{t('Date', 'yyyy-mm-dd')}" size="10" maxlength="10"
                                   placeholder="{t('Date', 'yyyy-mm-dd-for-humans')}"/>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="range_end">{t('Date', 'End date')}</label>
                        <div class="input-group date">
                            <span class="input-group-addon" aria-hidden="true">
                                <i class="fa fa-calendar-plus-o text-info"></i>
                            </span>
                            <input type="text" class="form-control" id="range_end"
                                   data-date-format="{t('Date', 'yyyy-mm-dd')}" size="10" maxlength="10"
                                   placeholder="{t('Date', 'yyyy-mm-dd-for-humans')}"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default">{t('Generic', 'Cancel')}</button>
                    <button id="interval_add" class="btn btn-success">{t('Generic', 'Add')}</button>
                </div>
            </div>
        </div>
    </div>
{/block}
