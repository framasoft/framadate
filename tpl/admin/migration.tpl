{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
    <div class="row">
        <div class="col-xs-12 col-md-4">
            <h2>{_('Summary')}</h2>
            {_('Succeeded:')} <span class="label label-warning">{$countSucceeded} / {$countTotal}</span>
            <br/>
            {_('Failed:')} <span class="label label-danger">{$countFailed} / {$countTotal}</span>
            <br/>
            {_('Skipped:')} <span class="label label-info">{$countSkipped} / {$countTotal}</span>
        </div>
        <div class="col-xs-12 col-md-4">
            <h2>{_('Success')}</h2>
            <ul>
                {foreach $success as $s}
                    <li>{$s}</li>
                    {foreachelse}
                    <li>{_('Nothing')}</li>
                {/foreach}
            </ul>
        </div>

        <div class="col-xs-12 col-md-4">
            <h2>{_('Fail')}</h2>
            <ul>
                {foreach $fail as $f}
                    <li>{$f}</li>
                    {foreachelse}
                    <li>{_('Nothing')}</li>
                {/foreach}
            </ul>
        </div>
    </div>
{/block}