{if !is_array($best_choices) || empty($best_choices)}
    {$best_choices = [0]}
{/if}

<h3>
    {__('Poll results', 'Votes')} {if $hidden}<i>({__('PollInfo', 'Results are hidden')})</i>{/if}
</h3>

<div id="t-wrap" class="t-sticky">
    <form action="{if $admin}{poll_url id=$admin_poll_id admin=true}{else}{poll_url id=$poll_id}{/if}" method="POST" id="poll_form">
        <input type="hidden" name="control" value="{$slots_hash}"/>
        <table id="t-act">
            <caption class="sr-only">{__('Poll results', 'Votes')} {$poll->title|html}</caption>
            <thead>
            {if $admin && !$expired}
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    {$headersDCount=0}
                    {foreach $slots as $slot}
                        {foreach $slot->moments as $id=>$moment}
                            <td headers="M{$slot@key} D{$headersDCount} H{$headersDCount}">
                                <a href="{poll_url id=$admin_poll_id admin=true action='delete_column' action_value=($slot->day|date_to_timestamp)|cat:'@'|cat:$moment}"
                                   data-remove-confirmation="{__('adminstuds', 'Confirm removal of the column.')}"
                                   class="btn btn-link btn-sm remove-column"
                                   title="{__('adminstuds', 'Remove column')} {$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}">
                                    <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                                    <span class="sr-only">{__('Generic', 'Remove')}</span>
                                </a>
                            {if $poll->collect_users_mail != constant("Framadate\CollectMail::NO_COLLECT")}
                                <a href="{poll_url id=$admin_poll_id admin=true action='collect_mail' action_value=($headersDCount)}"
                                   class="btn btn-link btn-sm collect-mail"
                                   title="{__('adminstuds', 'Collect the emails of the polled users for the choice')} {$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}">
                                    <i class="fa fa-envelope" aria-hidden="true"></i>
                                    <span class="sr-only">{__('Generic', 'Collect emails')}</span>
                                </a>
                            {/if}
                            </td>
                            {$headersDCount = $headersDCount+1}
                        {/foreach}
                    {/foreach}
                    <td>
                        <a href="{poll_url id=$admin_poll_id admin=true action='add_column'}"
                           class="btn btn-link btn-sm" title="{__('adminstuds', 'Add a column')}">
                            <i class="fa fa-plus text-success" aria-hidden="true"></i>
                            <span class="sr-only">{__('Poll results', 'Add a column')}</span>
                        </a>
                    </td>
                </tr>
            {/if}
            <tr>
                <th role="presentation"></th>
                {$count_same = 0}
                {$previous = 0}
                {foreach $slots as $id=>$slot}
                    {$display = $slot->day|date_format_intl:DATE_FORMAT_MONTH_YEAR|html}
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
                    <th colspan="{$slot->moments|count}" class="bg-primary day" id="D{$id}">{$slot->day|date_format_intl:DATE_FORMAT_DAY|html}</th>
                    {for $foo=0 to ($slot->moments|count)-1}
                        {append var='headersD' value=$id}
                    {/for}
                {/foreach}
                <th></th>
            </tr>
            <tr id="slots">
                <th role="presentation"></th>
                {$headersDCount=0}
                {$slots_raw = array()}
                {foreach $slots as $slot}
                    {foreach $slot->moments as $id=>$moment}
                        <th colspan="1" class="bg-info" id="H{$headersDCount}"
                            title="{$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}">
                            {$moment|html}
                        </th>
                        {append var='headersH' value=$headersDCount}
                        {$headersDCount = $headersDCount+1}
                        {$slots_raw[] = $slot->day|date_format_intl:DATE_FORMAT_FULL|cat:' - '|cat:$moment}
                    {/foreach}
                {/foreach}
                <th>
                    {include 'part/scroll_left_right.tpl'}
                    
                    {if $accessGranted}
                    <div id="hint" class="dropdown">
                        <button class="btn alert-warning dropdown-toggle"
                            type="button" id="legend" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="true">
                            <i class="fa fa-lg fa-lightbulb-o" aria-hidden="true"></i>
                            <span class="sr-only">{__('Generic', 'Information')}</span>
                        </button>
                        {include 'part/poll_hint.tpl' active=$poll->active}
                    </div>
                    {/if}

                    <button id="commentsBtn" class="btn btn-default" type="button"
                        title="{__('Comments', 'Comments')}">
                        <i class="fa fa-comment" aria-hidden="true"></i>
                        <span>{$comments|count}</span>
                        <span class="sr-only">{__('Comments', 'Comments')}</span>
                    </button>

                    {if !$hidden && ($votes|count)!=0}
                    <button id="chartBtn" class="btn btn-default" type="button"
                        title="{__('Poll results', 'Display the chart of the results')}">
                        <i class="fa fa-bar-chart" aria-hidden="true"></i>
                        <span class="sr-only">{__('Poll results', 'Display the chart of the results')}</span>
                    </button>
                    {/if}
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach $votes as $vote}

                {* Edited line *}
                {if $editingVoteId === $vote->uniqId && !$expired}
                <tr class="hidden-print">
                    <td>
                        <div class="input-group input-group-sm" id="edit">
                            <span class="input-group-addon" aria-hidden="true">
                                <i class="fa fa-user"></i>
                            </span>
                            <input type="hidden" name="edited_vote" value="{$vote->uniqId}"/>
                            <input type="text" id="name" name="name" value="{$vote->name|html}" class="form-control" title="{__('Generic', 'Your name')}" placeholder="{__('Generic', 'Your name')}" />
                            {if $poll->collect_users_mail != constant("Framadate\CollectMail::NO_COLLECT")}
                                <input type="email" {if $poll->collect_users_mail != constant("Framadate\CollectMail::COLLECT")} required {/if} id="mail" name="mail" value="{$vote->mail|html}" class="form-control" title="{__('Generic', 'Your email address')}" placeholder="{__('Generic', 'Your email address')}" />
                            {/if}
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
                                    <label for="y-choice-{$k}" title="{__('Poll results', 'Vote "yes" for')|html} {$slots_raw[$k]}">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                        <span class="sr-only">{__('Generic', 'Yes')}</span>
                                    </label>
                                </li>
                                <li class="inb">
                                    <input type="radio" id="i-choice-{$k}" name="choices[{$k}]" value="1" {if $choice=='1'}checked {/if}/>
                                    <label for="i-choice-{$k}" title="{__('Poll results', 'Votes under reserve for')|html} {$slots_raw[$k]}">
                                        <span aria-hidden="true">(<i class="fa fa-check"></i>)</span>
                                        <span class="sr-only">{__('Generic', 'Under reserve')}</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-{$k}" name="choices[{$k}]" value="0" {if $choice=='0'}checked {/if}/>
                                    <label for="n-choice-{$k}" title="{__('Poll results', 'Vote "no" for')|html} {$slots_raw[$k]}">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                        <span class="sr-only">{__('Generic', 'No')}</span>
                                    </label>
                                </li>
                                <li class="idk">
                                    <input type="radio" id="k-choice-{$k}" name="choices[{$k}]" value=" "
                                        {if $choice!='2' && $choice!='1' && $choice!='0'}checked {/if}
                                    />
                                    <label for="k-choice-{$k}" title="{__('Poll results', 'Do not participate in the vote for')|html} {$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}">
                                        <i class="fa fa-question" aria-hidden="true"></i>
                                        <span class="sr-only">{__('Generic', 'I don’t know')}</span>
                                    </label>
                                </li>
                            </ul>
                        </td>

                        {$k=$k + 1}
                      {/foreach}
                    {/foreach}

                    <td><button type="submit" class="btn btn-success btn-xs" name="save" value="{$vote->id|html}" title="{__('Poll results', 'Save choices')} {$vote->name|html}">{__('Generic', 'Save')}</button></td>
                </tr>
                {/if}
            {/foreach}

            {* Line to add a new vote *}
            {if $active && $editingVoteId === 0 && !$expired && $accessGranted}
                <tr id="vote-form" class="hidden-print mouseable">
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon" aria-hidden="true">
                                <i class="fa fa-user"></i>
                            </span>
                            <input type="text" id="name" name="name" class="form-control" title="{__('Generic', 'Your name')}" placeholder="{__('Generic', 'Your name')}" />
                            {if $poll->collect_users_mail != constant("Framadate\CollectMail::NO_COLLECT")}
                                <input type="email" {if $poll->collect_users_mail != constant("Framadate\CollectMail::COLLECT")} required {/if} id="mail" name="mail" class="form-control" title="{__('Generic', 'Your email address')}" placeholder="{__('Generic', 'Your email address')}" />
                            {/if}
                        </div>
                        {if $poll->collect_users_mail != constant("Framadate\CollectMail::NO_COLLECT") && $poll->editable == constant('Framadate\Editable::EDITABLE_BY_ALL')}
                            <div class="bg-danger">
                                <i class="fa fa-warning" aria-hidden="true"></i>
                                <label>{__('Poll results', 'Anyone will be able to see your email address after you voted')}</label>
                            </div>
                        {/if}
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
                                        <label for="y-choice-{$i}" title="{__('Poll results', 'Vote "yes" for')|html} {$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                            <span class="sr-only">{__('Generic', 'Yes')}</span>
                                        </label>
                                    </li>
                                    <li class="inb">
                                        <input type="radio" id="i-choice-{$i}" name="choices[{$i}]" value="1"
                                            {(!isset($selectedNewVotes[$i]) || ("1" !== $selectedNewVotes[$i])) ? "" : " checked"}
                                        />
                                        <label for="i-choice-{$i}" title="{__('Poll results', 'Votes under reserve for')|html} {$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}">
                                            <span aria-hidden="true">(<i class="fa fa-check"></i>)</span>
                                            <span class="sr-only">{__('Generic', 'Under reserve')}</span>
                                        </label>
                                    </li>
                                    {/if}
                                    <li class="no">
                                        <input type="radio" id="n-choice-{$i}" name="choices[{$i}]" value="0"
                                            {(!isset($selectedNewVotes[$i]) || ("0" !== $selectedNewVotes[$i])) ? "" : " checked"}
                                        />
                                        <label for="n-choice-{$i}" title="{__('Poll results', 'Vote "no" for')|html} {$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                            <span class="sr-only">{__('Generic', 'No')}</span>
                                        </label>
                                    </li>
                                    <li class="idk">
                                        <input type="radio" id="k-choice-{$i}" name="choices[{$i}]" value=" "
                                            {(isset($selectedNewVotes[$i]) && ("" !== $selectedNewVotes[$i])) ? "" : " checked"}
                                        />
                                        <label for="k-choice-{$i}" title="{__('Poll results', 'Do not participate in the vote for')|html} {$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}">
                                            <i class="fa fa-question" aria-hidden="true"></i>
                                            <span class="sr-only">{__('Generic', 'I don’t know')}</span>
                                        </label>
                                    </li>
                                </ul>
                            </td>

                            {$i = $i+1}
                        {/foreach}
                    {/foreach}
                    <td><button type="submit" class="btn btn-success btn-md" name="save" title="{__('Poll results', 'Save choices')}">{__('Generic', 'Save')}</button></td>
                </tr>
            {/if}
            </tbody>
        </table>
        
        <table id="t-res">
            {if !$hidden}
            <caption class="sr-only">{__('Poll results', 'Votes')} {$poll->title|html}</caption>
            <thead class="sr-only">
                <tr>
                    <th role="presentation"></th>
                    {foreach $slots as $id=>$slot}
                        <th class="bg-info" id="S{$id}">
                            {$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}
                        </th>
                    {/foreach}
                    <th></th>
                </tr>
            </thead>
            {/if}
            <tbody>
            {* Ugly content needed to scroll *}
                <tr class="needed-to-scroll" aria-hidden="true">
                    <th></th>
                    {foreach $slots as $id=>$slot}
                        <td></td>
                    {/foreach}
                    <td></td>
                </tr>
            {if !$hidden}
            {* Results *}
            {foreach $votes as $vote}
                <tr title="{$vote->name|html}">
                    {* Voted line *}
                    <th class="bg-info" {if $accessGranted && $admin}title="{$vote->mail|html}"{/if}>
                        {if $active && !$expired && $accessGranted &&
                            (
                            $poll->editable == constant('Framadate\Editable::EDITABLE_BY_ALL')
                            or $admin
                            or ($poll->editable == constant('Framadate\Editable::EDITABLE_BY_OWN') && $editedVoteUniqueId == $vote->uniqId)
                            )
                        }
                        <span class="edit-username-left" aria-hidden="true">{* duplicate -> aria-hidden *}
                            <a href="{if $admin}{poll_url id=$poll->admin_id vote_id=$vote->uniqId admin=true}{else}{poll_url id=$poll->id vote_id=$vote->uniqId}{/if}"
                               class="btn btn-default btn-sm"
                               title="{__f('Poll results', 'Edit line: %s', $vote->name)|html}">
                                <i class="fa fa-pencil"></i>
                                <span class="sr-only">{__('Generic', 'Edit')}</span>
                            </a>
                        </span>
                        {/if}
                        {$vote->name|html}
                    </th>

                    {$k=0}
                    {foreach $slots as $slot}
                      {foreach $slot->moments as $moment}
                        {$choice=$vote->choices[$k]}

                        {if $choice=='2'}
                            <td class="bg-success text-success" headers="S{$k}">
                                <i class="fa fa-check" aria-hidden="true"></i>
                                <span class="sr-only">{__('Generic', 'Yes')}</span>
                            </td>
                        {elseif $choice=='1'}
                            <td class="bg-warning text-warning" headers="S{$k}">
                                <span aria-hidden="true">(<i class="fa fa-check"></i>)</span>
                                <span class="sr-only">{__('Generic', 'Under reserve')}</span>
                            </td>
                        {elseif $choice=='0'}
                            <td class="bg-danger text-danger" headers="S{$k}">
                                <i class="fa fa-times" aria-hidden="true"></i>
                                <span class="sr-only">{__('Generic', 'No')}</span>
                            </td>
                        {else}
                            <td class="bg-info" headers="S{$k}">
                                <i class="fa fa-question" aria-hidden="true"></i>
                                <span class="sr-only">{__('Generic', 'I don’t known')}</span>
                            </td>
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
                            <a href="{if $admin}{poll_url id=$poll->admin_id vote_id=$vote->uniqId admin=true}{else}{poll_url id=$poll->id vote_id=$vote->uniqId}{/if}" class="btn btn-default btn-sm" title="{__f('Poll results', 'Edit line: %s', $vote->name)|html}">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                <span class="sr-only">{__('Generic', 'Edit')}</span>
                            </a>
                            {if $admin}
                                <a href="{poll_url id=$poll->id vote_id=$vote->uniqId}" 
                                    class="btn btn-default btn-sm clipboard-url" 
                                    data-toggle="popover" data-trigger="manual" 
                                    data-placement="left"
                                    title="{__('Poll results', 'Link to edit this particular line')}" 
                                    data-content="{__('Poll results', 'The link to edit this particular line has been copied to the clipboard!')}"
                                >
                                    <i class="fa fa-link" aria-hidden="true"></i>
                                    <span class="sr-only">{__('Generic', 'Link')}</span>
                                </a>
                                <a href="{poll_url id=$admin_poll_id admin=true action='delete_vote' action_value=$vote->id}"
                                   class="btn btn-default btn-sm"
                                   title="{__('Poll results', 'Remove line:')} {$vote->name|html}">
                                    <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                                    <span class="sr-only">{__('Generic', 'Remove')}</span>
                                </a>

                            {/if}
                        </td>
                    {else}
                        <td></td>
                    {/if}
                </tr>
            {/foreach}
            {/if}
            </tbody>
            {if !$hidden}
            <tfoot>
            {* Line displaying best moments *}
            {$count_bests = 0}
            {$max = max($best_choices['y'])}
            {if $max > 0}
                <tr id="addition">
                    <td>
                        {__('Poll results', 'Total')}
                        <br>
                        {$votes|count}
                        {if ($votes|count)==1}
                            {__('Poll results', 'polled user')}
                        {else}
                            {__('Poll results', 'polled users')}
                        {/if}
                    </td>
                    {foreach $best_choices['y'] as $i=>$best_moment}
                        <td>
                        {if $max == $best_moment}
                            {$count_bests = $count_bests +1}
                            <i class="fa fa-star text-info" aria-hidden="true"></i>
                        {/if}
                        {if $best_moment > 0}
                            <span class="yes-count">{$best_moment|html}</span>
                        {/if}
                        <br>
                        {if $best_choices['inb'][$i]>0}
                            <span class="small text-muted">
                                (+<span class="inb-count">{$best_choices['inb'][$i]|html}</span>)
                            </span>
                        {/if}
                        <br>
                        {if $best_choices['n'][$i]>0}
                            <span class="small text-muted">
                                (−<span class="no-count">{$best_choices['n'][$i]|html}</span>)
                            </span>
                        {/if}
                        </td>
                    {/foreach}
                    <td aria-hidden="true"></td>
                </tr>
            {/if}
            </tfoot>
            {/if}
        </table>
    </form>
</div>

{if !$hidden && $max > 0}
    <div id="chart-wrap" style="display: none;">
        <h3>{__('Poll results', 'Chart')}</h3>
        <canvas id="Chart"></canvas>
        {* Labels sent to chart.js config  *}
        <span class="sr-only" id="chart-label-yes">{__('Generic', 'Yes')}</span>
        <span class="sr-only" id="chart-label-inb">{__('Generic', 'Under reserve')}</span>
        <span class="sr-only" id="chart-label-no">{__('Generic', 'No')}</span>
    </div>
{/if}

{if !$hidden}
    {* Best votes listing *}
    {$max = max($best_choices['y'])}
    {if $max > 0}
        <div class="row">
        {if $count_bests == 1}
        <div class="col-sm-12"><h3>{__('Poll results', 'Best choice')}</h3></div>
        <div class="col-sm-6 col-sm-offset-3 alert alert-info">
            <p>
                <i class="fa fa-star text-info" aria-hidden="true"></i>
                {__('Poll results', 'The current best choice is:')}
            </p>
            {elseif $count_bests > 1}
            <div class="col-sm-12"><h3>{__('Poll results', 'Best choices')}</h3></div>
            <div class="col-sm-6 col-sm-offset-3 alert alert-info">
                <p>
                    <i class="fa fa-star text-info" aria-hidden="true"></i>
                    {__('Poll results', 'The current best choices are:')}
                </p>
                {/if}


                {$i = 0}
                <ul class="list-unstyled">
                    {foreach $slots as $slot}
                        {foreach $slot->moments as $moment}
                            {if $best_choices['y'][$i] == $max}
                                <li><strong>{$slot->day|date_format_intl:DATE_FORMAT_SHORT|html} - {$moment|html}</strong></li>
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
