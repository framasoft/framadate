{extends 'admin/admin_page.tpl'}

{block 'main'}
<div class="row">
    <div class="col-md-12">
        <form method="POST">

            {if $error}
                <div id="result" class="alert alert-danger">
                    <h4>{$error}</h4>
                    <small>{$error_details}</small>
                </div>
            {/if}

            <fieldset>
                <legend>{__('Installation', 'General')}</legend>
                <div class="form-group">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="appName" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'AppName')}</label>
                            <input type="text" class="form-control" id="appName" name="appName" value="{$fields['appName']}" autofocus required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="appMail" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'AppMail')}</label>
                            <input type="email" class="form-control" id="appMail" name="appMail" value="{$fields['appMail']}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="responseMail" class="input-group-addon">{__('Installation', 'ResponseMail')}</label>
                            <input type="email" class="form-control" id="responseMail" name="responseMail" value="{$fields['responseMail']}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="defaultLanguage" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'DefaultLanguage')}</label>
                            <select type="email" class="form-control" id="defaultLanguage" name="defaultLanguage" required>
                                {foreach $langs as $lang=>$label}
                                    <option value="{$lang}" {if $lang==$fields['defaultLanguage']}selected{/if}>{$label}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="cleanUrl" class="input-group-addon">{__('Installation', 'CleanUrl')}</label>

                        <div class="form-control">
                            <input type="checkbox" id="cleanUrl" name="cleanUrl" {($fields['cleanUrl']) ? 'checked' : ''}>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>{__('Installation', 'Database')}</legend>
                <div class="form-group">
                    <div class="input-group">
                        <label for="dbConnectionString" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'DbConnectionString')}</label>
                        <input type="text" class="form-control" id="dbConnectionString" name="dbConnectionString" value="{$fields['dbConnectionString']}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbUser" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'DbUser')}</label>
                        <input type="text" class="form-control" id="dbUser" name="dbUser" value="{$fields['dbUser']}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbPassword" class="input-group-addon">{__('Installation', 'DbPassword')}</label>
                        <input type="password" class="form-control" id="dbPassword" name="dbPassword" value="{$fields['dbPassword']}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbPrefix" class="input-group-addon">{__('Installation', 'DbPrefix')}</label>
                        <input type="text" class="form-control" id="dbPrefix" name="dbPrefix" value="{$fields['dbPrefix']}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="migrationTable" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'MigrationTable')}</label>
                        <input type="text" class="form-control" id="migrationTable" name="migrationTable" value="{$fields['migrationTable']}" required>
                    </div>
                </div>
            </fieldset>

            <div class="text-center form-group">
                <button type="submit" class="btn btn-primary">{__('Installation', 'Install')}</button>
            </div>

        </form>
    </div>
</div>
{/block}
