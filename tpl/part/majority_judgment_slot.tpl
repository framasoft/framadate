<ul class="list-unstyled choice">
    <li class="excellent">
        <input type="radio" id="e-choice-{$id}" name="choices[{$id}]" value="4" {if $choice=='4'}checked {/if}/>
        <label class="btn btn-default btn-xs" for="e-choice-{$id}" title="{__('Poll results', 'Vote excellent for')|html} {$slots[$id]->title|html}">
            <i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Excellent')}</span>
        </label>
    </li>
    <li class="good">
        <input type="radio" id="g-choice-{$id}" name="choices[{$id}]" value="3" {if $choice=='3'}checked {/if}/>
        <label class="btn btn-default btn-xs" for="g-choice-{$id}" title="{__('Poll results', 'Vote good for')|html} {$slots[$id]->title|html}">
            <i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Good')}</span>
        </label>
    </li>
    <li class="fair">
        <input type="radio" id="m-choice-{$id}" name="choices[{$id}]" value="2" {if $choice=='2'}checked {/if}/>
        <label class="btn btn-default btn-xs" for="m-choice-{$id}" title="{__('Poll results', 'Vote fair for')|html} {$slots[$id]->title|html}">
            <i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Fair')}</span>
        </label>
    </li>
    <li class="poor">
        <input type="radio" id="p-choice-{$id}" name="choices[{$id}]" value="1" {if $choice=='1'}checked {/if}/>
        <label class="btn btn-default btn-xs" for="p-choice-{$id}" title="{__('Poll results', 'Vote poor for')|html} {$slots[$id]->title|html}">
            <i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Poor')}</span>
        </label>
    </li>
    <li class="to-reject">
        <input type="radio" id="tr-choice-{$id}" name="choices[{$id}]" value="0" {if $choice=='0'}checked {/if}/>
        <label class="btn btn-default btn-xs" for="tr-choice-{$id}" title="{__('Poll results', 'Vote to reject for')|html} {$slots[$id]->title|html}">
            <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'To reject')}</span>
        </label>
    </li>
    <li style="display:none">
        <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value=" " {if $choice!='4' && $choice!='3' && $choice!='2' && $choice!='1' && $choice!='0'}checked {/if}/>
    </li>
</ul>