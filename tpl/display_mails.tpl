{extends file='page.tpl'}

{block name=main}
    <body>
    {if ($mails_yes|count) === 0}
        {__('display_mails', "People who have answered 'Yes' to this option have not left any email addresses.")}</br>
    {else}
	{__('display_mails', "People who have answered 'Yes' to this option have left those email addresses :")}</br>
	{foreach $mails_yes as $mail}
		<strong>{$mail|html}</strong> </br>
	{/foreach}
    {/if}
    </br>
    {if ($mails_ifneedbe|count) === 0}
    	{__('display_mails', "People who have answered 'If need be' to this option have not left any email addresses.")}</br>
    {else}
	{__('display_mails', "People who have answered 'If need be' to this option have left those email addresses :")}</br>
	{foreach $mails_ifneedbe as $mail}
	       	<strong>{$mail|html}</strong> </br>
	{/foreach}
    {/if}
    </br>
    {if ($mails_no|count) === 0}
    	{__('display_mails', "People who have answered 'No' to this option have not left any email addresses.")}</br>
    {else}
    	{__('display_mails', "People who have answered 'No' to this option have left those email addresses :")}</br>
    	{foreach $mails_no as $mail}
       		<strong>{$mail|html}</strong> </br>
    	{/foreach}
    {/if}
    </br>
    <a href="{poll_url id=$admin_poll_id admin=true}" class="btn btn-default" name="back">{__('adminstuds', 'Back to the poll')}</a>
    </body>
{/block}
