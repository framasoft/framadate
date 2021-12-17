{extends 'admin/admin_page.tpl'}

{block name="header"}
    <script src="{"js/app/admin/polls.js"|resource}"></script>
{/block}

{block 'admin_main'}
    <div class="panel panel-default" id="poll_search">
        <div class="panel-heading">{__('Generic', 'Search')}</div>
        <div class="panel-body" style="display: none;">
            <form method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="poll" class="control-label">{__('Admin', 'Poll ID')}</label>
                            <input type="text" name="poll" id="poll" class="form-control"
                                   value="{$search['poll']|html}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title" class="control-label">{__('Admin', 'Title')}</label>
                            <input type="text" name="title" id="title" class="form-control"
                                   value="{$search['title']|html}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="control-label">{__('Admin', 'Author')}</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{$search['name']|html}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mail" class="control-label">{__('Admin', 'Email')}</label>
                            <input type="text" name="mail" id="mail" class="form-control"
                                   value="{$search['mail']|html}"/>
                        </div>
                    </div>
                </div>
                <input type="submit" value="{__('Generic', 'Search')}" class="btn btn-default"/>
            </form>
        </div>
    </div>

    <form method="POST">
        <input type="hidden" name="csrf" value="{$crsf}"/>
        {if $poll_to_delete}
            <div class="alert alert-warning text-center">
                <h3>{__('adminstuds', 'Confirm removal of the poll')} "{$poll_to_delete->id|html}"</h3>

                <p>
                    <button class="btn btn-default" type="submit" value="1"
                            name="annullesuppression">{__('adminstuds', 'Keep the poll')}</button>
                    <button type="submit" name="delete_confirm" value="{$poll_to_delete->id|html}"
                            class="btn btn-danger">{__('adminstuds', 'Delete the poll')}</button>
                </p>
            </div>
        {/if}
        <input type="hidden" name="csrf" value="{$crsf}"/>

        <div class="panel panel-default">
            <div class="panel-heading">
                {if $count == $total}{$count}{else}{$count} / {$total}{/if} {__('Admin', 'polls in the database at this time')}
            </div>
            <div class="table-of-polls panel">
                <table class="table table-bordered table-polls">
                    <tr align="center">
                        <th scope="col"></th>
                        <th scope="col">{__('Admin', 'Title')}</th>
                        <th scope="col">{__('Admin', 'Author')}</th>
                        <th scope="col">{__('Admin', 'Email')}</th>
                        <th scope="col">{__('Admin', 'Expiration date')}</th>
                        <th scope="col">{__('Admin', 'Votes')}</th>
                        <th scope="col">{__('Admin', 'Poll ID')}</th>
                        <th scope="col" colspan="3">{__('Admin', 'Actions')}</th>
                    </tr>
                    {foreach $polls as $poll}
                        <tr align="center">
                            <td class="cell-format">
                                {if $poll->format === 'D'}
                                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"
                                          title="{__('Generic', 'Date')}"></span>
                                    <span class="sr-only">{__('Generic', 'Date')}</span>
                                {else}
                                    <span class="glyphicon glyphicon-list-alt" aria-hidden="true"
                                          title="{__('Generic', 'Classic')}"></span>
                                    <span class="sr-only">{__('Generic', 'Classic')}</span>
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
                            <td><a href="{poll_url id=$poll->id}" class="btn btn-link"
                                   title="{__('Admin', 'See the poll')}"><span
                                            class="glyphicon glyphicon-eye-open"></span><span
                                            class="sr-only">{__('Admin', 'See the poll')}</span></a></td>
                            <td><a href="{poll_url id=$poll->admin_id admin=true}" class="btn btn-link"
                                   title="{__('Admin', 'Change the poll')}"><span
                                            class="glyphicon glyphicon-pencil"></span><span
                                            class="sr-only">{__('Admin', 'Change the poll')}</span></a></td>
                            <td>
                                <button type="submit" name="delete_poll" value="{$poll->id|html}" class="btn btn-link"
                                        title="{__('Admin', 'Deleted the poll')}"><span
                                            class="glyphicon glyphicon-trash text-danger"></span><span
                                            class="sr-only">{__('Admin', 'Deleted the poll')}</span>
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>

            <div class="panel-heading">
                {__('Admin', 'Pages:')}
                {for $p=1 to $pages}
                    {if $p===$page}
                        <a href="{$SERVER_URL}admin/polls.php?page={$p}&{$search_query}" class="btn btn-danger"
                           disabled="disabled">{$p}</a>
                    {else}
                        <a href="{$SERVER_URL}admin/polls.php?page={$p}&{$search_query}" class="btn btn-info">{$p}</a>
                    {/if}
                {/for}
            </div>
        </div>
    </form>
{/block}
