{if !is_array($best_choices) || empty($best_choices)}
    {$best_choices = [0]}
{/if}

<h3>{__('Poll results', 'Votes of the poll')} {if $hidden}<i>({__('PollInfo', 'Results are hidden.')})</i>{/if}</h3>

<div id="tableContainer" class="tableContainer">
    <form action="{if $admin}{poll_url id=$admin_poll_id admin=true}{else}{poll_url id=$poll_id}{/if}" method="POST"  id="poll_form">
        <table class="results">
            <caption class="sr-only">{__('Poll results', 'Votes of the poll')} {$poll->title|html}</caption>
            <thead>
            {if $admin && !$expired}
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    {foreach $slots as $id=>$slot}
                        <td headers="C{$id}">
                            <a href="{poll_url id=$admin_poll_id admin=true action='delete_column' action_value=$slot->title}"
                               class="btn btn-link btn-sm" title="{__('adminstuds', 'Remove the column')} {$slot->title|html}">
                                <span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">{__('Generic', 'Remove')}</span>
                            </a>
                            </td>
                    {/foreach}
                    <td>
                        <a href="{poll_url id=$admin_poll_id admin=true action='add_slot' action_value=true}"
                           class="btn btn-link btn-sm" title="{__('adminstuds', 'Add a column')} {$slot->title|html}">
                            <span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">{__('Poll results', 'Add a column')}</span>
                        </a>
                    </td>
                </tr>
            {/if}
            <tr>
                <th role="presentation"></th>
                {foreach $slots as $id=>$slot}
                    <th class="bg-info" id="C{$id}">{$slot->title|markdown}</th>
                {/foreach}
                <th></th>
            </tr>
            </thead>
            <tbody>
            {foreach $votes as $vote}

                {if $editingVoteId === $vote->uniqId} {* Edited line *}

                <tr class="hidden-print">
                    <td class="bg-info" style="padding:5px">
                        <div class="input-group input-group-sm" id="edit">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                            <input type="hidden" name="edited_vote" value="{$vote->uniqId}"/>
                            <input type="text" id="name" name="name" value="{$vote->name|html}" class="form-control" title="{__('Generic', 'Your name')}" placeholder="{__('Generic', 'Your name')}" />
                        </div>
                    </td>

                    {foreach $vote->choices as $id=>$choice}

                        <td class="bg-info" headers="C{$id}">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-{$id}" name="choices[{$id}]" value="2" {if $choice==2}checked {/if}/>
                                    <label class="btn btn-default btn-xs" for="y-choice-{$id}" title="{__('Poll results', 'Vote yes for')|html} {$slots[$id]->title|html}">
                                        <span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Yes')}</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-{$id}" name="choices[{$id}]" value="1" {if $choice==1}checked {/if}/>
                                    <label class="btn btn-default btn-xs" for="i-choice-{$id}" title="{__('Poll results', 'Vote ifneedbe for')|html} {$slots[$id]->title|html}">
                                        (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value="0" {if $choice==0}checked {/if}/>
                                    <label class="btn btn-default btn-xs" for="n-choice-{$id}" title="{__('Poll results', 'Vote no for')|html} {$slots[$id]->title|html}">
                                        <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">{__('Generic', 'No')}</span>
                                    </label>
                                </li>
                            </ul>
                        </td>
                    {/foreach}
                    <td style="padding:5px"><button type="submit" class="btn btn-success btn-xs" name="save" value="{$vote->id|html}" title="{__('Poll results', 'Save the choices')} {$vote->name|html}">{__('Generic', 'Save')}</button></td>
                </tr>
                {elseif !$hidden} {* Voted line *}
                <tr>

                    <th class="bg-info">{$vote->name|html}</th>

                    {foreach $vote->choices as $id=>$choice}

                        {if $choice==2}
                            <td class="bg-success text-success" headers="C{$id}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Yes')}</span></td>
                        {elseif $choice==1}
                            <td class="bg-warning text-warning" headers="C{$id}">(<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span></td>
                        {else}
                            <td class="bg-danger" headers="C{$id}"><span class="sr-only">{__('Generic', 'No')}</span></td>
                        {/if}

                    {/foreach}

                    {if $active && !$expired && ($poll->editable == constant('Framadate\Editable::EDITABLE_BY_ALL') or $admin)}
                        <td class="hidden-print">
                            <a href="{if $admin}{poll_url id=$poll->admin_id vote_id=$vote->uniqId admin=true}{else}{poll_url id=$poll->id vote_id=$vote->uniqId}{/if}" class="btn btn-default btn-sm" title="{__('Poll results', 'Edit the line:')|html} {$vote->name|html}">
                                <span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{__('Generic', 'Edit')}</span>
                            </a>
                            {if $admin}
                                <a href="{poll_url id=$admin_poll_id admin=true action='delete_vote' action_value=$vote->id}"
                                   class="btn btn-default btn-sm"
                                   title="{__('Poll results', 'Remove the line:')} {$vote->name|html}">
                                    <span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">{__('Generic', 'Remove')}</span>
                                </a>
                            {/if}
                        </td>
                    {else}
                        <td></td>
                    {/if}
                </tr>
                {/if}
            {/foreach}

            {* Line to add a new vote *}

            {if $active && $editingVoteId === 0 && !$expired}
                <tr id="vote-form" class="hidden-print">
                    <td class="bg-info" style="padding:5px">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                            <input type="text" id="name" name="name" class="form-control" title="{__('Generic', 'Your name')}" placeholder="{__('Generic', 'Your name')}" />
                        </div>
                    </td>
                    {foreach $slots as $id=>$slot}
                        <td class="bg-info" headers="C{$id}">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-{$id}" name="choices[{$id}]" value="2" />
                                    <label class="btn btn-default btn-xs" for="y-choice-{$id}" title="{__('Poll results', 'Vote yes for')|html} {$slot->title|html}">
                                        <span class="glyphicon glyphicon-ok"></span><span class="sr-only">{__('Generic', 'Yes')}</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-{$id}" name="choices[{$id}]" value="1" />
                                    <label class="btn btn-default btn-xs" for="i-choice-{$id}" title="{__('Poll results', 'Vote ifneedbe for')|html} {$slot->title|html}">
                                        (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value="0" checked/>
                                    <label class="btn btn-default btn-xs" for="n-choice-{$id}" title="{__('Poll results', 'Vote no for')|html} {$slot->title|html}">
                                        <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">{__('Generic', 'No')}</span>
                                    </label>
                                </li>
                            </ul>
                        </td>
                    {/foreach}
                    <td><button type="submit" class="btn btn-success btn-md" name="save" title="{__('Poll results', 'Save the choices')}">{__('Generic', 'Save')}</button></td>
                </tr>
            {/if}

            {if !$hidden}
                {* Line displaying best moments *}
                {$count_bests = 0}
                {$max = max($best_choices['y'])}
                {if $max > 0}
                    <tr id="addition">
                        <td>{__('Poll results', 'Addition')}<br/>{$votes|count} {if ($votes|count)==1}{__('Poll results', 'polled user')}{else}{__('Poll results', 'polled users')}{/if}</td>
                        {foreach $best_choices['y'] as $i=>$best_choice}
                            {if $max == $best_choice}
                                {$count_bests = $count_bests +1}
                                <td><i class="glyphicon glyphicon-star text-info"></i><span class="yes-count">{$best_choice|html}</span>{if $best_choices['inb'][$i]>0}<br/><span class="small text-muted">(+<span class="inb-count">{$best_choices['inb'][$i]|html}</span>)</span>{/if}</td>
                            {elseif $best_choice > 0}
                                <td><span class="yes-count">{$best_choice|html}</span>{if $best_choices['inb'][$i]>0}<br/><span class="small text-muted">(+<span class="inb-count">{$best_choices['inb'][$i]|html}</span>)</span>{/if}</td>
                            {elseif $best_choices['inb'][$i]>0}
                                <td><br/><span class="small text-muted">(+<span class="inb-count">{$best_choices['inb'][$i]|html}</span>)</span></td>
                            {else}
                                <td></td>
                            {/if}
                        {/foreach}
                    </tr>
                {/if}
            {/if}
            </tbody>
        </table>
    </form>
</div>

{if !$hidden && $max > 0}
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
                $('#showChart').after("<h3>{__('Poll results', 'Chart')}</h3><canvas id=\"Chart\"></canvas>");
                $('#showChart').remove();
                
                var resIfneedbe = [];
                var resYes = [];
            
                $('#addition').find('td').each(function (colIndex) {
                    if($(this).find('.inb-count').text()!='' && $(this).find('.inb-count').text()!=undefined) {
                        resIfneedbe.push($(this).find('.inb-count').html())
                    } else {
                        resIfneedbe.push(0);
                    }
                    if($(this).find('.yes-count').text()!='' && $(this).find('.yes-count').text()!=undefined) {
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

                resIfneedbe.shift(); resIfneedbe.pop();
                resYes.shift(); resYes.pop();
                console.log(resYes);
                console.log(resIfneedbe);
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



{if !$hidden}
    {* Best votes listing *}
    {$max = max($best_choices['y'])}
    {if $max > 0}
        <div class="row">
        {if $count_bests == 1}
        <div class="col-sm-12"><h3>{__('Poll results', 'Best choice')}</h3></div>
        <div class="col-sm-6 col-sm-offset-3 alert alert-info">
            <p><i class="glyphicon glyphicon-star text-info"></i> {__('Poll results', 'The best choice at this time is:')}</p>
            {elseif $count_bests > 1}
            <div class="col-sm-12"><h3>{__('Poll results', 'Best choices')}</h3></div>
            <div class="col-sm-6 col-sm-offset-3 alert alert-info">
                <p><i class="glyphicon glyphicon-star text-info"></i> {__('Poll results', 'The bests choices at this time are:')}</p>
                {/if}


                {$i = 0}
                <ul style="list-style:none">
                    {foreach $slots as $slot}
                        {if $best_choices['y'][$i] == $max}
                            <li><strong>{$slot->title|markdown:true}</strong></li>
                        {/if}
                        {$i = $i+1}
                    {/foreach}
                </ul>
                <p>{__('Generic', 'with')} <b>{$max|html}</b> {if $max==1}{__('Generic', 'vote')}{else}{__('Generic', 'votes')}{/if}.</p>
            </div>
        </div>
    {/if}
{/if}
