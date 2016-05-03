{extends file='page.tpl'}

{block name="header"}
    <script src="{"js/jquery-ui.min.js"|resource}" type="text/javascript"></script>
    <script src="{"js/Chart.min.js"|resource}" type="text/javascript"></script>
    <script src="{"js/Chart.StackedBar.js"|resource}" type="text/javascript"></script>
    <script src="{"js/app/studs.js"|resource}" type="text/javascript"></script>
    <link rel="stylesheet" href="{'css/jquery-ui.min.css'|resource}">

{/block}

{block name=main}


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

    {if !$accessGranted && !$resultPubliclyVisible}

        {include 'part/password_request.tpl' active=$poll->active}

    {else}

        {* Global informations about the current poll *}
        {include 'part/poll_info.tpl' admin=$admin}

        {* Information about voting *}
        {if $expired}
            <div class="alert alert-danger">
                <p>{__('studs', 'The poll is expired, it will be deleted soon.')}</p>
                <p>{__('studs', 'Deletion date:')} {$deletion_date|date_format:$date_format['txt_short']|html}</p>
            </div>
        {else}
            {if $admin}
                {include 'part/poll_hint_admin.tpl'}
            {else}
                {include 'part/poll_hint.tpl' active=$poll->active}
            {/if}
        {/if}

        {* Scroll left and right *}
        <div class="hidden row scroll-buttons" aria-hidden="true">
            <div class="btn-group pull-right">
                <button class="btn btn-sm btn-link scroll-left" title="{__('Poll results', 'Scroll to the left')}">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn  btn-sm btn-link scroll-right" title="{__('Poll results', 'Scroll to the right')}">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </button>
            </div>
        </div>

        {if !$accessGranted && $resultPubliclyVisible}
            {include 'part/password_request.tpl' active=$poll->active}
        {/if}

        {* Vote table *}
        {if $poll->format === 'D'}
            {include 'part/vote_table_date.tpl' active=$poll->active}
        {else}
            {include 'part/vote_table_classic.tpl' active=$poll->active}
        {/if}

        {* Comments *}
        {include 'part/comments.tpl' active=$poll->active comments=$comments}

    {/if}

{/block}
