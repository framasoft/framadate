{extends file='page.tpl'}

{block name="header"}
    <script src="{"js/Chart.min.js"|resource}" type="text/javascript"></script>
    <script src="{"js/Chart.StackedBar.js"|resource}" type="text/javascript"></script>
    <script src="{"js/app/studs.js"|resource}" type="text/javascript"></script>
    <link rel="stylesheet" href="{'css/jquery-ui.min.css'|resource}">

    {if $admin}
        <script src="{"js/simplemde.min.js"|resource}" type="text/javascript"></script>
        <script src="{"js/mde-wrapper.js"|resource}" type="text/javascript"></script>
        <script src="{"js/app/adminstuds.js"|resource}" type="text/javascript"></script>
        <link rel="stylesheet" href="{'css/simplemde.min.css'|resource}">
    {/if}

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
                <p>{__('studs', 'The poll has expired, it will soon be deleted.')}</p>
                <p>{__('studs', 'Deletion date:')} {$deletion_date|date_format_intl:DATE_FORMAT_SHORT|html}</p>
            </div>
        {else}
            {if $admin}
                {include 'part/poll_hint_admin.tpl'}
            {else}
                {include 'part/poll_hint.tpl' active=$poll->active}
            {/if}
        {/if}

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
