{* Poll identifier *}
<div class="form-group {$errors['customized_url']['class']}">
    <label for="customized_url_options" class="col-sm-4 control-label">
        {__('Step 1', 'Poll link')}<br/>
    </label>

    <div class="col-sm-8">
        <div class="checkbox">
            <label>
                <input id="use_customized_url" name="use_customized_url" type="checkbox"
                       {if $use_customized_url}checked{/if}/>
                {__('Step 1', 'Customize the URL')}
            </label>
        </div>
    </div>
</div>
<div id="customized_url_options" {if !$use_customized_url}class="hidden"{/if}>
    <div class="form-group {$errors['customized_url']['class']}">
        <label for="customized_url" class="col-sm-4 control-label">
            <span id="pollUrlDesc" class="small">{__('Step 1', 'The identifier can contain letters, numbers and dashes "-".')}</span>
        </label>
        <div class="col-sm-8">
            <div class="input-group">
                                    <span class="input-group-addon">
                                        {$SERVER_URL}
                                    </span>
                <input id="customized_url" type="text" name="customized_url"
                       class="form-control" {$errors['customized_url']['aria']}
                       value="{$customized_url|html}" aria-describedBy="pollUrlDesc"
                       maxlength="64"
                       pattern="[A-Za-z0-9-]+"/>
            </div>
            <span class="help-block text-warning">{__('Step 1', 'By defining an identifier that can facilitate access to the poll for unwanted people. It is recommended to protect it with a password.')}</span>
        </div>
    </div>
    {if !empty($errors['customized_url']['msg'])}
        <div class="alert alert-danger">
            <p id="poll_customized_url_error">
                {$errors['customized_url']['msg']}
            </p>
        </div>
    {/if}
</div>
