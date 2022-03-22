{if !is_array($best_choices) || empty($best_choices)}
    {$best_choices = [0]}
{/if}

<h3>
    {__('Poll results', 'Votes of the poll')} {if $hidden}<i>({__('PollInfo', 'Results are hidden')})</i>{/if}
    {if $accessGranted}
        <a href="" data-toggle="modal" data-target="#hint_modal"><i class="glyphicon glyphicon-info-sign"></i></a>
    {/if}
</h3>


{include 'part/scroll_left_right.tpl'}


<div id="tableContainer" class="tableContainer">
    <form action="{if $admin}{poll_url id=$admin_poll_id admin=true}{else}{poll_url id=$poll_id}{/if}" method="POST" id="poll_form">
        <input type="hidden" name="control" value="{$slots_hash}"/>
        <table class="results">
            <caption class="sr-only">{__('Poll results', 'Votes of the poll')} {$poll->title|html}</caption>
            <thead>
            {if $admin && !$expired}
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    {$headersDCount=0}
                    {foreach $slots as $slot}
                        {foreach $slot->moments as $id=>$moment}
                            <td headers="M{$slot@key} D{$headersDCount} H{$headersDCount}">
                                <a href="{poll_url id=$admin_poll_id admin=true action='delete_column' action_value=$slot->day|cat:'@'|cat:$moment}"
                                   data-remove-confirmation="{__('adminstuds', 'Confirm removal of the column.')}"
                                   class="btn btn-link btn-sm remove-column"
                                   title="{__('adminstuds', 'Remove the column')} {$slot->day|intl_date_format:$date_format.txt_short|html} - {$moment|html}">
                                    <i class="glyphicon glyphicon-remove text-danger"></i><span class="sr-only">{__('Generic', 'Remove')}</span>
                                </a>
                            </td>
                            {$headersDCount = $headersDCount+1}
                        {/foreach}
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
                {$count_same = 0}
                {$previous = 0}
                {foreach $slots as $id=>$slot}
                    {$display = $slot->day|intl_date_format:$date_format.txt_month_year|html}
                    {if $previous !== 0 && $previous != $display}
                        <th colspan="{$count_same}" class="bg-primary month" id="M{$id}">{$previous}</th>
                        {$count_same = 0}
                    {/if}

                    {$count_same = $count_same + ($slot->moments|count)}

                    {if $slot@last}
                        <th colspan="{$count_same}" class="bg-primary month" id="M{$id}">{$display}</th>
                    {/if}

                    {$previous = $display}

                    {for $foo=0 to ($slot->moments|count)-1}
                        {append var='headersM' value=$id}
                    {/for}
                {/foreach}
                <th></th>
            </tr>
            <tr>
                <th role="presentation"></th>
                {foreach $slots as $id=>$slot}
                    <th colspan="{$slot->moments|count}" class="bg-primary day" id="D{$id}">{$slot->day|intl_date_format:$date_format.txt_day|html}</th>
                    {for $foo=0 to ($slot->moments|count)-1}
                        {append var='headersD' value=$id}
                    {/for}
                {/foreach}
                <th></th>
            </tr>
            <tr>
                <th role="presentation"></th>
                {$headersDCount=0}
                {$slots_raw = array()}
                {foreach $slots as $slot}
                    {foreach $slot->moments as $id=>$moment}
                        <th colspan="1" class="bg-info" id="H{$headersDCount}">{$moment|html}</th>
                        {append var='headersH' value=$headersDCount}
                        {$headersDCount = $headersDCount+1}
                        {$slots_raw[] = $slot->day|intl_date_format:$date_format.txt_full|cat:' - '|cat:$moment}
                    {/foreach}
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


                    {$k=0}
                    {foreach $slots as $slot}
                      {foreach $slot->moments as $moment}
                        {$choice=$vote->choices[$k]}


                        <td class="bg-info" headers="M{$headersM[$k]} D{$headersD[$k]} H{$headersH[$k]}">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-{$k}" name="choices[{$k}]" value="2" {if $choice=='2'}checked {/if}/>
                                    <label class="btn btn-default btn-xs" for="y-choice-{$k}" title="{__('Poll results', 'Vote yes for')|html} {$slots_raw[$k]}">
                                        <i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Yes')}</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-{$k}" name="choices[{$k}]" value="1" {if $choice=='1'}checked {/if}/>
                                    <label class="btn btn-default btn-xs" for="i-choice-{$k}" title="{__('Poll results', 'Vote ifneedbe for')|html} {$slots_raw[$k]}">
                                        (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-{$k}" name="choices[{$k}]" value="0" {if $choice=='0'}checked {/if}/>
                                    <label class="btn btn-default btn-xs" for="n-choice-{$k}" title="{__('Poll results', 'Vote no for')|html} {$slots_raw[$k]}">
                                        <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'No')}</span>
                                    </label>
                                </li>
                                <li class="hide">
                                    <input type="radio" id="n-choice-{$k}" name="choices[{$k}]" value=" " {if $choice!='2' && $choice!='1' && $choice!='0'}checked {/if}/>
                                </li>
                            </ul>
                        </td>

                        {$k=$k + 1}
                      {/foreach}
                    {/foreach}

                    <td class="btn-edit"><button type="submit" class="btn btn-success btn-xs" name="save" value="{$vote->id|html}" title="{__('Poll results', 'Save the choices')} {$vote->name|html}">{__('Generic', 'Save')}</button></td>

                </tr>
                {elseif !$hidden}
                <tr>

                    {* Voted line *}
                    <th class="bg-info">{$vote->name|html}
                    {if $active && !$expired && $accessGranted &&
                        (
                        $poll->editable == constant('Framadate\Editable::EDITABLE_BY_ALL')
                        or $admin
                        or ($poll->editable == constant('Framadate\Editable::EDITABLE_BY_OWN') && $editedVoteUniqueId == $vote->uniqId)
                        ) &&
                    $slots|count gt 4
                    }
						<span class="edit-username-left">
							<a href="{if $admin}{poll_url id=$poll->admin_id vote_id=$vote->uniqId admin=true}{else}{poll_url id=$poll->id vote_id=$vote->uniqId}{/if}" class="btn btn-default btn-sm" title="{__f('Poll results', 'Edit the line: %s', $vote->name)|html}">
                       		<i class="glyphicon glyphicon-pencil"></i><span class="sr-only">{__('Generic', 'Edit')}</span>
                       		</a>
					</span>
					{/if}
					</th>




                    {$k=0}
                    {foreach $slots as $slot}
                      {foreach $slot->moments as $moment}
                        {$choice=$vote->choices[$k]}

                        {if $choice=='2'}
                            <td class="bg-success text-success" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}"><i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Yes')}</span></td>
                        {elseif $choice=='1'}
                            <td class="bg-warning text-warning" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}">(<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span></td>
                        {elseif $choice=='0'}
                            <td class="bg-danger text-danger" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}"><i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'No')}</span></td>
                        {else}
                            <td class="bg-info" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}"><span class="sr-only">{__('Generic', 'Unknown')}</span></td>
                        {/if}

                        {$k=$k + 1}
                      {/foreach}
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
                                    <i class="glyphicon glyphicon-link"></i><span class="sr-only">{__('Generic', 'Link')}</span>
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
                    <td class="bg-info btn-edit">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" id="name" name="name" class="form-control" title="{__('Generic', 'Your name')}" placeholder="{__('Generic', 'Your name')}" />
                        </div>
                    </td>


                    {$i = 0}
                    {foreach $slots as $slot}
                        {foreach $slot->moments as $moment}

                            <td class="bg-info" headers="M{$headersM[$i]} D{$headersD[$i]} H{$headersH[$i]}">
                                <ul class="list-unstyled choice">
                                    {if $poll->ValueMax eq NULL || $best_choices['y'][$i] lt $poll->ValueMax}
                                    <li class="yes">
                                        <input type="radio" id="y-choice-{$i}" name="choices[{$i}]" value="2"
                                        	{(!isset($selectedNewVotes[$i]) || ("2" !== $selectedNewVotes[$i])) ? "" : " checked"}
                                        />
                                        <label class="btn btn-default btn-xs" for="y-choice-{$i}" title="{__('Poll results', 'Vote yes for')|html} {$slot->day|intl_date_format:$date_format.txt_short|html} - {$moment|html}">
                                            <i class="glyphicon glyphicon-ok"></i><span class="sr-only">{__('Generic', 'Yes')}</span>
                                        </label>
                                    </li>
                                    <li class="ifneedbe">
                                        <input type="radio" id="i-choice-{$i}" name="choices[{$i}]" value="1"
                                        	{(!isset($selectedNewVotes[$i]) || ("1" !== $selectedNewVotes[$i])) ? "" : " checked"}
                                        />
                                        <label class="btn btn-default btn-xs" for="i-choice-{$i}" title="{__('Poll results', 'Vote ifneedbe for')|html} {$slot->day|intl_date_format:$date_format.txt_short|html} - {$moment|html}">
                                            (<i class="glyphicon glyphicon-ok"></i>)<span class="sr-only">{__('Generic', 'Ifneedbe')}</span>
                                        </label>
                                    </li>
                                    {/if}

                                    <li class="no">
                                        <input type="radio" id="n-choice-{$i}" name="choices[{$i}]" value="0"
                                        	{(!isset($selectedNewVotes[$i]) || ("0" !== $selectedNewVotes[$i])) ? "" : " checked"}
                                        />
                                        <label class="btn btn-default btn-xs {(!isset($selectedNewVotes[$i]) || ("0" !== $selectedNewVotes[$i])) ? "startunchecked" : ""}" for="n-choice-{$i}" title="{__('Poll results', 'Vote no for')|html} {$slot->day|intl_date_format:$date_format.txt_short|html} - {$moment|html}">
                                            <i class="glyphicon glyphicon-ban-circle"></i><span class="sr-only">{__('Generic', 'No')}</span>
                                        </label>
                                    </li>
                                    <li class="hide">
                                      <input type="radio" id="n-choice-{$i}" name="choices[{$i}]" value=" "
                                      	{(isset($selectedNewVotes[$i]) && ("" !== $selectedNewVotes[$i])) ? "" : " checked"}
                                      />
                                    </li>
                                </ul>
                            </td>

                            {$i = $i+1}
                        {/foreach}
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
                        {foreach $best_choices['y'] as $i=>$best_moment}
                            {if $max == $best_moment}
                                {$count_bests = $count_bests +1}
                                <td><i class="glyphicon glyphicon-star text-info"></i><span class="yes-count">{$best_moment|html}</span>{if $best_choices['inb'][$i]>0}<br/><span class="small text-muted">(+<span class="inb-count">{$best_choices['inb'][$i]|html}</span>)</span>{/if}</td>
                            {elseif $best_moment > 0}
                                <td><span class="yes-count">{$best_moment|html}</span>{if $best_choices['inb'][$i]>0}<br/><span class="small text-muted">(+<span class="inb-count">{$best_choices['inb'][$i]|html}</span>)</span>{/if}</td>
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
                        resIfneedbe.push(inbCountText)
                    } else {
                        resIfneedbe.push(0);
                    }
                    var yesCountText = $(this).find('.yes-count').text();
                    if(yesCountText != '' && yesCountText != undefined) {
                        resYes.push(yesCountText)
                    } else {
                        resYes.push(0);
                    }
                });
                var cols = [
                {foreach $slots as $slot}
                    {foreach $slot->moments as $moment}
                        $('<div/>').html('{$slot->day|intl_date_format:$date_format.txt_short|html} - {$moment|html}').text(),
                    {/foreach}
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
        <div class="row best-choice">
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
                        {foreach $slot->moments as $moment}
                            {if $best_choices['y'][$i] == $max}
                                {assign var="space" value="`$slot->day|date_format:'d-m-Y'|html`|`$moment`"}
                                <li><strong>{$slot->day|intl_date_format:$date_format.txt_full|html} - {$moment|html}</strong>
                                    <a href="{poll_url id=$poll_id action='get_ical_file' action_value=($space)}" class="btn btn-default btn-sm" title="{__('studs', 'Download as ical/ics file')}">
                                        <span class="fa fa-calendar text-muted"></span>
                                        <span class="sr-only">{__('studs', 'Download as ical/ics file')}</span>
                                    </a>
                                </li>
                            {/if}
                            {$i = $i+1}
                        {/foreach}
                    {/foreach}
                </ul>
                <p>{__('Generic', 'with')} <b>{$max|html}</b> {if $max==1}{__('Generic', 'vote')}{else}{__('Generic', 'votes')}{/if}.</p>
            </div>
        </div>
    {/if}
{/if}
