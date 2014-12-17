<h3>{_('Votes of the poll')}</h3>

<div id="tableContainer" class="tableContainer">
    <form action="{$poll_id|poll_url}" method="POST">
        <table class="results">
            <caption class="sr-only">{_('Votes of the poll')} {$poll->title}</caption>
            <thead>
            <tr>
                <th role="presentation"></th>
                {foreach $slots as $id=>$slot}
                    <th colspan="{$slot->moments|count}" class="bg-primary month" id="M{$id}">{$slot->day|date_format:'%B %Y'}</th>
                    {for $foo=0 to ($slot->moments|count)-1}
                        {append var='headersM' value=$id}
                    {/for}
                {/foreach}
                <th></th>
            </tr>
            <tr>
                <th role="presentation"></th>
                {foreach $slots as $id=>$slot}
                    <th colspan="{$slot->moments|count}" class="bg-primary day" id="D{$id}">{$slot->day|date_format:$date_format.txt_day}</th>
                {/foreach}
                <th></th>
            </tr>
            <tr>
                <th role="presentation"></th>
                {$headersDCount=0}
                {foreach $slots as $slot}
                    {foreach $slot->moments as $id=>$moment}
                        <th colspan="1" class="bg-info" id="H{$headersDCount}">{$moment}</th>
                        {append var='headersD' value=$headersDCount}
                        {$headersDCount = $headersDCount+1}
                    {/foreach}
                {/foreach}
                <th></th>
            </tr>
            </thead>
            <tbody>
            {foreach $votes as $vote}
                <tr>
                    {* Edited line *}

                    <th class="bg-info">{$vote->name}</th>

                    {if $editingVoteId == $vote->id}
                        {foreach $vote->choices as $k=>$choice}

                            <td class="bg-info" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}">
                                <ul class="list-unstyled choice">
                                    <li class="yes">
                                        <input type="radio" id="y-choice-{$k}" name="choices[{$k}]" value="2" {if $choice==2}checked {/if}/>
                                        <label class="btn btn-default btn-xs" for="y-choice-{$k}" title="{_('Vote yes for ')} . $radio_title[$k] . '">
                                            <span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span>
                                        </label>
                                    </li>
                                    <li class="ifneedbe">
                                        <input type="radio" id="i-choice-{$k}" name="choices[{$k}]" value="1" {if $choice==1}checked {/if}/>
                                        <label class="btn btn-default btn-xs" for="i-choice-{$k}" title="{_('Vote ifneedbe for ')} . $radio_title[$k] . '">
                                            (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span>
                                        </label>
                                    </li>
                                    <li class="no">
                                        <input type="radio" id="n-choice-{$k}" name="choices[{$k}]" value="0" {if $choice==0}checked {/if}/>
                                        <label class="btn btn-default btn-xs" for="n-choice-{$k}" title="{_('Vote no for ')} . $radio_title[$k] . '">
                                            <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">{_('No')}</span>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        {/foreach}
                        <td style="padding:5px"><button type="submit" class="btn btn-success btn-xs" name="save" value="{$vote->id}" title="{_('Save the choices')} {$vote->name}">{_('Save')}</button></td>
                    {else}

                        {* Voted line *}

                        {foreach $vote->choices as $k=>$choice}

                            {if $choice==2}
                                <td class="bg-success text-success" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span></td>
                            {elseif $choice==1}
                                <td class="bg-warning text-warning" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}">(<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span></td>
                            {else}
                                <td class="bg-danger" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}"><span class="sr-only">{_('No')}</span></td>
                            {/if}

                        {/foreach}

                        {if $active && $poll->editable}
                            <td>
                                <button type="submit" class="btn btn-link btn-sm" name="edit_vote" value="{$vote->id}" title="{_('Edit the line:')} {$vote->name}">
                                    <span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{_('Edit')}</span>
                                </button>
                            </td>
                        {else}
                            <td></td>
                        {/if}
                    {/if}
                </tr>
            {/foreach}

            {* Line to add a new vote *}

            {if $active && $editingVoteId == 0}
                <tr id="vote-form">
                    <td class="bg-info" style="padding:5px">
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                            <input type="text" id="name" name="name" class="form-control" title="{_('Your name')}" placeholder="{_('Your name')}" />
                        </div>
                    </td>
                    {$i = 0}
                    {foreach $slots as $slot}
                        {foreach $slot->moments as $moment}
                            <td class="bg-info" headers="M{$headersM[$i]} D{$headersD[$i]} H{$i}">
                                <ul class="list-unstyled choice">
                                    <li class="yes">
                                        <input type="radio" id="y-choice-{$i}" name="choices[{$i}]" value="2" />
                                        <label class="btn btn-default btn-xs" for="y-choice-{$i}" title="{_('Vote yes for')} {$slot->day|date_format:$date_format.txt_short} - {$moment}">
                                            <span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span>
                                        </label>
                                    </li>
                                    <li class="ifneedbe">
                                        <input type="radio" id="i-choice-{$i}" name="choices[{$i}]" value="1" />
                                        <label class="btn btn-default btn-xs" for="i-choice-{$i}" title="{_('Vote ifneedbe for')} {$slot->day|date_format:$date_format.txt_short} - {$moment}">
                                            (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span>
                                        </label>
                                    </li>
                                    <li class="no">
                                        <input type="radio" id="n-choice-{$i}" name="choices[{$i}]" value="0" checked/>
                                        <label class="btn btn-default btn-xs" for="n-choice-{$i}" title="{_('Vote no for')} {$slot->day|date_format:$date_format.txt_short} - {$moment}">
                                            <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">{_('No')}</span>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                            {$i = $i+1}
                        {/foreach}
                    {/foreach}
                    <td><button type="submit" class="btn btn-success btn-md" name="save" title="{_('Save the choices')}">{_('Save')}</button></td>
                </tr>
            {/if}

            {* Line displaying best moments *}
            {$count_bests = 0}
            <tr id="addition">
                <td>{_("Addition")}</td>
                {$max = max($best_moments)}
                {foreach $best_moments as $best_moment}
                    {if $max == $best_moment}
                        {$count_bests = $count_bests +1}
                        <td><span class="glyphicon glyphicon-star text-warning"></span><span>{$max}</span></td>
                    {else}
                        <td></td>
                    {/if}
                {/foreach}
            </tr>
            </tbody>
        </table>
    </form>
</div>

{* Best votes listing *}

{$max = max($best_moments)}
{if $max > 0}
    <div class="row">
    {if $count_bests == 1}
    <div class="col-sm-12"><h3>{_("Best choice")}</h3></div>
    <div class="col-sm-6 col-sm-offset-3 alert alert-success">
        <p><span class="glyphicon glyphicon-star text-warning"></span>{_("The best choice at this time is:")}</p>
        {elseif $count_bests > 1}
        <div class="col-sm-12"><h3>{_("Best choices")}</h3></div>
        <div class="col-sm-6 col-sm-offset-3 alert alert-success">
            <p><span class="glyphicon glyphicon-star text-warning"></span>{_("The bests choices at this time are:")}</p>
            {/if}


            {$i = 0}
            <ul style="list-style:none">
                {foreach $slots as $slot}
                    {foreach $slot->moments as $moment}
                        {if $best_moments[$i] == $max}
                            <li><strong>{$slot->day|date_format:$date_format.txt_full} - {$moment}</strong></li>
                        {/if}
                        {$i = $i+1}
                    {/foreach}
                {/foreach}
            </ul>
            <p>{_("with")} <b>{$max}</b> {if $max==1}{_('vote')}{else}{_('votes')}{/if}.</p>
        </div>
    </div>
{/if}