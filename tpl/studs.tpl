{extends file='page.tpl'}

{block name=main}

    {if !empty($message)}
        <div class="alert alert-{$message->type}" role="alert">{$message->message}</div>
    {/if}

{* Global informations about the current poll *}

{include 'part/poll_info.tpl' admin=$admin}

{* Information about voting *}

{if $admin}
    {include 'part/poll_hint_admin.tpl'}
{else}
    {include 'part/poll_hint.tpl' active=$poll->active}
{/if}

{* Scroll left and right *}

<div class="hidden row scroll-buttons" aria-hidden="true">
    <div class="btn-group pull-right">
        <button class="btn btn-sm btn-link scroll-left" title="{_('Scroll to the left')}">
            <span class="glyphicon glyphicon-chevron-left"></span>
        </button>
        <button class="btn  btn-sm btn-link scroll-right" title="{_('Scroll to the right')}">
            <span class="glyphicon glyphicon-chevron-right"></span>
        </button>
    </div>
</div>

{* Vote table *}

{include 'part/vote_table.tpl' active=$poll->active}

{* Comments *}

{include 'part/comments.tpl' active=$poll->active comments=$comments}

{/block}