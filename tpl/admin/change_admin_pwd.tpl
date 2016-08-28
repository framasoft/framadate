{extends 'admin/admin_page.tpl'}

{block name="header"}
    <script src="{"js/app/admin/polls.js"|resource}" type="text/javascript"></script>
{/block}

{block 'admin_main'}
	{if $msg_error != null}
	<div class="alert alert-danger">
			<p>{$msg_error|html}</p>
	</div>
	{/if}

	{if $msg_info != null}
	<div class="alert alert-info">
			<p>{$msg_info|html}</p>
	</div>
	{/if}

	<form action="" method="POST">
		<div class="row">
			<div class="col-md-6 form-group">
				<label for="old_pwd" class="control-label">{__('Password', 'OldPwd')}</label>
				<input type="text" name="old_pwd" id="old_pwd" class="form-control" value=""/>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 form-group">
				<label for="new_pwd" class="control-label">{__('Password', 'NewPwd')}</label>
				<input type="text" name="new_pwd" id="new_pwd" class="form-control" value=""/>
			</div>
			<div class="col-md-6 form-group">
				<label for="new_pwd_confirm" class="control-label">{__('Password', 'NewPwdConfirm')}</label>
				<input type="text" name="new_pwd_confirm" id="new_pwd_confirm" class="form-control" value=""/>
			</div>
		</div>
		<input type="submit" value="{__('Password', 'Change')}" class="btn btn-default"/>
	</form>
{/block}
