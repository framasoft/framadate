{if !is_array($best_choices) || empty($best_choices)}
    {$best_choices = [0]}
{/if}

<h3>
    {__('Poll results', 'Votes of the poll')} {if $hidden}<i>({__('PollInfo', 'Results are hidden')})</i>{/if}
    {if $accessGranted}
        <a href="" data-toggle="modal" data-target="#hint_modal"><i class="glyphicon glyphicon-info-sign"></i></a><!-- TODO Add accessibility -->
    {/if}
</h3>


{include 'part/scroll_left_right.tpl'}


<div id="tableContainer" class="tableContainer">
    <form action="{if $admin}{poll_url id=$admin_poll_id admin=true}{else}{poll_url id=$poll_id}{/if}" method="POST"  id="poll_form">
        <input type="hidden" name="control" value="{$slots_hash}"/>
        <table class="results">
            <caption class="sr-only">{__('Poll results', 'Votes of the poll')} {$poll->title|html}</caption>
            <thead>
            {if $admin && !$expired}
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    {foreach $slots as $id=>$slot}
                        <td headers="C{$id}">
                            <a href="{poll_url id=$admin_poll_id admin=true action='delete_column' action_value=$slot->title}"
                               data-remove-confirmation="{__('adminstuds', 'Confirm removal of the column.')}"
                               class="btn btn-link btn-sm remove-column" title="{__('adminstuds', 'Remove the column')} {$slot->title|html}">
                                <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only">{__('Generic', 'Remove')}</span>
                            </a>
                            </td>
                    {/foreach}
                    <td>
                        <a href="{poll_url id=$admin_poll_id admin=true action='add_column'}"
                           class="btn btn-link btn-sm" title="{__('adminstuds', 'Add a column')}">
                            <i class="glyphicon glyphicon-plus text-success"></i><span class="sr-only">{__('Poll results', 'Add a column')}</span>
                        </a>
                    </td>
                </tr>
            {/if}
            <tr>
                <th role="presentation"></th>
                {foreach $slots as $id=>$slot}
                    <th class="bg-info" id="C{$id}" title="{$slot->title|markdown:true}">{$slot->title|markdown}</th>
                {/foreach}
                <th></th>
            </tr>
            </thead>
            <tbody>
            {foreach $votes as $vote}
                {* Edited line *}

                {if $editingVoteId === $vote->uniqId && !$expired}

                <tr class="hidden-print">
                    <td class="bg-info btn-edit">
                        <div class="input-group input-group-sm" id="edit">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="hidden" name="edited_vote" value="{$vote->uniqId}"/>
                            <input type="text" id="name" name="name" value="{$vote->name|html}" class="form-control" title="{__('Generic', 'Your name')}" placeholder="{__('Generic', 'Your name')}" />
                        </div>
                    </td>

                    {$id=0}
                    {foreach $slots as $slot}
                        {$choice=$vote->choices[$id]}

                        <td class="bg-info" headers="C{$id}">
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
                                <li class="hide">
                                    <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value=" " {if $choice!='2' && $choice!='1' && $choice!='0'}checked {/if}/>
                                </li>
                            </ul>
                        </td>

                        {$id=$id + 1}
                    {/foreach}

                    <td class="btn-edit"><button type="submit" class="btn btn-success btn-xs" name="save" value="{$vote->id|html}" title="{__('Poll results', 'Save the choices')} {$vote->name|html}">{__('Generic', 'Save')}</button></td>
                </tr>
                {elseif !$hidden} {* Voted line *}
                <tr>

                    <th class="bg-info">{$vote->name|html}
                    {if $active && !$expired && $accessGranted &&
                    (
                    $poll->editable == constant('Framadate\Editable::EDITABLE_BY_ALL')
                    or $admin
                    or ($poll->editable == constant('Framadate\Editable::EDITABLE_BY_OWN') && $editedVoteUniqueId == $vote->uniqId)
                    ) && $slots gt 4
                    }
					<span class="edit-username-left">
						<a href="{if $admin}{poll_url id=$poll->admin_id vote_id=$vote->uniqId admin=true}{else}{poll_url id=$poll->id vote_id=$vote->uniqId}{/if}" class="btn btn-default btn-sm" title="{__f('Poll results', 'Edit the line: %s', $vote->name)|html}">
                    	<i class="glyphicon glyphicon-pencil"></i><span class="sr-only">{__('Generic', 'Edit')}</span>
                   	 	</a>
					</span>
					{/if}
					</th>

                    {$id=0}
                    {foreach $slots as $slot}
                        {$choice=$vote->choices[$id]}

                        {if $choice=='2'}
                            <td class="bg-success text-success" headers="C{$id}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Yes')}</span></td>
                        {elseif $choice=='1'}
                            <td class="bg-warning text-warning" headers="C{$id}">(<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span></td>
                        {elseif $choice=='0'}
                            <td class="bg-danger text-danger" headers="C{$id}"><i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'No')}</span></td>
                        {else}
                            <td class="bg-info" headers="C{$id}"><span class="sr-only">{__('Generic', 'Unknown')}</span></td>
                        {/if}

                        {$id=$id + 1}
                    {/foreach}

                    {if $active && !$expired && $accessGranted &&
                        (
                         $poll->editable == constant('Framadate\Editable::EDITABLE_BY_ALL')
                         or $admin
                         or ($poll->editable == constant('Framadate\Editable::EDITABLE_BY_OWN') && $editedVoteUniqueId == $vote->uniqId)
                        )
                    }

                        <td class="hidden-print">
                            <a href="{if $admin}{poll_url id=$poll->admin_id vote_id=$vote->uniqId admin=true}{else}{poll_url id=$poll->id vote_id=$vote->uniqId}{/if}" class="btn btn-default btn-sm" title="{__f('Poll results', 'Edit the line: %s', $vote->name)|html}">
                                <i class="glyphicon glyphicon-pencil"></i><span class="sr-only">{__('Generic', 'Edit')}</span>
                            </a>
                            {if $admin}
                                <a href="{poll_url id=$poll->id vote_id=$vote->uniqId}" class="btn btn-default btn-sm clipboard-url" data-toggle="popover" data-trigger="manual" title="{__('Poll results', 'Link to edit this particular line')}" data-content="{__('Poll results', 'Link to edit this particular line has been copied inside the clipboard!')}">
                                    <span class="btn-link glyphicon glyphicon-link"></span>
                                </a>
                                <a href="{poll_url id=$admin_poll_id admin=true action='delete_vote' action_value=$vote->id}"
                                   class="btn btn-default btn-sm"
                                   title="{__('Poll results', 'Remove the line:')} {$vote->name|html}">
                                    <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only">{__('Generic', 'Remove')}</span>
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

            {if $active && $editingVoteId === 0 && !$expired && $accessGranted}
                <tr id="vote-form" class="hidden-print">
                    <td class="bg-info" class="btn-edit">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="name" name="name" class="form-control" title="{__('Generic', 'Your name')}" placeholder="{__('Generic', 'Your name')}" />
                        </div>
                    </td>
					{$i = 0}
                    {foreach $slots as $id=>$slot}
                        <td class="bg-info" headers="C{$id}">
                            <ul class="list-unstyled choice">
								{if $poll->ValueMax eq NULL || $best_choices['y'][$i] lt $poll->ValueMax}
                               	 	<li class="yes">
                                    	<input type="radio" id="y-choice-{$id}" name="choices[{$id}]" value="2"
                                    		{(!isset($selectedNewVotes[$id]) || ("2" !== $selectedNewVotes[$id])) ? "" : " checked"}
                                    	/>
                                    	<label class="btn btn-default btn-xs" for="y-choice-{$id}" title="{__('Poll results', 'Vote yes for')|html} {$slot->title|html}">
                                       		<i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Yes')}</span>
                                    	</label>
                                	</li>
                               		 <li class="ifneedbe">
                                  	  <input type="radio" id="i-choice-{$id}" name="choices[{$id}]" value="1"
                                    		{(!isset($selectedNewVotes[$id]) || ("1" !== $selectedNewVotes[$id])) ? "" : " checked"}
                                    	/>
                                  	  <label class="btn btn-default btn-xs" for="i-choice-{$id}" title="{__('Poll results', 'Vote ifneedbe for')|html} {$slot->title|html}">
                                        (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span>
                                    	</label>
                               	 	</li>
								{/if}
                                <li class="no">
                                    <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value="0"
                                		{(!isset($selectedNewVotes[$id]) || ("0" !== $selectedNewVotes[$id])) ? "" : " checked"}
                                	/>
                                    <label class="btn btn-default btn-xs {(!isset($selectedNewVotes[$id]) || ("0" !== $selectedNewVotes[$id])) ? "startunchecked" : ""}" for="n-choice-{$id}" title="{__('Poll results', 'Vote no for')|html} {$slot->title|html}">
                                        <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'No')}</span>
                                    </label>
                                </li>
                                <li class="hide">
                                  <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value=" "
                                		{(isset($selectedNewVotes[$id]) && ("" !== $selectedNewVotes[$id])) ? "" : " checked"}
                                	/>
                                </li>
                            </ul>
                        </td>
						{$i = $i+1}

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
    <script>
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
                    "{$slot->title|markdown:true|addslashes}",
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
                <ul class="list-unstyled">
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
