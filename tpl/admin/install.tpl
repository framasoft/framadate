{extends 'admin/admin_page.tpl'}

{block 'main'}
<div class="row">
    <div class="col-md-12">
        <form action="" method="POST">

            {if $error}
                <div id="result" class="alert alert-danger">{$error}</div>
            {/if}

            <fieldset>
                <legend>{__('Installation', 'General')}</legend>
                <div class="form-group">
                    <div class="form-group">
                        <div class="input-group">
                            <label for="appName" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'AppName')}</label>
                            <input type="text" class="form-control" id="appName" name="appName" value="{$fields['General']['appName']}" autofocus required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="appMail" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'AppMail')}</label>
                            <input type="email" class="form-control" id="appMail" name="appMail" value="{$fields['General']['appMail']}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="responseMail" class="input-group-addon">{__('Installation', 'ResponseMail')}</label>
                            <input type="email" class="form-control" id="responseMail" name="responseMail" value="{$fields['General']['responseMail']}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <label for="defaultLanguage" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'DefaultLanguage')}</label>
                            <select type="email" class="form-control" id="defaultLanguage" name="defaultLanguage" required>
                                <option value="de">{$langs['de']}</option>
                                <option value="en">{$langs['es']}</option>
                                <option value="es">{$langs['es']}</option>
                                <option value="fr" selected>{$langs['fr']}</option>
                                <option value="it">{$langs['it']}</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="cleanUrl" class="input-group-addon">{__('Installation', 'CleanUrl')}</label>

                        <div class="form-control">
                            <input type="checkbox" id="cleanUrl" name="cleanUrl" {($fields['General']['cleanUrl']) ? 'checked' : ''}>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>{__('Installation', 'Database')}</legend>
                <div class="form-group">
                    <div class="input-group">
                        <label for="dbConnectionString" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'DbConnectionString')}</label>
                        <input type="text" class="form-control" id="dbConnectionString" name="dbConnectionString" value="{$fields['Database configuration']['dbConnectionString']}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbUser" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'DbUser')}</label>
                        <input type="text" class="form-control" id="dbUser" name="dbUser" value="{$fields['Database configuration']['dbUser']}" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbPassword" class="input-group-addon">{__('Installation', 'DbPassword')}</label>
                        <input type="password" class="form-control" id="dbPassword" name="dbPassword" value="{$fields['Database configuration']['dbPassword']}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbPrefix" class="input-group-addon">{__('Installation', 'DbPrefix')}</label>
                        <input type="text" class="form-control" id="dbPrefix" name="dbPrefix" value="{$fields['Database configuration']['dbPrefix']}">
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="migrationTable" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'MigrationTable')}</label>
                        <input type="text" class="form-control" id="migrationTable" name="migrationTable" value="{$fields['Database configuration']['migrationTable']}" required>
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
