{* Password *}

<div class="form-group">
    <label for="poll_id" class="col-sm-4 control-label">
        {__('Step 1', 'Password')}
    </label>

    <div class="col-sm-8">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="use_password" {if $poll_use_password}checked{/if}
                       id="use_password">
                {__('Step 1', "Use a password to restrict access")}
            </label>
        </div>
    </div>

    <div id="password_options"{if !$poll_use_password} class="hidden"{/if}>
        <div class="col-sm-offset-4 col-sm-8">
            <div class="input-group">
                <input id="poll_password" type="password" name="password"
                       class="form-control" {$errors['password']['aria']}/>
                <label for="poll_password"
                       class="input-group-addon">{__('Step 1', 'Choice')}</label>
            </div>
        </div>
        {if !empty($errors['password']['msg'])}
            <div class="alert alert-danger">
                <p id="poll_password_error">
                    {$errors['password']['msg']}
                </p>
            </div>
        {/if}
        <div class="col-sm-offset-4 col-sm-8">
            <div class="input-group">
                <input id="poll_password_repeat" type="password" name="password_repeat"
                       class="form-control" {$errors['password_repeat']['aria']}/>
                <label for="poll_password_repeat"
                       class="input-group-addon">{__('Step 1', 'Confirmation')}</label>
            </div>
        </div>
        {if !empty($errors['password_repeat']['msg'])}
            <div class="alert alert-danger">
                <p id="poll_password_repeat_error">
                    {$errors['password_repeat']['msg']}
                </p>
            </div>
        {/if}
        <div class="col-sm-offset-4 col-sm-8">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="results_publicly_visible"
                           {if $poll_results_publicly_visible}checked{/if}
                           id="results_publicly_visible"/>
                    {__('Step 1', "The results are publicly visible")}
                </label>
            </div>
        </div>
    </div>
</div>
