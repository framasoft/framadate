{extends file='page.tpl'}

{block name=main}
    <body>
    {if sizeof($mails_yes)==0}
        {__('display_mail', 'People who have answered "Yes" to this option have not left any email adress.')}</br>
    {else}
	{__('display_mail', 'People who have answered "Yes" to this option have left those email adresses :')}</br>
	{foreach $mails_yes as $mail}
		<strong>{$mail|html}</strong> </br>
	{/foreach}
    {/if}
    </br>
    {if sizeof($mails_ifneedbe)==0}
    	{__('display_mail', 'People who have answered "If need be" to this option have not left any email adress.')}</br>
    {else}
	{__('display_mail', 'People who have answered "If need be" to this option have left those email adresses :')}</br>
	{foreach $mails_ifneedbe as $mail}
	       	<strong>{$mail|html}</strong> </br>
	{/foreach}
    {/if}
    </br>
    {if sizeof($mails_no)==0}
    	{__('display_mail', 'People who have answered "No" to this option have not left any email adress.')}</br>
    {else}
    	{__('display_mail', 'People who have answered "No" to this option have left those email adresses :')}</br>
    	{foreach $mails_no as $mail}
       		<strong>{$mail|html}</strong> </br>
    	{/foreach}
    {/if}
    </br>
    <a href="{poll_url id=$admin_poll_id admin=true}" class="btn btn-default" name="back">{__('adminstuds', 'Back to the poll')}</a>
    </body>
{/block}
