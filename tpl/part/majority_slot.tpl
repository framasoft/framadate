<ul class="list-unstyled choice">
    <li class="yes">
        <input type="radio" id="y-choice-{$id}" name="choices[{$id}]" value="2" {if $choice=='2'}checked {/if}/>
        <label class="btn btn-default btn-xs" for="y-choice-{$id}" title="{__('Poll results', 'Vote yes for')|html} {$slots[$id]->title|html}">
            <i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Yes')}</span>
        </label>
    </li>
    <li class="ifneedbe">
        <input type="radio" id="i-choice-{$id}" name="choices[{$id}]" value="1" {if $choice=='1'}checked {/if}/>
        <label class="btn btn-default btn-xs" for="i-choice-{$id}" title="{__('Poll results', 'Vote ifneedbe for')|html} {$slots[$id]->title|html}">
            (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span>
        </label>
    </li>
    <li class="no">
        <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value="0" {if $choice=='0'}checked {/if}/>
        <label class="btn btn-default btn-xs" for="n-choice-{$id}" title="{__('Poll results', 'Vote no for')|html} {$slots[$id]->title|html}">
            <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'No')}</span>
        </label>
    </li>
    <li style="display:none">
        <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value=" " {if $choice!='2' && $choice!='1' && $choice!='0'}checked {/if}/>
    </li>
</ul>