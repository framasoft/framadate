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

 <legend>{__('Installation', 'typedatabase')}</legend>
</br>
mysql<input type = "radio" name = "base" value = "mysql">
</br>
PostgreSQL<input type = "radio" name = "base" value = "pgsql">
         
<fieldset>
             

 <div class="form-group">


 <legend>{__('Installation', 'identifiantadmin')}</legend>



<div class="form-group">

                        <div class="input-group">
                            <label for="nameadmin" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'nameadmin')}</label>
                            <input type="text" class="form-control" id="nameadmin" name="nameadmin" value="{$fields['nameadmin']}" autofocus required>
                        </div>
            </div>

</div>

<div class="form-group">
                        <div class="input-group">
                            <label for="passadmin" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'passadmin')}</label>
                            <input type="password" class="form-control" id="passadmin" name="passadmin" value="{$fields['passadmin']}" autofocus required>
                        </div>
            </div>

<legend></legend>
</div>

 <legend>{__('Installation', 'Database')}</legend>

 <div class="form-group">
         <div class="form-group">
                        <div class="input-group">
                            <label for="appName" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'server')}</label>
                            <input type="text" class="form-control" id="server" name="server" value="{$fields['server']}" autofocus required>
                        </div>
            </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbConnectionString" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'DbConnectionString')}</label>
                        <input type="hidden" class="form-control" id="dbConnectionString" name="dbConnectionString" value="{$fields['dbConnectionString']}" required>
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
