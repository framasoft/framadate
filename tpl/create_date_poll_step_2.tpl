{extends file='page.tpl'}

{block name="header"}
    <script>
        window.date_formats = {
            DATE: '{__('Date', 'DATE')}',
            DATEPICKER: '{__('Date', 'datepicker')}'
        };
    </script>
    <script src="{'js/app/framadatepicker.js'|resource}"></script>
    <script src="{'js/app/date_poll.js'|resource}"></script>
{/block}

{block name=main}
    <form name="formulaire" method="POST" class="form-horizontal">
        <div class="row" id="selected-days">
            <div class="col-md-10 col-md-offset-1">
                <h3>{__('Step 2 date', 'Choose the dates of your poll')}</h3>

                {if $error != null}
                <div class="alert alert-danger">
                    <p>{$error}</p>
                </div>
                {/if}

                <div class="alert alert-info">
                    <p>{__('Step 2 date', 'To schedule an event you need to propose at least two choices (two hours for one day or two days).')}</p>

                    <p>{__('Step 2 date', 'You can add or remove additionnal days and hours with the buttons')}
                        <span class="glyphicon glyphicon-minus text-info"></span>
                        <span class="sr-only">{__('Generic', 'Remove')}</span>
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="sr-only">{__('Generic', 'Add')}</span>
                    </p>

                    <p>{__('Step 2 date', 'For each selected day, you can choose, or not, meeting hours (e.g.: "8h", "8:30", "8h-10h", "evening", etc.)')}</p>
                </div>

                <div id="days_container">
                    {foreach $choices as $i=>$choice}
                        {if $choice->getName()}
                            {$day_value = $choice->getName()|intl_date_format:$date_format['txt_date']}
                        {else}
                            {$day_value = ''}
                        {/if}
                        <fieldset>
                            <div class="form-group">
                                <legend>
                                    <label class="sr-only" for="day{$i}">{__('Generic', 'Day')} {$i+1}</label>

                                    <div class="col-xs-10 col-sm-11">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                                            <input type="text" class="form-control" id="day{$i}" title="{__('Generic', 'Day')} {$i+1}"
                                                   data-date-format="{__('Date', 'dd/mm/yyyy')}" aria-describedby="dateformat{$i}" name="days[]" value="{$day_value}"
                                                   size="10" maxlength="10" placeholder="{__('Date', 'dd/mm/yyyy')}" autocomplete="off"/>
                                        </div>
                                    </div>
                                    <div class="col-xs-2 col-sm-1">
                                        <button type="button" title="{__('Step 2 date', 'Remove this day')}" class="remove-day btn btn-sm btn-link">
                                            <span class="glyphicon glyphicon-remove text-danger"></span>
                                            <span class="sr-only">{__('Step 2 date', 'Remove this day')}</span>
                                        </button>
                                    </div>

                                    <span id="dateformat{$i}" class="sr-only">({__('Date', 'dd/mm/yyyy')})</span>
                                </legend>

                                {foreach $choice->getSlots() as $j=>$slot}
                                    <div class="col-sm-2">
                                        <label for="d{$i}-h{$j}" class="sr-only control-label">{__('Generic', 'Time')} {$j+1}</label>
                                        <input type="text" class="form-control hours" title="{$day_value} - {__('Generic', 'Time')} {$j+1}"
                                               placeholder="{__('Generic', 'Time')} {$j+1}" id="d{$i}-h{$j}" name="horaires{$i}[]" value="{$slot|html_special_chars}"/>
                                    </div>
                                {/foreach}

                                <div class="col-sm-2">
                                    <div class="btn-group btn-group-xs" style="margin-top: 5px;">
                                        <button type="button" title="{__('Step 2 date', 'Remove an hour')}" class="remove-an-hour btn btn-default">
                                            <span class="glyphicon glyphicon-minus text-info"></span>
                                            <span class="sr-only">{__('Step 2 date', 'Remove an hour')}</span>
                                        </button>
                                        <button type="button" title="{__('Step 2 date', 'Add an hour')}" class="add-an-hour btn btn-default">
                                            <span class="glyphicon glyphicon-plus text-success"></span>
                                            <span class="sr-only">{__('Step 2 date', 'Add an hour')}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    {/foreach}
                </div>


                <div class="col-md-4">
                    <button type="button" id="copyhours" class="btn btn-default disabled" title="{__('Step 2 date', 'Copy hours of the first day')}"><span
                                class="glyphicon glyphicon-sort-by-attributes-alt text-info"></span><span
                                class="sr-only">{__('Step 2 date', 'Copy hours of the first day')}</span></button>
                    <div class="btn-group btn-group">
                        <button type="button" id="remove-a-day" class="btn btn-default disabled" title="{__('Step 2 date', 'Remove a day')}"><span
                                    class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">{__('Step 2 date', 'Remove a day')}</span></button>
                        <button type="button" id="add-a-day" class="btn btn-default" title="{__('Step 2 date', 'Add a day')}"><span
                                    class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">{__('Step 2 date', 'Add a day')}</span></button>
                    </div>
                    <a href="" data-toggle="modal" data-target="#add_days" class="btn btn-default" title="{__('Date', 'Add range dates')}">
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="glyphicon glyphicon-plus text-success"></span>
                        <span class="sr-only">{__('Date', 'Add range dates')}</span>
                    </a>
                </div>
                <div class="col-md-8 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-remove text-danger"></span>
                            {__('Generic', 'Remove')} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><a id="resetdays" href="javascript:void(0)">{__('Step 2 date', 'Remove all days')}</a></li>
                            <li><a id="resethours" href="javascript:void(0)">{__('Step 2 date', 'Remove all hours')}</a></li>
                        </ul>
                    </div>
                    <a class="btn btn-default" href="{$SERVER_URL}create_poll.php?type=date"
                       title="{__('Step 2', 'Back to step 1')}">{__('Generic', 'Back')}</a>
                    <button name="choixheures" value="{__('Generic', 'Next')}" type="submit" class="btn btn-success disabled"
                            title="{__('Step 2', 'Go to step 3')}">{__('Generic', 'Next')}</button>
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
                    <h4 class="modal-title">{__('Date', 'Add range dates')}</h4>
                </div>
                <div class="modal-body row">
                    <div class="col-xs-12">
                        <div class="alert alert-info">
                            {__('Date', 'Max dates count')}
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="range_start">{__('Date', 'Start date')}</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="range_start"
                                   data-date-format="{__('Date', 'dd/mm/yyyy')}" size="10" maxlength="10"
                                   placeholder="{__('Date', 'dd/mm/yyyy')}"/>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <label for="range_end">{__('Date', 'End date')}</label>
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                            <input type="text" class="form-control" id="range_end"
                                   data-date-format="{__('Date', 'dd/mm/yyyy')}" size="10" maxlength="10"
                                   placeholder="{__('Date', 'dd/mm/yyyy')}"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-default">{__('Generic', 'Cancel')}</button>
                    <button id="interval_add" class="btn btn-success">{__('Generic', 'Add')}</button>
                </div>
            </div>
        </div>
    </div>
{/block}
