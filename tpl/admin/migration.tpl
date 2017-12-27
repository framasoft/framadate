{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
    <div class="row">
        <div class="col-xs-12 col-md-4">
            <h2>{__('Admin', 'Summary')}</h2>
            {__('Admin', 'Succeeded:')} <span class="label label-warning">{$countSucceeded|html} / {$countTotal|html}</span>
            <br/>
            {__('Admin', 'Failed:')} <span class="label label-danger">{$countFailed|html} / {$countTotal|html}</span>
            <br/>
            {__('Admin', 'Skipped:')} <span class="label label-info">{$countSkipped|html} / {$countTotal|html}</span>
        </div>
        <div class="col-xs-12 col-md-4">
            <h2>{__('Admin', 'Success')}</h2>
            <ul>
                {foreach $success as $s}
                    <li>{$s|html}</li>
                    {foreachelse}
                    <li>{__('Admin', 'Nothing')}</li>
                {/foreach}
            </ul>
        </div>

        <div class="col-xs-12 col-md-4">
            <h2>{__('Admin', 'Fail')}</h2>
            <ul>
                {foreach $fail as $f}
                    <li>{$f|html}</li>
                    {foreachelse}
                    <li>{__('Admin', 'Nothing')}</li>
                {/foreach}
            </ul>
        </div>

        <div class="col-xs-12 well well-sm">
            {__('Generic', 'Page generated in')} {$time} {__('Generic', 'seconds')}
        </div>
    </div>
{/block}
