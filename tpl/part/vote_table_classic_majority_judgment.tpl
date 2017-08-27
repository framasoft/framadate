{extends file="part/vote_table_classic_majority.tpl"}

{block name = add_choice}
    {* setting to reject as default choice so as to be neutral *}
    {$choice = constant('Framadate\VoteRating::TOREJECT')}
{/block}

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
            <i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'To reject')}</span>
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
    <td class="result-to-reject" headers="C{$id}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'To reject')}</span></td>
{else}
    <td class="bg-info" headers="C{$id}"><span class="sr-only">{__('Generic', 'Unknown')}</span></td>
{/if}
{/block}

{block name=total}
{/block}

{block name=chart}
{if !$hidden && count($votes)>0}
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
                
                var cols = [
                {foreach $slots as $id=>$slot}
                    $('<div/>').html('{$slot->title|markdown:true}').text(), 
                {/foreach}
                ];

                var resExcellent = [
                {foreach $slots as $id=>$slot}
                    {$best_choices[$id]['excellent']},
                {/foreach}
                ];
                var resGood = [
                {foreach $slots as $id=>$slot}
                    {$best_choices[$id]['good']},
                {/foreach}
                ]
                var resFair = [
                {foreach $slots as $id=>$slot}
                    {$best_choices[$id]['fair']},
                {/foreach}
                ]
                var resPoor = [
                {foreach $slots as $id=>$slot}
                    {$best_choices[$id]['poor']},
                {/foreach}
                ]
                var resToReject = [
                {foreach $slots as $id=>$slot}
                    {$best_choices[$id]['to-reject']},
                {/foreach}
                ]
                var resMedians = [
                {foreach $slots as $id=>$slot}
                    {if ($best_choices[$id]['total'] % 2 == 0)}
                        {$best_choices[$id]['total']/2 - 0.5},
                    {else}
                        {$best_choices[$id]['total']/2},
                    {/if}
                {/foreach}
                ]
                
                // resExcellent.shift();
                // resYes.shift();
                // console.info(JSON.stringify({json_encode($best_choices)}, null, 3));
                // console.info(JSON.stringify(resExcellent), null, 3);
                // console.info(resExcellent);

                var barChartData = {
                    labels : cols,
                    datasets : [
                    {
                        type: 'line',
                        label: "{__('Generic', 'Score')}",
                        yAxisID: "y-axis-1",
                        borderColor	 : "red",
                        fill: false,
                        data: resMedians
                    },
                    {
                        label: "{__('Generic', 'Excellent')}",
                        yAxisID: "y-axis-0",
                        backgroundColor : "#677835",
                        highlightFill: "#67753C",
                        barShowStroke : false,
                        data : resExcellent
                    },
                    {
                        label: "{__('Generic', 'Good')}",
                        yAxisID: "y-axis-0",
                        backgroundColor : "#BCD86A",
                        highlightFill : "#ABC661",
                        barShowStroke : false,
                        data : resGood
                    },
                    {
                        label: "{__('Generic', 'Fair')}",
                        yAxisID: "y-axis-0",
                        backgroundColor : "#C48A1B",
                        highlightFill : "#BD8A00",
                        barShowStroke : false,
                        data : resFair
                    },
                    {
                        label: "{__('Generic', 'Poor')}",
                        yAxisID: "y-axis-0",
                        backgroundColor : "#AD220F",
                        highlightFill : "#BF2511",
                        barShowStroke : false,
                        data : resPoor
                    },
                    {
                        label: "{__('Generic', 'To-reject')}",
                        yAxisID: "y-axis-0",
                        backgroundColor : "#5A5A5A",
                        highlightFill : "#585858&",
                        barShowStroke : false,
                        data : resToReject
                    }
                    ]
                };

                var ctx = document.getElementById("Chart").getContext("2d");
                window.myBar = new Chart(ctx, {
                    type: 'bar',
                    data: barChartData,
                    options: {
                        responsive : true,
                        scales: {
                          xAxes: [{
                            stacked: true
                          }],
                          yAxes: [{
                            stacked: true,
                            position: "left",
                            id: "y-axis-0"
                          }, {
                            stacked: false,
                            position: "right",
                            id: "y-axis-1",
                            ticks: {
                               beginAtZero: true,
                               suggestedMax: {$best_choices[0]['total']}
                           }
                          }]
                        }
                    }
                });
                return false;
            });
        });
    </script>
    
{/if}
{/block}

{block name=best_choices}
    {$mj_choices = array() }
    {$medians = array()}
    {foreach $slots as $id=>$slot}
        {foreach constant('Framadate\VoteRating::VOTERANGE') as $vote_value => $rank}
            {if isset($mj_choices[$id][0])}
                {$mj_choices[$id][] = end($mj_choices[$id]) + $best_choices[$id][$rank]}
            {else}
                {$mj_choices[$id][] = $best_choices[$id][$rank]}
            {/if}
            {if (!isset($medians[$id])) and (end($mj_choices[$id]) > ($best_choices[$id]['total']/2)) }
                {$medians[$id] = $rank}
            {/if}
        {/foreach}
        {*$medians[$id]*}
    {/foreach}
    
    <div class="row">
    <div class="col-sm-12"><h3>{__('Poll results', 'The ordered choices at this time are (tied votes are not sorted):')}</h3></div>
    <div class="col-sm-6 col-sm-offset-3 alert alert-info">
    {foreach array_reverse(constant('Framadate\VoteRating::VOTERANGE'), true) as $id => $rank}
        {$empty_rank = True}
        <ul style="list-style:none">
        {foreach $medians as $choice => $note}
            {if ($note == $rank)}
                {if $empty_rank}
                    {$empty_rank = False}               
                    <li><strong>{__('Generic', ucfirst($rank))}</strong></li>
                {/if}
                <li>* {$slots[$choice]->title} </li>
            {/if}
        {/foreach}  
        </ul>
    {/foreach}
    </div>
    </div>
{/block}