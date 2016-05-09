{* Messages *}
<div id="message-container">
    {if !empty($message)}
        <div class="alert alert-dismissible alert-{$message->type|html} hidden-print" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="{__('Generic', 'Close')}"><span aria-hidden="true">&times;</span></button>
            {$message->message|html}
            {if $message->link != null}
                <div class="input-group input-group-sm">
                        <span class="input-group-btn">
                            <a {if $message->linkTitle != null} title="{$message->linkTitle|escape}" {/if} class="btn btn-default btn-sm" href="{$message->link}">
                                {if $message->linkIcon != null}<i class="glyphicon glyphicon-pencil"></i>{if $message->linkTitle != null}<span class="sr-only">{$message->linkTitle|escape}</span>{/if}{/if}
                            </a>
                        </span>
                    <input type="text" aria-hidden="true" value="{$message->link}" class="form-control" readonly="readonly" >
                </div>
                {if $message->includeTemplate != null}
                    {$message->includeTemplate}
                {/if}
            {/if}
        </div>
    {/if}
</div>
<div id="nameErrorMessage" class="hidden alert alert-dismissible alert-danger hidden-print" role="alert">{__('Error', 'The name is invalid.')}<button type="button" class="close" data-dismiss="alert" aria-label="{__('Generic', 'Close')}"><span aria-hidden="true">&times;</span></button></div>
<div id="genericErrorTemplate" class="hidden alert alert-dismissible alert-danger hidden-print" role="alert"><span class="contents"></span><button type="button" class="close" data-dismiss="alert" aria-label="{__('Generic', 'Close')}"><span aria-hidden="true">&times;</span></button></div>
<div id="genericUnclosableSuccessTemplate" class="hidden alert alert-success hidden-print" role="alert"><span class="contents"></span></div>