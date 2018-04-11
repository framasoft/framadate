<h1>{$poll->title|html|string_format:__('EditLink', 'Edit link for poll "%s"')}</h1>
<p>
    {__('EditLink', 'Here is the link for editing your vote:')}
    <a href="{poll_url id=$poll_id vote_id=$editedVoteUniqueId}">{$poll->title|html}</a>
</p>
