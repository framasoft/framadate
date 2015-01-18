{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
    <form action="" method="POST">
        <input type="hidden" name="csrf" value="{$crsf}"/>
        {if $poll_to_delete}
            <div class="alert alert-warning text-center">
                <h3>{_("Confirm removal of the poll ")}"{$poll_to_delete->id|html}"</h3>

                <p>
                    <button class="btn btn-default" type="submit" value="1"
                            name="annullesuppression">{_('Keep this poll!')}</button>
                    <button type="submit" name="delete_confirm" value="{$poll_to_delete->id|html}"
                            class="btn btn-danger">{_('Remove this poll!')}</button>
                </p>
            </div>
        {/if}

        <p>
            {$polls|count} {_('polls in the database at this time')}
            {if $log_file}
                <a role="button" class="btn btn-default btn-xs pull-right" href="{$log_file|resource}">{_('Logs')}</a>
            {/if}
        </p>

        <table class="table table-bordered">
            <tr align="center">
                <th scope="col">{_('Title')}</th>
                <th scope="col">{_('Format')}</th>
                <th scope="col">{_('Author')}</th>
                <th scope="col">{_('Email')}</th>
                <th scope="col">{_('Expiration\'s date')}</th>
                <th scope="col">{_('Users')}</th>
                <th scope="col">{_('Poll ID')}</th>
                <th scope="col" colspan="3">{_('Actions')}</th>
            </tr>
            {foreach $polls as $poll}
                <tr align="center">
                    <td>{$poll->title|html}</td>
                    <td>
                        {if $poll->format === 'D'}
                        <span class="glyphicon glyphicon-calendar" aria-hidden="true" title="{_('Date')}"></span><span class="sr-only">{_('Date')}</span>
                        {else}
                        <span class="glyphicon glyphicon-list-alt" aria-hidden="true" title="{_('Classic')}"></span><span class="sr-only">{_('Classic')}</span>
                        {/if}
                    </td>
                    <td>{$poll->admin_name|html}</td>
                    <td>{$poll->admin_mail|html}</td>

                    {if strtotime($poll->end_date) > time()}
                    <td>{date('d/m/y', strtotime($poll->end_date))}</td>
                    {else}
                    <td><span class="text-danger">{strtotime($poll->end_date)|date_format:'d/m/Y'}</span></td>
                    {/if}
                    <td>{$poll->votes|html}</td>
                    <td>{$poll->id|html}</td>
                    <td><a href="{$poll->id|poll_url|html}" class="btn btn-link" title="{_('See the poll')}"><span class="glyphicon glyphicon-eye-open"></span><span class="sr-only">{_('See the poll')}</span></a></td>
                    <td><a href="{$poll->admin_id|poll_url:true|html}" class="btn btn-link" title="{_('Change the poll')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{_('Change the poll')}</span></a></td>
                    <td><button type="submit" name="delete_poll" value="{$poll->id|html}" class="btn btn-link" title="{_('Remove the poll')}"><span class="glyphicon glyphicon-trash text-danger"></span><span class="sr-only">{_('Remove the poll')}</span></td>
                </tr>
            {/foreach}
        </table>

        <div>
            {_('Pages:')}
            {for $p=1 to $pages}
                {if $p===$page}
                    <a href="{$SERVER_URL}{$SCRIPT_NAME}?page={$p}" class="btn btn-danger" disabled="disabled">{$p}</a>
                {else}
                    <a href="{$SERVER_URL}{$SCRIPT_NAME}?page={$p}" class="btn btn-info">{$p}</a>
                {/if}
            {/for}
        </div>
    </form>
{/block}