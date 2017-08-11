{extends file="part/vote_table_classic_majority.tpl"}

{block name=display_slot}
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
{/block}

{block name=display_choice}
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
{/block}

{block name=total}
{/block}

{if !$hidden}
    <div class="row" aria-hidden="true">
        <div class="col-xs-12">
            <p class="text-center" id="showChart">
                <button class="btn btn-lg btn-default">
                    <span class="fa fa-fw fa-bar-chart"></span> {__('Poll results', 'Display the chart of the results')}
                </button>
            </p>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#showChart').on('click', function() {
                $('#showChart')
                        .after("<h3>{__('Poll results', 'Chart')}</h3><canvas id=\"Chart\"></canvas>")
                        .remove();
                
                var resIfneedbe = [];
                var resYes = [];
            
                $('#addition').find('td').each(function () {
                    var inbCountText = $(this).find('.inb-count').text();
                    if(inbCountText != '' && inbCountText != undefined) {
                        resIfneedbe.push($(this).find('.inb-count').html())
                    } else {
                        resIfneedbe.push(0);
                    }

                    var yesCountText = $(this).find('.yes-count').text();
                    if(yesCountText != '' && yesCountText != undefined) {
                        resYes.push($(this).find('.yes-count').html())
                    } else {
                        resYes.push(0);
                    }
                });
                var cols = [
                {foreach $slots as $id=>$slot}
                    $('<div/>').html('{$slot->title|markdown:true}').text(), 
                {/foreach}
                ];

                resIfneedbe.shift();
                resYes.shift();

                var barChartData = {
                    labels : cols,
                    datasets : [
                    {
                        label: "{__('Generic', 'Ifneedbe')}",
                        fillColor : "rgba(255,207,79,0.8)",
                        highlightFill: "rgba(255,207,79,1)",
                        barShowStroke : false,
                        data : resIfneedbe
                    },
                    {
                        label: "{__('Generic', 'Yes')}",
                        fillColor : "rgba(103,120,53,0.8)",
                        highlightFill : "rgba(103,120,53,1)",
                        barShowStroke : false,
                        data : resYes
                    }
                    ]
                };

                var ctx = document.getElementById("Chart").getContext("2d");
                window.myBar = new Chart(ctx).StackedBar(barChartData, {
                    responsive : true
                });
                return false;
            });
        });
    </script>
    
{/if}
{/chart}