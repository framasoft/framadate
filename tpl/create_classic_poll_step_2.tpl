{extends file='page.tpl'}
{block name="header"}

    <link href="{'css/datepicker3.css'|resource}" rel="stylesheet">
    <script type="text/javascript" src="{'js/app/framadatepicker.js'|resource}"></script>
    <script type="text/javascript" src="{'js/app/classic_poll.js'|resource}"></script>
{/block}

{block name=main}

    <form name="formulaire" action="" method="POST" class="form-horizontal" role="form">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="alert alert-info">
                        <p>{__('Step 2 classic', 'To create a poll you should provide at least two different choices.')}</p>
                        <p>{__('Step 2 classic', 'You can add or remove choices with the buttons')} <span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">{__('Generic', 'Remove')}</span> <span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">{__('Generic', 'Add')}</span></p>
                        {if ($allowMarkdown)}
                            <p>{__('Step 2 classic', 'Links or images can be included using')} <a href="http://{$locale|locale_2_lang}.wikipedia.org/wiki/Markdown">{__('Step 2 classic', 'Markdown syntax')}</a>.</p>
                        {/if}
                    </div>
                    {foreach $choices as $i=>$choice}
                        <div class="form-group choice-field row">
                            <label for="choice{$i}" class="col-sm-2 control-label">{__('Generic', 'Choice')} {$i + 1}</label>
                            <div class="col-sm-10 input-group">
                                <input type="text" class="form-control" name="choices[]" size="40" value="{$choice->getName()}" id="choice{$i}" />
                                {if ($allowMarkdown) }
                                    <span class="input-group-addon md-a-img" title="{__('Step 2 classic', 'Add a link or an image')} - {__('Generic', 'Choice')} {$i + 1}"><span class="glyphicon glyphicon-picture"></span> <span class="glyphicon glyphicon-link"></span></span>
                                {/if}
                            </div>
                        </div>
                    {/foreach}
                    <div class="col-md-4">
                        <div class="btn-group btn-group">
                            <button type="button" id="remove-a-choice" class="btn btn-sub" title="{__('Step 2 classic', 'Remove a choice')}"><span class="glyphicon glyphicon-minus text-info"></span><span class="sr-only">{__('Generic', 'Remove')}</span></button>
                            <button type="button" id="add-a-choice" class="btn btn-sub" title="{__('Step 2 classic', 'Add a choice')}"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">{__('Generic', 'Add')}</span></button>
                        </div>
                    </div>
                    <div class="col-md-8 text-right">
                        <a class="btn btn-sub" href="{$SERVER_URL}/create_poll.php?type=classic" title="{__('Step 2', 'Return to step 1')}">{__('Generic', 'Back')}</a>
                        <button name="fin_sondage_autre" value="{__('Generic', 'Next')}" type="submit" class="btn btn-success" title="{__('Step 2', 'Go to step 3')}">{__('Generic', 'Next')}</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="md-a-imgModal" tabindex="-1" role="dialog" aria-labelledby="md-a-imgModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{__('Generic', 'Close')}</span></button>
                        <p class="modal-title" id="md-a-imgModalLabel">{__('Step 2 classic', 'Add a link or an image')}</p>
                    </div>
                    <div class="modal-body">
                        <p class="alert alert-info">{__('Step 2 classic', 'These fields are optional. You can add a link, an image or both.')}</p>
                        <div class="form-group">
                            <label for="md-img" class="control-label"><span class="glyphicon glyphicon-picture"></span><span class="glyphicon glyphicon-link"></span>{__('Step 2 classic', 'URL of the image')}</label>
                            <input id="md-img" type="text" placeholder="http://…" class="form-control" size="40" />
                        </div>
                        <div class="form-group">
                            <label for="md-a" class="control-label"><span class="glyphicon glyphicon-link"></span>{__('Generic', 'Link')}</label>
                            <input id="md-a" type="text" placeholder="http://…" class="form-control" size="40" />
                        </div>
                        <div class="form-group">
                            <label for="md-text" class="control-label">{__('Step 2 classic', 'Alternative text')}</label>
                            <input id="md-text" type="text" class="form-control" size="40" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sub" data-dismiss="modal">{__('Generic', 'Cancel')}</button>
                        <button type="button" class="btn btn-primary">{__('Generic', 'Add')}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

{/block}
    
