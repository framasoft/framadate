{extends 'admin/admin_page.tpl'}

{block name="header"}
    <script src="{"js/app/admin/polls.js"|resource}" type="text/javascript"></script>
{/block}

{block 'admin_main'}
    <div class="panel panel-default" id="poll_search">
        <div class="panel-heading">{t('Generic', 'Search')}</div>
        <div class="panel-body" style="display: none;">
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="poll" class="control-label">{t('Admin', 'Poll ID')}</label>
                            <input type="text" name="poll" id="poll" class="form-control"
                                   value="{$search['poll']|html}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title" class="control-label">{t('Admin', 'Title')}</label>
                            <input type="text" name="title" id="title" class="form-control"
                                   value="{$search['title']|html}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name" class="control-label">{t('Admin', 'Author')}</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   value="{$search['name']|html}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mail" class="control-label">{t('Admin', 'Email')}</label>
                            <input type="text" name="mail" id="mail" class="form-control"
                                   value="{$search['mail']|html}"/>
                        </div>
                    </div>
                </div>
                <input type="submit" value="{t('Generic', 'Search')}" class="btn btn-default"/>
            </form>
        </div>
    </div>

    <form action="" method="POST">
        <input type="hidden" name="csrf" value="{$crsf}"/>
        {if $poll_to_delete}
            <div class="alert alert-warning text-center">
                <h3>{t('adminstuds', 'Confirm removal of your poll')} "{$poll_to_delete->id|html}"</h3>

                <p>
                    <button class="btn btn-default" type="submit" value="1"
                            name="annullesuppression">{t('adminstuds', 'Keep the poll')}</button>
                    <button type="submit" name="delete_confirm" value="{$poll_to_delete->id|html}"
                            class="btn btn-danger">{t('adminstuds', 'Delete poll')}</button>
                </p>
            </div>
        {/if}
        <input type="hidden" name="csrf" value="{$crsf}"/>

        <div class="panel panel-default">
            <div class="panel-heading">
                {if $count == $total}{$count}{else}{$count} / {$total}{/if} {t('Admin', 'polls in the database at this time')}
            </div>
            <div class="table-of-polls panel">
                <table class="table table-bordered table-polls">
                    <tr align="center">
                        <th scope="col"></th>
                        <th scope="col">{t('Admin', 'Title')}</th>
                        <th scope="col">{t('Admin', 'Author')}</th>
                        <th scope="col">{t('Admin', 'Email')}</th>
                        <th scope="col">{t('Admin', 'Expiry date')}</th>
                        <th scope="col">{t('Admin', 'Votes')}</th>
                        <th scope="col">{t('Admin', 'Poll ID')}</th>
                        <th scope="col" colspan="3">{t('Admin', 'Actions')}</th>
                    </tr>
                    {foreach $polls as $poll}
                        <tr align="center">
                            <td class="cell-format">
                                {if $poll->format === 'D'}
                                    <i class="fa fa-calendar" aria-hidden="true"
                                       title="{t('Generic', 'Date')}"></i>
                                    <span class="sr-only">{t('Generic', 'Date')}</span>
                                {else}
                                    <i class="fa fa-th-list" aria-hidden="true"
                                       title="{t('Generic', 'Classic')}"></i>
                                    <span class="sr-only">{t('Generic', 'Classic')}</span>
                                {/if}
                            </td>
                            <td>{$poll->title|html}</td>
                            <td>{$poll->admin_name|html}</td>
                            <td>{$poll->admin_mail|html}</td>

                            {if $poll->end_date > date_create()}
                                <td>{$poll->end_date|date_format_intl:'d/m/Y'}</td>
                            {else}
                                <td><span class="text-danger">{$poll->end_date|date_format_intl:'d/m/Y'}</span></td>
                            {/if}
                            <td>{$poll->votes|html}</td>
                            <td>{$poll->id|html}</td>
                            <td>
                                <a href="{poll_url id=$poll->id}" class="btn btn-link"
                                   title="{t('Admin', 'See the poll')}">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                    <span class="sr-only">{t('Admin', 'See the poll')}</span>
                                </a>
                            </td>
                            <td>
                                <a href="{poll_url id=$poll->admin_id admin=true}" class="btn btn-link"
                                   title="{t('Admin', 'Change the poll')}">
                                   <i class="fa fa-pencil" aria-hidden="true"></i>
                                   <span class="sr-only">{t('Admin', 'Change the poll')}</span>
                                </a>
                            </td>
                            <td>
                                <button type="submit" name="delete_poll" value="{$poll->id|html}" class="btn btn-link"
                                        title="{t('Admin', 'Poll deleted')}">
                                    <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                                    <span class="sr-only">{t('Admin', 'Poll deleted')}</span>
                                </button>
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>

            <div class="panel-heading">
                {t('Admin', 'Pages:')}
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
