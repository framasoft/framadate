{extends file='page.tpl'}

{block name=main}
    <form action="{poll_url id=$admin_poll_id admin=true}" method="POST">
        <div class="alert alert-info text-center">
            <h2>{__('adminstuds', 'Column\'s adding')}</h2>

            {if $format === 'D'}
                <div class="form-group">
                    <label for="newdate" class="col-md-4">{__('Generic', 'Day')}</label>
                    <div class="col-md-8">
                        <div class="input-group date">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                            <input type="text" id="newdate" data-date-format="{__('Date', 'dd/mm/yyyy')}" aria-describedby="dateformat" name="newdate" class="form-control" placeholder="{__('Date', 'dd/mm/yyyy')}" />
                        </div>
                        <span id="dateformat" class="sr-only">({__('Date', 'dd/mm/yyyy')})</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="newmoment" class="col-md-4">{__('Generic', 'Time')}</label>
                    <div class="col-md-8">
                        <input type="text" id="newmoment" name="newmoment" class="form-control" />
                    </div>
                </div>
            {else}
                <div class="form-group">
                    <label for="choice" class="col-md-4">{__('Generic', 'Choice')}</label>
                    <div class="col-md-8">
                        <input type="text" id="choice" name="choice" class="form-control" />
                    </div>
                </div>
            {/if}
            <div class="form-group">
                <button class="btn btn-default" type="submit" name="back">{__('adminstuds', 'Back to the poll')}</button>
                <button type="submit" name="confirm_add_column" class="btn btn-success">{__('adminstuds', 'Add a column')}</button>
            </div>
        </div>
    </form>
    <script type="text/javascript" src="js/app/framadatepicker.js"></script>
{/block}