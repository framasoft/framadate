{* Collect users email *}

<div class="form-group">
    <label for="collect_voters_email" class="col-sm-4 control-label">
        {__('Step 1', "Collect voters' email addresses")}
    </label>
    <div class="col-sm-8" id="collect_voters_email">
        <div class="radio">
            <label>
                <input type="radio" name="collect_users_mail" id="no_collect"
                       {if $collect_users_mail==constant("Framadate\CollectMail::NO_COLLECT")}checked{/if}
                       value="{constant("Framadate\CollectMail::NO_COLLECT")}">
                {__('Step 1', 'Email addresses are not collected')}
            </label>
            <label>
                <input type="radio" name="collect_users_mail"
                       {if $collect_users_mail==constant("Framadate\CollectMail::COLLECT")}checked{/if}
                       value="{constant("Framadate\CollectMail::COLLECT")}">
                {__('Step 1', 'Email addresses are collected but not required')}
            </label>
            <label>
                <input type="radio" name="collect_users_mail"
                       {if $collect_users_mail==constant("Framadate\CollectMail::COLLECT_REQUIRED")}checked{/if}
                       value="{constant("Framadate\CollectMail::COLLECT_REQUIRED")}">
                {__('Step 1', 'Email addresses are required')}
            </label>
            <label>
                <input type="radio" disabled name="collect_users_mail"
                       {if $collect_users_mail==constant("Framadate\CollectMail::COLLECT_REQUIRED_VERIFIED")}checked{/if}
                       value="{constant("Framadate\CollectMail::COLLECT_REQUIRED_VERIFIED")}">
                {__('Step 1', 'Email addresses are required and verified')}
            </label>
        </div>
    </div>
</div>

<div id="collect_warning" class="hidden">
    <div class="col-sm-offset-4 col-sm-8">
        <label class="bg-danger">
            <i class="glyphicon glyphicon-alert"></i>
            {__('Step 1', "Warning: Anyone can see the polled users' email addresses since all voters can modify any vote. You should restrict permission rules.")}
        </label>
    </div>
</div> {* END div.form-group *}
