{if $active}
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