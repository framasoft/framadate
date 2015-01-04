{if !is_array($best_choices) || empty($best_choices)}
    {$best_choices = [0]}
{/if}

<h3>{_('Votes of the poll')}</h3>

<div id="tableContainer" class="tableContainer">
    <form action="" method="POST">
        <table class="results">
            <caption class="sr-only">{_('Votes of the poll')} {$poll->title}</caption>
            <thead>
            {if $admin}
                <tr class="hidden-print">
                    <th role="presentation"></th>
                    {foreach $slots as $id=>$slot}
                        <td headers="C{$id}">
                            <button type="submit" name="delete_column" value="{$slot->title}" class="btn btn-link btn-sm" title="{_('Remove the column')} {$slot->title}"><span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">{_('Remove')}</span></button>
                        </td>
                    {/foreach}
                    <td>
                        <button type="submit" name="add_slot" class="btn btn-link btn-sm" title="{_('Add a column')}"><span class="glyphicon glyphicon-plus text-success"></span><span class="sr-only">{_('Add a column')}</span></button>
                    </td>
                </tr>
            {/if}
            <tr>
                <th role="presentation"></th>
                {foreach $slots as $id=>$slot}
                    <th class="bg-info" id="H{$id}">{$slot->title|markdown}</th>
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
                        {foreach $vote->choices as $id=>$choice}

                            <td class="bg-info" headers="C{$id}">
                                <ul class="list-unstyled choice">
                                    <li class="yes">
                                        <input type="radio" id="y-choice-{$id}" name="choices[{$id}]" value="2" {if $choice==2}checked {/if}/>
                                        <label class="btn btn-default btn-xs" for="y-choice-{$id}" title="{_('Vote yes for ')} . $radio_title[$id] . '">
                                            <span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span>
                                        </label>
                                    </li>
                                    <li class="ifneedbe">
                                        <input type="radio" id="i-choice-{$id}" name="choices[{$id}]" value="1" {if $choice==1}checked {/if}/>
                                        <label class="btn btn-default btn-xs" for="i-choice-{$id}" title="{_('Vote ifneedbe for ')} . $radio_title[$id] . '">
                                            (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span>
                                        </label>
                                    </li>
                                    <li class="no">
                                        <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value="0" {if $choice==0}checked {/if}/>
                                        <label class="btn btn-default btn-xs" for="n-choice-{$id}" title="{_('Vote no for ')} . $radio_title[$id] . '">
                                            <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">{_('No')}</span>
                                        </label>
                                    </li>
                                </ul>
                            </td>
                        {/foreach}
                        <td style="padding:5px"><button type="submit" class="btn btn-success btn-xs" name="save" value="{$vote->id}" title="{_('Save the choices')} {$vote->name}">{_('Save')}</button></td>
                    {else}

                        {* Voted line *}

                        {foreach $vote->choices as $choice}

                            {if $choice==2}
                                <td class="bg-success text-success" headers="C{$id}"><span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span></td>
                            {elseif $choice==1}
                                <td class="bg-warning text-warning" headers="C{$id}">(<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span></td>
                            {else}
                                <td class="bg-danger" headers="C{$id}"><span class="sr-only">{_('No')}</span></td>
                            {/if}

                        {/foreach}

                        {if $active && $poll->editable}
                            <td>
                                <button type="submit" class="btn btn-link btn-sm" name="edit_vote" value="{$vote->id}" title="{_('Edit the line:')} {$vote->name}">
                                    <span class="glyphicon glyphicon-pencil"></span><span class="sr-only">{_('Edit')}</span>
                                </button>
                                {if $admin}
                                    <button type="submit" class="btn btn-link btn-sm" name="delete_vote" value="{$vote->id}" title="{_('Remove the line:')} {$vote->name}">
                                        <span class="glyphicon glyphicon-remove text-danger"></span><span class="sr-only">{_('Remove')}</span>
                                    </button>
                                {/if}
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
                    {foreach $slots as $id=>$slot}
                        <td class="bg-info" headers="C{$id}">
                            <ul class="list-unstyled choice">
                                <li class="yes">
                                    <input type="radio" id="y-choice-{$id}" name="choices[{$id}]" value="2" />
                                    <label class="btn btn-default btn-xs" for="y-choice-{$id}" title="{_('Vote yes for')} {$slot->title}">
                                        <span class="glyphicon glyphicon-ok"></span><span class="sr-only">{_('Yes')}</span>
                                    </label>
                                </li>
                                <li class="ifneedbe">
                                    <input type="radio" id="i-choice-{$id}" name="choices[{$id}]" value="1" />
                                    <label class="btn btn-default btn-xs" for="i-choice-{$id}" title="{_('Vote ifneedbe for')} {$slot->title}">
                                        (<span class="glyphicon glyphicon-ok"></span>)<span class="sr-only">{_('Ifneedbe')}</span>
                                    </label>
                                </li>
                                <li class="no">
                                    <input type="radio" id="n-choice-{$id}" name="choices[{$id}]" value="0" checked/>
                                    <label class="btn btn-default btn-xs" for="n-choice-{$id}" title="{_('Vote no for')} {$slot->title}">
                                        <span class="glyphicon glyphicon-ban-circle"></span><span class="sr-only">{_('No')}</span>
                                    </label>
                                </li>
                            </ul>
                        </td>
                    {/foreach}
                    <td><button type="submit" class="btn btn-success btn-md" name="save" title="{_('Save the choices')}">{_('Save')}</button></td>
                </tr>
            {/if}

            {* Line displaying best moments *}
            {$count_bests = 0}
            {$max = max($best_choices)}
            {if $max > 0}
                <tr id="addition">
                    <td>{_("Addition")}</td>
                    {foreach $best_choices as $best_choice}
                        {if $max == $best_choice}
                            {$count_bests = $count_bests +1}
                            <td><span class="glyphicon glyphicon-star text-warning"></span>{$best_choice}</td>
                        {else}
                            <td>{$best_choice}</td>
                        {/if}
                    {/foreach}
                </tr>
            {/if}
            </tbody>
        </table>
    </form>
</div>

{* Best votes listing *}

{$max = max($best_choices)}
{if $max > 0}
    <div class="row">
    {if $count_bests == 1}
    <div class="col-sm-12"><h3>{_("Best choice")}</h3></div>
    <div class="col-sm-6 col-sm-offset-3 alert alert-success">
        <p><span class="glyphicon glyphicon-star text-warning"></span>{_('The best choice at this time is:')}</p>
        {elseif $count_bests > 1}
        <div class="col-sm-12"><h3>{_("Best choices")}</h3></div>
        <div class="col-sm-6 col-sm-offset-3 alert alert-success">
            <p><span class="glyphicon glyphicon-star text-warning"></span>{_('The bests choices at this time are:')}</p>
            {/if}


            {$i = 0}
            <ul style="list-style:none">
                {foreach $slots as $slot}
                    {if $best_choices[$i] == $max}
                        <li><strong>{$slot->title|markdown:true}</strong></li>
                    {/if}
                    {$i = $i+1}
                {/foreach}
            </ul>
            <p>{_('with')} <b>{$max}</b> {if $max==1}{_('vote')}{else}{_('votes')}{/if}.</p>
        </div>
    </div>
{/if}