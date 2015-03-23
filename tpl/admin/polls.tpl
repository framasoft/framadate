{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
    <form action="" method="POST">
        <input type="hidden" name="csrf" value="{$crsf}"/>
        {if $poll_to_delete}
            <div class="alert alert-warning text-center">
                <h3>{__('adminstuds\\Confirm removal of the poll')} "{$poll_to_delete->id|html}"</h3>

                <p>
                    <button class="btn btn-default" type="submit" value="1"
                            name="annullesuppression">{__('adminstuds\\Keep the poll')}</button>
                    <button type="submit" name="delete_confirm" value="{$poll_to_delete->id|html}"
                            class="btn btn-danger">{__('adminstuds\\Delete the poll')}</button>
                </p>
            </div>
        {/if}

        <div class="panel panel-default">
            <div class="panel-heading">
                {$polls|count} / {$count} {__('Admin\\polls in the database at this time')}
            </div>


            <table class="table table-bordered table-polls">
                <tr align="center">
                    <th scope="col"></th>
                    <th scope="col">{__('Admin\\Title')}</th>
                    <th scope="col">{__('Admin\\Author')}</th>
                    <th scope="col">{__('Admin\\Email')}</th>
                    <th scope="col">{__('Admin\\Expiration date')}</th>
                    <th scope="col">{__('Admin\\Users')}</th>
                    <th scope="col">{__('Admin\\Poll ID')}</th>
                    <th scope="col" colspan="3">{__('Admin\\Actions')}</th>
                </tr>
                {foreach $polls as $poll}
                    <tr align="center">
                        <td class="cell-format">
                            {if $poll->format === 'D'}
                            <span class="glyphicon glyphicon-calendar" aria-hidden="true" title="{__('Generic\\Date')}"></span><span class="sr-only">{__('Generic\\Date')}</span>
                            {else}
                            <span class="glyphicon glyphicon-list-alt" aria-hidden="true" title="{__('Generic\\Classic')}"></span><span class="sr-only">{__('Generic\\Classic')}</span>
                            {/if}
                        </td>
                        <td>{$poll->title|html}</td>
                        <td>{$poll->admin_name|html}</td>
                        <td>{$poll->admin_mail|html}</td>

                        {if strtotime($poll->end_date) > time()}
                        <td>{date('d/m/y', strtotime($poll->end_date))}</td>
                        {else}
                        <td><span class="text-danger">{strtotime($poll->end_date)|date_format:'d/m/Y'}</span></td>
                        {/if}
                        <td>{$poll->votes|html}</td>
                        <td>{$poll->id|html}</td>
                        <td><a href="{$poll->id|poll_url|html}" class="btn btn-link" title="{__('Admin\\See the poll')}"><span class="glyphicon glyphicon-eye-open"></span><span class="sr-only">{__('Admin\\See the poll')}</span></a></td>
                        <td><a href="{$poll->admin_id|poll_url:true|html}" class="btn btn-link" title="{__('Admin\\Change the poll')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Admin\\Change the poll')}</span></a></td>
                        <td><button type="submit" name="delete_poll" value="{$poll->id|html}" class="btn btn-link" title="{__('Admin\\Deleted the poll')}"><span class="glyphicon glyphicon-trash text-danger"></span><span class="sr-only">{__('Admin\\Deleted the poll')}</span></td>
                    </tr>
                {/foreach}
            </table>

            <div class="panel-heading">
                {__('Admin\\Pages:')}
                {for $p=1 to $pages}
                    {if $p===$page}
                        <a href="{$SERVER_URL}admin/polls.php?page={$p}" class="btn btn-danger" disabled="disabled">{$p}</a>
                    {else}
                        <a href="{$SERVER_URL}admin/polls.php?page={$p}" class="btn btn-info">{$p}</a>
                    {/if}
                {/for}
            </div>
        </div>
    </form>
{/block}