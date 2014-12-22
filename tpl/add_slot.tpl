{extends file='page.tpl'}

{block name=main}
    <form action="{$admin_poll_id|poll_url:true}" method="POST">
        <div class="alert alert-info text-center">
            <h2>{_("Column's adding")}</h2>

            <div class="form-group">
                <label for="newdate" class="col-md-4">{_("Day")}</label>
                <div class="col-md-8">
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                        <input type="text" id="newdate" data-date-format="{_("dd/mm/yyyy")}" aria-describedby="dateformat" name="newdate" class="form-control" placeholder="{_("dd/mm/yyyy")}" />
                    </div>
                    <span id="dateformat" class="sr-only">{_("(dd/mm/yyyy)")}</span>
                </div>
            </div>
            <div class="form-group">
                <label for="newhour" class="col-md-4">{_("Time")}</label>
                <div class="col-md-8">
                    <input type="text" id="newhour" name="newhour" class="form-control" />
                </div>
            </div>
            <div class="pull-right">
                <button class="btn btn-default" type="submit" name="back">{_('Back to the poll')}</button>
                <button type="submit" name="confirm_add_slot" class="btn btn-success">{_('Add a column')}</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
{/block}