<div class="form-group">
    <label for="poll_id" class="col-sm-4 control-label">
        {__('Step 1', 'Permissions')}
    </label>
    <div class="col-sm-8">
        <div class="radio">
            <label>
                <input type="radio" name="editable" id="editableByAll"
                       {if $poll_editable==constant("Framadate\Editable::EDITABLE_BY_ALL")}checked{/if}
                       value="{constant("Framadate\Editable::EDITABLE_BY_ALL")}">
                {__('Step 1', 'All voters can modify any vote')}
            </label>
            <label>
                <input type="radio" name="editable"
                       {if $poll_editable==constant("Framadate\Editable::EDITABLE_BY_OWN")}checked{/if}
                       value="{constant("Framadate\Editable::EDITABLE_BY_OWN")}">
                {__('Step 1', 'Voters can modify their vote themselves')}
            </label>
            <label>
                <input type="radio" name="editable"
                       {if empty($poll_editable) or $poll_editable==constant("Framadate\Editable::NOT_EDITABLE")}checked{/if}
                       value="{constant("Framadate\Editable::NOT_EDITABLE")}">
                {__('Step 1', 'Votes cannot be modified')}
            </label>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="hidden" {if $poll_hidden}checked{/if}
                       id="hidden">
                {__('Step 1', "Only the poll maker can see the poll results")}
            </label>
        </div>
        <div id="hiddenWithBadEditionModeError" class="alert alert-danger hidden">
            <p>
                {__('Error', "You can't create a poll with hidden results with the following option: ")}
                "{__('Step 1', 'All voters can modify any vote')}"
            </p>
        </div>
    </div>
</div>
