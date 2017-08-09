{if $choice=='4'}
    <td class="result-excellent" headers="C{$id}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Excellent')}</span></td>
{elseif $choice=='3'}
    <td class="result-good" headers="C{$id}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Good')}</span></td>
{elseif $choice=='2'}
    <td class="result-fair" headers="C{$id}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Medium')}</span></td>
{elseif $choice=='1'}
    <td class="result-poor" headers="C{$id}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Poor')}</span></td>
{elseif $choice=='0'}
    <td class="result-to-reject" headers="C{$id}"><i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'To reject')}</span></td>
{else}
    <td class="bg-info" headers="C{$id}"><span class="sr-only">{__('Generic', 'Unknown')}</span></td>
{/if}