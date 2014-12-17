{extends file='page.tpl'}

{block name=main}

    {if !empty($message)}
        <div class="alert alert-{$message->type}" role="alert">{$message->message}</div>
    {/if}

{* Global informations about the current poll *}

{include 'part/poll_info.tpl'}

{* Information about voting *}

{if $poll->active}
<div class="alert alert-info">
    <p>{_("If you want to vote in this poll, you have to give your name, choose the values that fit best for you and validate with the plus button at the end of the line.")}</p>
    <p aria-hidden="true"><b>{_('Legend:')}</b> <span class="glyphicon glyphicon-ok"></span> = {_('Yes')}, <b>(<span class="glyphicon glyphicon-ok"></span>)</b> = {_('Ifneedbe')}, <span class="glyphicon glyphicon-ban-circle"></span> = {_('No')}</p>
</div>
{else}
<div class="alert alert-danger">
    <p>{_("The administrator locked this poll, votes and comments are frozen, it's not possible to participate anymore.")}</p>
    <p aria-hidden="true"><b>{_('Legend:')}</b> <span class="glyphicon glyphicon-ok"></span> = {_('Yes')}, <b>(<span class="glyphicon glyphicon-ok"></span>)</b> = {_('Ifneedbe')}, <span class="glyphicon glyphicon-ban-circle"></span> = {_('No')}</p>
</div>
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