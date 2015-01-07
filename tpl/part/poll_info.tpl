{* TODO Add a form maybe *}
{$admin = $admin|default:false}

{if $admin}<form action="{$admin_poll_id|poll_url:true}" method="POST">{/if}
    <div class="jumbotron{if $admin} bg-danger{/if}">
        <div class="row">
            <div id="title-form" class="col-md-7">
                <h3>{$poll->title}{if $admin} <button class="btn btn-link btn-sm btn-edit" title="{_('Edit the title')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{_('Edit')}</span></button>{/if}</h3>
                {if $admin}
                    <div class="hidden js-title">
                        <label class="sr-only" for="newtitle">{_("Title")}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newtitle" name="title" size="40" value="{$poll->title}" />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-success" name="update_poll_info" value="title" title="'{_('Save the new title')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Save')}</span></button>
                                <button class="btn btn-link btn-cancel" title="#_('Cancel the title edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{_('Cancel')}</span></button>
                            </span>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="col-md-5 hidden-print">
                <div class="btn-group pull-right">
                    <button onclick="print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> {_('Print')}</button>
                    <a href="{$SERVER_URL}exportcsv.php?poll={$poll_id}" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> {_('Export to CSV')}</a>
                    {if $admin}
                        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-trash"></span> <span class="sr-only">{_("Remove")}</span> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><button class="btn btn-link" type="submit" name="remove_all_votes">{_('Remove all the votes') }</button></li>
                            <li><button class="btn btn-link" type="submit" name="remove_all_comments">{_('Remove all the comments')}</button></li>
                            <li class="divider" role="presentation"></li>
                            <li><button class="btn btn-link" type="submit" name="delete_poll">{_("Remove the poll")}</button></li>
                        </ul>
                    {/if}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-5">
                <h4 class="control-label">{_("Initiator of the poll")}</h4>
                <p class="form-control-static">{$poll->admin_name}</p>
                {if $admin}
                    <div id="email-form">
                        <p>{$poll->admin_mail} <button class="btn btn-link btn-sm btn-edit" title="{_('Edit the email adress')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{_('Edit')}</span></button></p>
                        <div class="hidden js-email">
                            <label class="sr-only" for="admin_mail">{_("Email adress")}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="admin_mail" name="admin_mail" size="40" value="{$poll->admin_mail}" />
                                <span class="input-group-btn">
                                    <button type="submit" name="update_poll_info" value="admin_mail" class="btn btn-success" title="{_('Save the email address ')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Save')}</span></button>
                                    <button class="btn btn-link btn-cancel" title="{_('Cancel the email address edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{_('Cancel')}</span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
            {if !empty($poll->comment)}
                <div class="form-group col-md-7" id="description-form">
                    <h4 class="control-label">{_("Description")}{if $admin}<button class="btn btn-link btn-sm btn-edit" title="{_('Edit the description')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{_('Edit')}</span></button>{/if}</h4>
                    <p class="form-control-static well">{$poll->comment}</p>
                    <div class="hidden js-desc text-right">
                        <label class="sr-only" for="newdescription">'._("Description") .'</label>
                        <textarea class="form-control" id="newdescription" name="comment" rows="2" cols="40">{$poll->comment}</textarea>
                        <button type="submit" id="btn-new-desc" name="update_poll_info" value="comment" class="btn btn-sm btn-success" title="{_("Save the description")}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Save')}</span></button>
                        <button class="btn btn-default btn-sm btn-cancel" title="{_('Cancel the description edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{_('Cancel')}</span></button>
                    </div>
                </div>
            {/if}
        </div>

        <div class="row">
            <div class="form-group form-group {if $admin}col-md-5{else}col-md-6{/if}">
                <label for="public-link"><a class="public-link" href="{$poll_id|poll_url}">{_("Public link of the poll")} <span class="btn-link glyphicon glyphicon-link"></span></a></label>
                <input class="form-control" id="public-link" type="text" readonly="readonly" value="{$poll_id|poll_url}" />
            </div>
            {if $admin}
                <div class="form-group col-md-5">
                    <label for="admin-link"><a class="admin-link" href="{$admin_poll_id|poll_url:true}">{_("Admin link of the poll")} <span class="btn-link glyphicon glyphicon-link"></span></a></label>
                    <input class="form-control" id="admin-link" type="text" readonly="readonly" value="{$admin_poll_id|poll_url:true}" />
                </div>
                <div class="form-group col-md-2">
                    <h4 class="control-label">{_("Expiration's date")}</h4>
                    <p>{$poll->end_date|date_format:$date_format['txt_date']}</p>
                </div>
            {/if}
        </div>
        {if $admin}
            <div class="row">
                <div class="col-md-5 col-md-offset-7" >
                    <div id="poll-rules-form">
                        {if $poll->active}
                            {if $poll->editable}
                                {$rule_id = 2}
                                {$rule_icon = '<span class="glyphicon glyphicon-edit"></span>'}
                                {$rule_txt = _('Votes are editable')}
                            {else}
                                {$rule_id = 1}
                                {$rule_icon = '<span class="glyphicon glyphicon-check"></span>'}
                                {$rule_txt = _('Votes and comments are open')}
                            {/if}
                        {else}
                            {$rule_id = 0}
                            {$rule_icon = '<span class="glyphicon glyphicon-lock"></span>'}
                            {$rule_txt = _('Votes and comments are locked')}
                        {/if}
                        <p class="pull-right">{$rule_icon} {$rule_txt}<button class="btn btn-link btn-sm btn-edit" title="{_('Edit the poll rules')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{_('Edit')}</span></button></p>
                        <div class="hidden js-poll-rules">
                            <label class="sr-only" for="rules">{_("Poll rules")}</label>
                            <div class="input-group">
                                <select class="form-control" id="rules" name="rules">
                                    <option value="0"{if $rule_id==0} selected="selected"{/if}>{_("Votes and comments are locked")}</option>
                                    <option value="1"{if $rule_id==1} selected="selected"{/if}>{_("Votes and comments are open")}</option>
                                    <option value="2"{if $rule_id==2} selected="selected"{/if}>{_("Votes are editable")}</option>
                                </select>
                                <span class="input-group-btn">
                                    <button type="submit" name="update_poll_info" value="rules" class="btn btn-success" title="{_('Save the new rules')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Save')}</span></button>
                                    <button class="btn btn-link btn-cancel" title="{_('Cancel the rules edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{_('Cancel')}</span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>
{if $admin}</form>{/if}
