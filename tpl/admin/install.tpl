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
                        <p class="help-block">Le nom de l'application qui sera notamment utilisé dans les emails.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="appMail" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'AppMail')}</label>
                                    <input type="email" class="form-control" id="appMail" name="appMail" value="{$fields['appMail']}" required>
                                </div>
                                <p class="help-block">L'adresse email de l'administrateur qui sera fournie en cas de souci.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <label for="responseMail" class="input-group-addon">{__('Installation', 'ResponseMail')}</label>
                                    <input type="email" class="form-control" id="responseMail" name="responseMail" value="{$fields['responseMail']}">
                                </div>
                                <p class="help-block">L'adresse de réponse des couriels envoyés par l'application.</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
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
                        </div>
                        <div class="col-md-6">

                            <div class="input-group">
                                <label for="cleanUrl" class="input-group-addon">{__('Installation', 'CleanUrl')}</label>

                                <div class="form-control">
                                    <input type="checkbox" id="cleanUrl" name="cleanUrl" {($fields['cleanUrl']) ? 'checked' : ''}>
                                    <p class="help-block">Utiliser la réécriture d'URL pour obtenir de belles URLs.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>{__('Installation', 'Database')}</legend>

                <div class="form-group">
                    {__('Installation', 'DbDriver')}
                    <div class="radio">
                        <label>
                            <input type="radio" name="dbDriver" id="dbDriver_mysql" value="pdo_mysql" checked>
                            MySQL
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="dbDriver" id="dbDriver_pgsql" value="pdo_pgsql">
                            PostgreSQL
                        </label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="dbHost" class="input-group-addon">{__('Installation', 'DbHost')}</label>
                                <input type="text" class="form-control" id="dbHost" name="dbHost" value="{$fields['dbHost']}" required>
                            </div>
                            <p class="help-block">Le nom d'hôte du serveur de base de données, <code>localhost</code> si le serveur est le même.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="dbPort" class="input-group-addon">{__('Installation', 'DbPort')}</label>
                                <input type="text" class="form-control" id="dbPort" name="dbPort" value="{$fields['dbPort']}">
                            </div>
                            <p class="help-block">Port 3306 par défaut pour MySQL, 5432 pour PostgreSQL</p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <label for="dbName" class="input-group-addon">{__('Installation', 'DbName')}</label>
                        <input type="text" class="form-control" id="dbName" name="dbName" value="{$fields['dbName']}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="dbUser" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'DbUser')}</label>
                                <input type="text" class="form-control" id="dbUser" name="dbUser" value="{$fields['dbUser']}" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="dbPassword" class="input-group-addon">{__('Installation', 'DbPassword')}</label>
                                <input type="password" class="form-control" id="dbPassword" name="dbPassword" value="{$fields['dbPassword']}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="dbPrefix" class="input-group-addon">{__('Installation', 'DbPrefix')}</label>
                                <input type="text" class="form-control" id="dbPrefix" name="dbPrefix" value="{$fields['dbPrefix']}">
                            </div>
                            <p class="help-block">Le préfixe à appliquer devant les tables</p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <label for="migrationTable" class="input-group-addon">{__('Generic', 'ASTERISK')} {__('Installation', 'MigrationTable')}</label>
                                <input type="text" class="form-control" id="migrationTable" name="migrationTable" value="{$fields['migrationTable']}" required>
                            </div>
                            <p class="help-block">La table utilisée pour stocker les migrations</p>
                        </div>
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
