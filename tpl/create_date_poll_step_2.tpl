{extends file='page.tpl'}

{block name="header"}
    <script type="text/javascript" src="{'js/app/framadatepicker.js'|resource}"></script>
    <script type="text/javascript" src="{'js/app/date_poll.js'|resource}"></script>
{/block}

{block name=main}
    <form name="formulaire" action="" method="POST" class="form-horizontal" role="form">
        <div class="row" id="selected-days">
            <div class="col-md-10 col-md-offset-1">
                <h3>{__('Step 2 date', 'Choose the dates of your poll')}</h3>

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

                {foreach $choices as $i=>$choice}
                    {if $choice->getName()}
                        {$day_value = strftime('%d/%m/%Y', $choice->getName())}
                    {else}
                        {$day_value = ''}
                    {/if}
                    <fieldset>
                        <div class="form-group">
                            <legend>
                                <label class="sr-only" for="day{$i}">{__('Generic', 'Day')} {$i+1}</label>

                                <div class="input-group date col-xs-7">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar text-info"></i></span>
                                    <input type="text" class="form-control" id="day{$i}" title="{__('Generic', 'Day')} {$i+1}"
                                           data-date-format="{__('Date', 'dd/mm/yyyy')}" aria-describedby="dateformat{$i}" name="days[]" value="{$day_value}"
                                           size="10" maxlength="10" placeholder="{__('Date', 'dd/mm/yyyy')}"/>
                                </div>
                                <span id="dateformat{$i}" class="sr-only">({__('Date', 'dd/mm/yyyy')})</span>
                            </legend>

                            {foreach $choice->getSlots() as $j=>$slot}
                                <div class="col-sm-2">
                                    <label for="d{$i}-h{$j}" class="sr-only control-label">{__('Generic', 'Time')} {$j+1}</label>
                                    <input type="text" class="form-control hours" title="{$day_value} - {__('Generic', 'Time')} {$j+1}"
                                           placeholder="{__('Generic', 'Time')} {$j+1}" id="d{$i}-h{$j}" name="horaires{$i}[]" value="{$slot}"/>
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
{/block}
