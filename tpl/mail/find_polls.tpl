<h1>{__('FindPolls', 'Here are your polls')}</h1>
<ul>
    {foreach $polls as $poll}
        <li>
            <a href="{poll_url id=$poll->admin_id admin=true}">{$poll->title|html}</a>
            ({__('Generic', 'Creation date:')} {$poll->creation_date|date_format:$date_format['txt_full']})
        </li>
    {/foreach}
</ul>