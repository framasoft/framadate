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
    {include 'part/messages.tpl'}


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
        {if $poll->format == 'D'}
            {include 'part/vote_table_date.tpl' active=$poll->active}
        {else}
            {if $poll->vote_system==constant("Framadate\VoteSystem::MAJORITY")}
                {include 'part/vote_table_classic_majority.tpl' active=$poll->active}
            {elseif $poll->vote_system==constant("Framadate\VoteSystem::MAJORITY_JUDGMENT")}
                {include 'part/vote_table_classic_majority_judgment.tpl' active=$poll->active}
            {/if}
        {/if}

        {* Comments *}
        {include 'part/comments.tpl' active=$poll->active comments=$comments}

    {/if}

{/block}
