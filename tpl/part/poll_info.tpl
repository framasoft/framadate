<div class="jumbotron">
    <div class="row">
        <div class="col-md-7">
            <h3>{$poll->title}</h3>
        </div>
        <div class="col-md-5">
            <div class="btn-group pull-right">
                <button onclick="print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span>{_('Print')}</button>
                <a href="{$SERVER_URL}export.php?poll={$poll_id}&mode=csv" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span>{_('Export to CSV')}</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <div class="form-group">
                <h4 class="control-label">{_("Initiator of the poll")}</h4>
                <p class="form-control-static">{$poll->admin_name}</p>
            </div>
            <div class="form-group">
                <label for="public-link"><a class="public-link" href="{$poll_id|poll_url}">{_("Public link of the poll")}<span class="btn-link glyphicon glyphicon-link"></span></a></label>
                <input class="form-control" id="public-link" type="text" readonly="readonly" value="{$poll_id|poll_url}" />
            </div>
        </div>

        {if !empty($poll->comment)}
            <div class="form-group col-md-7">
                <h4 class="control-label">{_("Description")}</h4><br />
                <p class="form-control-static well">{$poll->comment}</p>
            </div>
        {/if}
    </div>
</div>