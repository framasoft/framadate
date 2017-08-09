{if $choice=='2'}
    <td class="bg-success text-success" headers="C{$id}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Yes')}</span></td>
{elseif $choice=='1'}
    <td class="bg-warning text-warning" headers="C{$id}">(<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span></td>
{elseif $choice=='0'}
    <td class="bg-danger text-danger" headers="C{$id}"><i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'No')}</span></td>
{else}
    <td class="bg-info" headers="C{$id}"><span class="sr-only">{__('Generic', 'Unknown')}</span></td>
{/if}