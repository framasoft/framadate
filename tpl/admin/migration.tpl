{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
    <div class="row">
        <div class="col-xs-12 col-md-4">
            <h2>{_('Summary')}</h2>
            {_('Succeeded:')} <span class="label label-warning">{$countSucceeded|html} / {$countTotal|html}</span>
            <br/>
            {_('Failed:')} <span class="label label-danger">{$countFailed|html} / {$countTotal|html}</span>
            <br/>
            {_('Skipped:')} <span class="label label-info">{$countSkipped|html} / {$countTotal|html}</span>
        </div>
        <div class="col-xs-12 col-md-4">
            <h2>{_('Success')}</h2>
            <ul>
                {foreach $success as $s}
                    <li>{$s|html}</li>
                    {foreachelse}
                    <li>{_('Nothing')}</li>
                {/foreach}
            </ul>
        </div>

        <div class="col-xs-12 col-md-4">
            <h2>{_('Fail')}</h2>
            <ul>
                {foreach $fail as $f}
                    <li>{$f|html}</li>
                    {foreachelse}
                    <li>{_('Nothing')}</li>
                {/foreach}
            </ul>
        </div>

        <div class="col-xs-12 well well-sm">
            {_('Page generated in')} {$time} {_('secondes')}
        </div>
    </div>
{/block}