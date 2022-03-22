<p>{__f('FindPolls', 'Here is the list of the polls that you manage on %s:', $smarty.const.NOMAPPLICATION)}</p>
<ul>
    {foreach $polls as $poll}
        <li>
            <a href="{poll_url id=$poll->admin_id admin=true}">{$poll->title|html}</a>
            ({__('Generic', 'Creation date:')} {$poll->creation_date|intl_date_format:$date_format['txt_full']})
        </li>
    {/foreach}
</ul>
<p>{__('FindPolls','Have a good day!')}</p>
<p>
    <i>
        {__('FindPolls','PS: this email has been sent because you – or someone else – asked to get back the polls created with your email address.')}
        {capture name="email_url"}<a href="mailto:{$smarty.const.ADRESSEMAILADMIN}">{$smarty.const.ADRESSEMAILADMIN}</a>{/capture}
        {__f('FindPolls',"If you weren't the source of this action and if you think this is an abuse of the service, please notify the administrator on %s.", $smarty.capture.email_url)}
    </i>
</p>
