{* TODO Add a form maybe *}
<div class="jumbotron{if $admin} bg-danger{/if}">
    <div class="row">
        <div class="col-md-7">
            <h3>{$poll->title}</h3>
        </div>
        <div class="col-md-5">
            <div class="btn-group pull-right">
                <button onclick="print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span> {_('Print')}</button>
                <a href="{$SERVER_URL}export.php?poll={$poll_id}&mode=csv" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span> {_('Export to CSV')}</a>
                {if $admin|default:false}
                    <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-trash"></span> <span class="sr-only">' . _("Remove") . '</span> <span class="caret"></span>
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
        </div>
        {if !empty($poll->comment)}
            <div class="form-group col-md-7">
                <h4 class="control-label">{_("Description")}</h4><br />
                <p class="form-control-static well">{$poll->comment}</p>
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
</div>