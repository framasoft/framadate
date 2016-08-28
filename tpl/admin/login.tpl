{extends 'admin/admin_page.tpl'}

{block 'admin_main'}
	{if $msg_error != null}
	<div class="alert alert-danger">
			<p>{$msg_error|html}</p>
	</div>
	{/if}

	<form action="" method="POST">
		<input type="password" id="admin_password" name="admin_password" value="{$password|html}" class="form-control" title="{__('Password', 'Password')}" placeholder="{__('Password', 'Password')}" />
		<button type="submit" name="submit_password" value="1" class="btn btn-link">{__('Password', 'Submit access')}</button>
	</form>
{/block}
