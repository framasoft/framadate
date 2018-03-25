{extends file='page.tpl'}

{block name=main}
    <body>
    Les personnes ayant répondu "Oui" à cette option ont laissé ces adresses mails : </br>
    {foreach $mails_yes as $mail}
       	<strong>{$mail|html}</strong> </br>
    {/foreach}
    </br>
    Les personnes ayant répondu "Si besoin" à cette option ont laissé ces adresses mails : </br>
    {foreach $mails_ifneedbe as $mail}
       	<strong>{$mail|html}</strong> </br>
    {/foreach}
    </br>
    <a href="{poll_url id=$admin_poll_id admin=true}" class="btn btn-default" name="back">{__('adminstuds', 'Back to the poll')}</a>
    </body>
{/block}
