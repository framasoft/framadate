{$admin = $admin|default:false}

{if $admin}<form action="{poll_url id=$admin_poll_id admin=true}" method="POST">{/if}
    <div class="jumbotron{if $admin} bg-danger{/if}">
        <div class="row"> {* Title | buttons*}
            <div id="title-form" class="col-md-7">
                <h3>{$poll->title|html}{if $admin && !$expired} <button class="btn btn-link btn-sm btn-edit" title="{__('PollInfo', 'Edit the title')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span></button>{/if}</h3>
                {if $admin && !$expired}
                    <div class="hidden js-title">
                        <label class="sr-only" for="newtitle">{__('PollInfo', 'Title')}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newtitle" name="title" size="40" value="{$poll->title|html}" />
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-success" name="update_poll_info" value="title" title="{__('PollInfo', 'Save the new title')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Save')}</span></button>
                                <button class="btn btn-link btn-cancel" title="{__('PollInfo', 'Cancel the title edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{__('Generic', 'Cancel')}</span></button>
                            </span>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="col-md-5 hidden-print">
                <div class="btn-group pull-right">
                    <button onclick="print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> {__('PollInfo', 'Print')}</button>
                    <a href="{$SERVER_URL|html}exportcsv.php?poll={$poll_id|html}" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> {__('PollInfo', 'Export to CSV')}</a>
                    {if $admin && !$expired}
                        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-trash"></span> <span class="sr-only">{__('Generic', 'Remove')}</span> <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li><button class="btn btn-link" type="submit" name="remove_all_votes">{__('PollInfo', 'Remove all the votes') }</button></li>
                            <li><button class="btn btn-link" type="submit" name="remove_all_comments">{__('PollInfo', 'Remove all the comments')}</button></li>
                            <li class="divider" role="presentation"></li>
                            <li><button class="btn btn-link" type="submit" name="delete_poll">{__('PollInfo', 'Remove the poll')}</button></li>
                        </ul>
                    {/if}
                </div>
            </div>
        </div>
        <div class="row"> {* Admin name + email | Description *}
            <div class="form-group col-md-4">
                <div id="name-form">
                    <label class="control-label">{__('PollInfo', 'Initiator of the poll')}</label>
                    <p class="form-control-static">{$poll->admin_name|html}{if $admin && !$expired} <button class="btn btn-link btn-sm btn-edit" title="{__('PollInfo', 'Edit the name')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span></button>{/if}</p>
                    {if $admin && !$expired}
                    <div class="hidden js-name">
                        <label class="sr-only" for="newname">{__('PollInfo', 'Initiator of the poll')}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newname" name="name" size="40" value="{$poll->admin_name|html}" />
                            <span class="input-group-btn">
                            <button type="submit" class="btn btn-success" name="update_poll_info" value="name" title="{__('PollInfo', 'Save the new name')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Save')}</span></button>
                            <button class="btn btn-link btn-cancel" title="{__('PollInfo', 'Cancel the name edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{__('Generic', 'Cancel')}</span></button>
                            </span>
                        </div>
                    </div>
                    {/if}
                </div>
                {if $admin}
                <div id="email-form">
                    <p>{$poll->admin_mail|html}{if !$expired} <button class="btn btn-link btn-sm btn-edit" title="{__('PollInfo', 'Edit the email adress')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span></button>{/if}</p>
                    {if !$expired}
                        <div class="hidden js-email">
                            <label class="sr-only" for="admin_mail">{__('PollInfo', 'Email')}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="admin_mail" name="admin_mail" size="40" value="{$poll->admin_mail|html}" />
                            <span class="input-group-btn">
                                <button type="submit" name="update_poll_info" value="admin_mail" class="btn btn-success" title="{__('PollInfo', 'Save the email address')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Save')}</span></button>
                                <button class="btn btn-link btn-cancel" title="{__('PollInfo', 'Cancel the email address edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{__('Generic', 'Cancel')}</span></button>
                            </span>
                            </div>
                        </div>
                    {/if}
                </div>
                {/if}
            </div>
            {if $admin || preg_match('/[^ \r\n]/', $poll->description)}
                <div class="form-group col-md-8" id="description-form">
                    <label class="control-label">{__('Generic', 'Description')}{if $admin && !$expired} <button class="btn btn-link btn-sm btn-edit" title="{__('PollInfo', 'Edit the description')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span></button>{/if}</label>
                    <pre class="form-control-static well poll-description">{$poll->description|html}</pre>
                    {if $admin && !$expired}
                        <div class="hidden js-desc text-right">
                            <label class="sr-only" for="newdescription">{__('Generic', 'Description')}</label>
                            <textarea class="form-control" id="newdescription" name="description" rows="2" cols="40">{$poll->description|html}</textarea>
                            <button type="submit" id="btn-new-desc" name="update_poll_info" value="description" class="btn btn-sm btn-success" title="{__('PollInfo', 'Save the description')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Save')}</span></button>
                            <button class="btn btn-default btn-sm btn-cancel" title="{__('PollInfo', 'Cancel the description edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{__('Generic', 'Cancel')}</span></button>
                        </div>
                    {/if}
                </div>
            {/if}
        </div>
        <div class="row">
        </div>

        <div class="row">
            <div class="form-group form-group {if $admin}col-md-4{else}col-md-6{/if}">
                <label for="public-link"><a class="public-link" href="{poll_url id=$poll_id}">{__('PollInfo', 'Public link of the poll')} <span class="btn-link glyphicon glyphicon-link"></span></a></label>
                <input class="form-control" id="public-link" type="text" readonly="readonly" value="{poll_url id=$poll_id}" onclick="select();"/>
            </div>
            {if $admin}
                <div class="form-group col-md-4">
                    <label for="admin-link"><a class="admin-link" href="{poll_url id=$admin_poll_id admin=true}">{__('PollInfo', 'Admin link of the poll')} <span class="btn-link glyphicon glyphicon-link"></span></a></label>
                    <input class="form-control" id="admin-link" type="text" readonly="readonly" value="{poll_url id=$admin_poll_id admin=true}" onclick="select();"/>
                </div>
                <div id="expiration-form" class="form-group col-md-4">
                    <label class="control-label">{__('PollInfo', 'Expiration date')}</label>
                    <p>{$poll->end_date|date_format:$date_format['txt_date']|html}{if !$expired} <button class="btn btn-link btn-sm btn-edit" title="{__('PollInfo', 'Edit the expiration date')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span></button>{/if}</p>
                    {if !$expired}
                        <div class="hidden js-expiration">
                            <label class="sr-only" for="newexpirationdate">{__('PollInfo', 'Expiration date')}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="newexpirationdate" name="expiration_date" size="40" value="{$poll->end_date|date_format:$date_format['txt_date']|html}" />
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-success" name="update_poll_info" value="expiration_date" title="{__('PollInfo', 'Save the new expiration date')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Save')}</span></button>
                                    <button class="btn btn-link btn-cancel" title="{__('PollInfo', 'Cancel the expiration date edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{__('Generic', 'Cancel')}</span></button>
                                </span>
                            </div>
                        </div>
                    {/if}
                </div>
            {/if}
        </div>
        {if $admin}
            <div class="row">
                <div class="col-md-4 col-md-offset-4" >
                    <div id="poll-hidden-form">
                        {if $poll->hidden}
                            {$hidden_icon = "glyphicon-eye-close"}
                            {$hidden_text = __('PollInfo', 'Results are hidden.')}
                        {else}
                            {$hidden_icon = "glyphicon-eye-open"}
                            {$hidden_text = __('PollInfo', 'Results are visible.')}
                        {/if}
                        <p class=""><span class="glyphicon {$hidden_icon}"> </span> {$hidden_text}<button class="btn btn-link btn-sm btn-edit" title="{__('PollInfo', 'Edit the poll rules')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span></button></p>
                        <div class="hidden js-poll-hidden">
                            <div class="input-group">
                                <input type="checkbox" id="hidden" name="hidden" {if $poll->hidden}checked="checked"{/if}/>
                                <label for="hidden">{__('PollInfo', 'Results are hidden.')}</label>
                                <span class="input-group-btn">
                                    <button type="submit" name="update_poll_info" value="hidden" class="btn btn-success" title="{__('PollInfo', 'Save the new rules')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Save')}</span></button>
                                    <button class="btn btn-link btn-cancel" title="{__('PollInfo', 'Cancel the rules edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{__('Generic', 'Cancel')}</span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" >
                    <div id="poll-rules-form">
                        {if $poll->active}
                            {if $poll->editable}
                                {if $poll->editable == constant("Framadate\Editable::EDITABLE_BY_ALL")}
                                    {$rule_id = 2}
                                    {$rule_txt = __('PollInfo', 'Votes are editable')}
                                {else}
                                    {$rule_id = 3}
                                    {$rule_txt = __('PollInfo', 'Votes are editable solely by their owner.')}
                                {/if}
                                {$rule_icon = '<span class="glyphicon glyphicon-edit"></span>'}
                                {else}
                                {$rule_id = 1}
                                {$rule_icon = '<span class="glyphicon glyphicon-check"></span>'}
                                {$rule_txt = __('PollInfo', 'Votes and comments are open')}
                            {/if}
                        {else}
                            {$rule_id = 0}
                            {$rule_icon = '<span class="glyphicon glyphicon-lock"></span>'}
                            {$rule_txt = __('PollInfo', 'Votes and comments are locked')}
                        {/if}
                        <p class="">{$rule_icon} {$rule_txt|html}{if !$expired} <button class="btn btn-link btn-sm btn-edit" title="{__('PollInfo', 'Edit the poll rules')}"><span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span></button>{/if}</p>
                        {if !$expired}
                        <div class="hidden js-poll-rules">
                            <label class="sr-only" for="rules">{__('PollInfo', 'Poll rules')}</label>
                            <div class="input-group">
                                <select class="form-control" id="rules" name="rules">
                                    <option value="0"{if $rule_id==0} selected="selected"{/if}>{__('PollInfo', 'Votes and comments are locked')}</option>
                                    <option value="1"{if $rule_id==1} selected="selected"{/if}>{__('PollInfo', 'Votes and comments are open')}</option>
                                    <option value="2"{if $rule_id==2} selected="selected"{/if}>{__('PollInfo', 'Votes are editable')}</option>
                                    <option value="3"{if $rule_id==3} selected="selected"{/if}>{__('PollInfo', 'Votes are editable solely by their owner.')}</option>
                                </select>
                                <span class="input-group-btn">
                                    <button type="submit" name="update_poll_info" value="rules" class="btn btn-success" title="{__('PollInfo', 'Save the new rules')}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Save')}</span></button>
                                    <button class="btn btn-link btn-cancel" title="{__('PollInfo', 'Cancel the rules edit')}"><span class="glyphicon glyphicon-remove"></span><span class="sr-only">{__('Generic', 'Cancel')}</span></button>
                                </span>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        {/if}
    </div>
{if $admin}</form>{/if}
