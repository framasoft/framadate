<div class="dropdown-menu alert alert-warning" aria-labelledby="legend">
    <div class="help">
    {if $admin}
        <p>{__('adminstuds', 'As poll administrator, you can change all the lines of this poll with this button')}
            <i class="fa fa-pencil" aria-hidden="true"></i>
            <span class="sr-only">{__('Generic', 'Edit')}</span>,
            {__('adminstuds', 'remove a column or a line with')}
            <i class="fa fa-trash text-danger" aria-hidden="true"></i>
            <span class="sr-only">{__('Generic', 'Remove')}</span>
            {__('adminstuds', 'and add a new column with')}
            <i class="fa fa-plus text-success" aria-hidden="true"></i>
            <span class="sr-only">{__('adminstuds', 'Add a column')}</span>.
        </p>

        <p>{__('adminstuds', 'Finally, you can change the properties of this poll such as the title, the comments or your email address.')}</p>
    {else}
        {if $active}
        <p>{__('studs', 'If you want to vote in this poll, you have to give your name, make your choice, and submit it by selecting the save button at the end of the line.')}</p>
        {else}
        <p>{__('studs', 'The administrator locked this poll. Votes and comments are frozen, it is no longer possible to participate')}</p>
        {/if}
    {/if}
    </div>
    <p class="h4" aria-hidden="true">{__('Generic', 'Legend:')}</p>
    <ul class="list-unstyled">
        <li class="l-yes">
            <span class="text-success"><i class="fa fa-check"></i></span>
            <span onclick="document.getElementById('t-wrap').classList.toggle('hideYes')">
                {__('Generic', 'Yes')}
            </span>
        </li>
        <li class="l-inb">
            <span class="text-warning">(<i class="fa fa-check"></i>)</span>
            <span onclick="document.getElementById('t-wrap').classList.toggle('hideInb')">
                {__('Generic', 'Under reserve')}
            </span>
        </li>
        <li class="l-no">
            <span class="text-danger"><i class="fa fa-times"></i></span>
            <span onclick="document.getElementById('t-wrap').classList.toggle('hideNo')">
                {__('Generic', 'No')}
            </span>
        </li>
        <li class="l-idk">
            <span class="text-muted"><i class="fa fa-question"></i></span>
            <span onclick="document.getElementById('t-wrap').classList.toggle('hideIdk')">
                {__('Generic', 'I don’t know')}
            </span>
        </li>
      </ul>
    </div>
</div>