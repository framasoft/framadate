{* Value MAX *}
<div class="form-group {$errors['ValueMax']['class']}">
    <label for="use_valueMax" class="col-sm-4 control-label">
        {__('Step 1', 'Value Max')}<br/>
    </label>
    <div class="col-sm-8">
        <div class="checkbox">
            <label>
                <input id="use_ValueMax" name="use_ValueMax" type="checkbox"
                       {if $use_ValueMax}checked{/if}>
                {__('Step 1', "Limit the amount of voters per option")}
            </label>
        </div>
    </div>
</div>

<div class="form-group {$errors['ValueMax']['class']}">
    <div {if !$use_ValueMax}class="hidden"{/if} id="valueMaxWrapper">

        <div class="col-sm-offset-4 col-sm-8">
            <label>
                <input id="ValueMax" type="number" min="1" name="ValueMax"
                       value="{$ValueMax|html}" {$errors['ValueMax']['aria']}>

                {__('Step 1', 'votes per option')}
            </label>

        </div>
    </div>
</div>

{if !empty($errors['ValueMax']['msg'])}
    <div class="alert alert-danger">
        <p id="poll_customized_url_error">
            {$errors['ValueMax']['msg']}
        </p>
    </div>
{/if}
