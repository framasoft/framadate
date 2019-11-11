{* Value MAX *}
<div class="form-group {$errors['value_max']['class']}">
    <label for="use_valueMax" class="col-sm-4 control-label">
        {t('Step 1', 'Value Max')}<br/>
    </label>
    <div class="col-sm-8">
        <div class="checkbox">
            <label>
                <input id="use_value_max" name="use_value_max" type="checkbox"
                       {if $use_value_max}checked{/if}>
                {t('Step 1', "Limit the amount of voters per option")}
            </label>
        </div>
    </div>
</div>

<div class="form-group {$errors['value_max']['class']}">
    <div {if !$use_value_max}class="hidden"{/if} id="value_max_wrapper">

        <div class="col-sm-offset-4 col-sm-8">
            <label>
                <input id="value_max" type="number" min="1" name="value_max"
                       value="{$value_max|html}" {$errors['value_max']['aria']}>

                {t('Step 1', 'votes per option')}
            </label>

        </div>
    </div>
</div>

{if !empty($errors['value_max']['msg'])}
    <div class="alert alert-danger">
        <p id="poll_customized_url_error">
            {$errors['value_max']['msg']}
        </p>
    </div>
{/if}
