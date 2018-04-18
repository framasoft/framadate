{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
    {if $executing}
        <div class="row">
            <pre>{$output}</pre>
            <div class="col-xs-12 well well-sm">
                {__('Generic', 'Page generated in')} {$time} {__('Generic', 'seconds')}
            </div>
        </div>
    {/if}
    <div class="row">
        <div class="col-xs-12 col-md-4">
            <h2>{__('Admin', 'Status')}</h2>
            {if $countExecuted === $countTotal}
                <div class="alert alert-success">
                    No migrations to execute
                </div>
            {else}
                <div class="alert alert-danger">
                    <form method="POST">
                        <button type="submit" class="btn btn-danger btn-lg" name="execute">Execute migration</button>
                    </form>
                    <br />
                    {$countWaiting|html} migrations available.
                </div>
            {/if}
        </div>
        <div class="col-xs-12 col-md-4">
            <h2>{__('Admin', 'Summary')}</h2>
            {__('Admin', 'Waiting')} <span class="label label-warning">{$countWaiting|html} / {$countTotal|html}</span>
            <br/>
            {__('Admin', 'Executed')} <span class="label label-success">{$countExecuted|html} / {$countTotal|html}</span>
        </div>
    </div>
{/block}
