{include file='head.tpl'}
{include file='header.tpl'}

<form action="{$poll_id|poll_url}" method="POST">

        {* Global informations about the current poll *}

        <div class="jumbotron">
            <div class="row">
                <div class="col-md-7">
                    <h3>{$poll->title}</h3>
                </div>
                <div class="col-md-5">
                    <div class="btn-group pull-right">
                        <button onclick="javascript:print(); return false;" class="btn btn-default"><span class="glyphicon glyphicon-print"></span>{_('Print')}</button>
                        <button onclick="window.location.href='{$SERVER_URL}exportcsv.php?poll={$poll_id}';return false;" class="btn btn-default"><span class="glyphicon glyphicon-download-alt"></span>{_('Export to CSV')}</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <h4 class="control-label">{_("Initiator of the poll")}</h4>
                        <p class="form-control-static">{$poll->admin_name}</p>
                    </div>
                    <div class="form-group">
                        <label for="public-link"><a class="public-link" href="{$poll_id|poll_url}">{_("Public link of the poll")}<span class="btn-link glyphicon glyphicon-link"></span></a></label>
                        <input class="form-control" id="public-link" type="text" readonly="readonly" value="{$poll_id|poll_url}" />
                    </div>
                </div>

                {if !empty($poll->comment)}
                <div class="form-group col-md-7">
                    <h4 class="control-label">{_("Description")}</h4><br />
                    <p class="form-control-static well">{$poll->comment}</p>
                </div>
                {/if}
            </div>
        </div>

        {* Information about voting *}

        {if $poll->active}
        <div class="alert alert-info">
            <p>{_("If you want to vote in this poll, you have to give your name, choose the values that fit best for you and validate with the plus button at the end of the line.")}</p>
            <p aria-hidden="true"><b>{_('Legend:')}</b> <span class="glyphicon glyphicon-ok"></span> = {_('Yes')}, <b>(<span class="glyphicon glyphicon-ok"></span>)</b> = {_('Ifneedbe')}, <span class="glyphicon glyphicon-ban-circle"></span> = {_('No')}</p>
        </div>
        {else}
        <div class="alert alert-danger">
            <p>{_("The administrator locked this poll, votes and comments are frozen, it's not possible to participate anymore.")}</p>
            <p aria-hidden="true"><b>{_('Legend:')}</b> <span class="glyphicon glyphicon-ok"></span> = {_('Yes')}, <b>(<span class="glyphicon glyphicon-ok"></span>)</b> = {_('Ifneedbe')}, <span class="glyphicon glyphicon-ban-circle"></span> = {_('No')}</p>
        </div>
        {/if}

        {* Scroll left and right *}

        <div class="hidden row scroll-buttons" aria-hidden="true">
            <div class="btn-group pull-right">
                <button class="btn btn-sm btn-link scroll-left" title="{_('Scroll to the left')}">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="btn  btn-sm btn-link scroll-right" title="{_('Scroll to the right')}">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </button>
            </div>
        </div>

        {* Vote table *}

        <h3>{_('Votes of the poll')}</h3>
        <div id="tableContainer" class="tableContainer">
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

                                <td class="bg-info" headers="'.$td_headers[$k].'">
                                    <ul class="list-unstyled choice">
                                        <li class="yes">
                                            <input type="radio" id="y-choice-{$k}" name="choice{$k}" value="2" {if $choice==2}checked {/if}/>
                                            <label class="btn btn-default btn-xs" for="y-choice-{$k}" title="{_('Vote yes for ')} . $radio_title[$k] . '">
                                                <span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span>
                                            </label>
                                        </li>
                                        <li class="ifneedbe">
                                            <input type="radio" id="i-choice-{$k}" name="choice{$k}" value="1" {if $choice==1}checked {/if}/>
                                            <label class="btn btn-default btn-xs" for="i-choice-{$k}" title="{_('Vote ifneedbe for ')} . $radio_title[$k] . '">
                                                (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span>
                                            </label>
                                        </li>
                                        <li class="no">
                                            <input type="radio" id="n-choice-{$k}" name="choice{$k}" value="0" {if $choice==0}checked {/if}/>
                                            <label class="btn btn-default btn-xs" for="n-choice-{$k}" title="{_('Vote no for ')} . $radio_title[$k] . '">
                                                <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">{_('No')}</span>
                                            </label>
                                        </li>
                                    </ul>
                                </td>
                                <td></td>
                            {/foreach}
                        {else}

                            {* Voted line *}

                            {foreach $vote->choices as $k=>$choice}

                                {if $choice==2}
                                <td class="bg-success text-success" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span></td>
                                {else if $choice==1}
                                <td class="bg-warning text-warning" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}">(<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span></td>
                                {else}
                                <td class="bg-danger" headers="M{$headersM[$k]} D{$headersD[$k]} H{$k}"><span class="sr-only">{_('No')}</span></td>
                                {/if}

                            {/foreach}

                            {if $poll->active && $poll->editable}
                                <td>
                                    <input type="hidden" name="edit_vote" value="{$vote->id}"/>
                                    <button type="submit" class="btn btn-link btn-sm" name="edit_vote" title="{_('Edit the line:')} {$vote->name}">
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

                {if $poll->active && $editingVoteId == 0}
                    <tr id="vote-form">
                        <td class="bg-info" style="padding:5px">
                            <div class="input-group input-group-sm">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                                <input type="text" id="nom" name="nom" class="form-control" title="{_('Your name')}" placeholder="{_('Your name')}" />
                            </div>
                        </td>
                        {$i = 0}
                        {foreach $slots as $slot}
                            {foreach $slot->moments as $moment}
                        <td class="bg-info" headers="M{$headersM[$i]} D{$headersD[$i]} H{$i}">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-{$i}" name="choice{$i}" value="2" />
                                    <label class="btn btn-default btn-xs" for="y-choice-{$i}" title="{_('Vote yes for')} {$slot->day|date_format:$date_format.txt_short} - {$moment}">
                                        <span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-{$i}" name="choice{$i}" value="1" />
                                    <label class="btn btn-default btn-xs" for="i-choice-{$i}" title="{_('Vote ifneedbe for')} {$slot->day|date_format:$date_format.txt_short} - {$moment}">
                                        (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice{$i}" name="choice{$i}" value="0" checked/>
                                    <label class="btn btn-default btn-xs" for="n-choice-{$i}" title="{_('Vote no for')} {$slot->day|date_format:$date_format.txt_short} - {$moment}">
                                        <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">{_('No')}</span>
                                    </label>
                                </li>
                            </ul>
                        </td>
                            {$i = $i+1}
                            {/foreach}
                        {/foreach}
                        <td><button type="submit" class="btn btn-success btn-sm" name="add_vote" title="{_('Save the choices')}">{_('Save')}</button></td>
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
</form>
{include file='footer.tpl'}