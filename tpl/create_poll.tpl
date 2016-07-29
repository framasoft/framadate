{extends file='page.tpl'}

{block name="header"}
    <script src="{"js/app/create_poll.js"|resource}" type="text/javascript"></script>
    <link rel="stylesheet" href="{"css/app/create_poll.css"|resource}">
{/block}

{block name=main}
    <div class="row" style="display:none" id="form-block">
        <div class="col-md-8 col-md-offset-2">
            <form name="formulaire" id="formulaire" action="" method="POST" class="form-horizontal" role="form">

                <div class="alert alert-info">
                    <p>
                        {__('Step 1', 'You are in the poll creation section.')}<br/>
                        {__('Step 1', 'Required fields cannot be left blank.')}
                    </p>
                </div>

                <div class="form-group {$errors['title']['class']}">
                    <label for="poll_title" class="col-sm-4 control-label">{__('Step 1', 'Poll title')} *</label>

                    <div class="col-sm-8">
                        <input id="poll_title" type="text" name="title" class="form-control" {$errors['title']['aria']}
                               value="{$poll_title|html}"/>
                    </div>
                </div>
                {if !empty($errors['title']['msg'])}
                    <div class="alert alert-danger">
                        <p id="poll_title_error">
                            {$errors['title']['msg']}
                        </p>
                    </div>
                {/if}

                <div class="form-group {$errors['id']['class']}">
                    <label for="poll_id" class="col-sm-4 control-label">
                        {__('Step 1', 'Poll id')}<br/>
                        <span id="pollIdDesc" class="small">{__('Step 1', 'Poll id rules')}</span>
                    </label>

                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <input id="customize_id" name="customize_id" type="checkbox"/>
                            </span>
                            <span class="input-group-addon">
                                {$SERVER_URL}
                            </span>
                            <input id="poll_id" type="text" name="id" class="form-control" {$errors['id']['aria']}
                                   value="{$poll_id|html}" aria-describedBy="pollIdDesc" disabled="disabled" maxlength="64"
                                   pattern="[A-Za-z0-9-]+"/>
                        </div>
                        <span class="help-block text-warning">{__('Step 1', 'Poll id warning')}</span>
                    </div>
                </div>
                {if !empty($errors['id']['msg'])}
                    <div class="alert alert-danger">
                        <p id="poll_title_error">
                            {$errors['id']['msg']}
                        </p>
                    </div>
                {/if}

                <div class="form-group {$errors['description']['class']}">
                    <label for="poll_comments" class="col-sm-4 control-label">{__('Generic', 'Description')}</label>

                    <div class="col-sm-8">
                        <textarea id="poll_comments" name="description"
                                  class="form-control" {$errors['description']['aria']}
                                  rows="5">{$poll_description|html}</textarea>
                    </div>
                </div>
                {if !empty($errors['description']['msg'])}
                    <div class="alert alert-danger">
                        <p id="poll_title_error">
                            {$errors['description']['msg']}
                        </p>
                    </div>
                {/if}

                <div class="form-group {$errors['name']['class']}">
                    <label for="yourname" class="col-sm-4 control-label">{__('Generic', 'Your name')} *</label>

                    <div class="col-sm-8">
                        {if $useRemoteUser}
                            <input type="hidden" name="name" value="{$form->admin_name}" />{$form->admin_name}
                        {else}
                            <input id="yourname" type="text" name="name" class="form-control" {$errors['name']['aria']} value="{$poll_name|html}" />
                        {/if}
                    </div>
                </div>
                {if !empty($errors['name']['msg'])}
                    <div class="alert alert-danger">
                        <p id="poll_title_error">
                            {$errors['name']['msg']}
                        </p>
                    </div>
                {/if}

                {if $use_smtp}
                    <div class="form-group {$errors['email']['class']}">
                        <label for="email" class="col-sm-4 control-label">
                            {__('Generic', 'Your email address')} *<br/>
                            <span class="small">{__('Generic', '(in the format name@mail.com)')}</span>
                        </label>

                        <div class="col-sm-8">
                            {if $useRemoteUser}
                                <input type="hidden" name="mail" value="{$form->admin_mail}">{$form->admin_mail}
                            {else}
                                <input id="email" type="text" name="mail" class="form-control" {$errors['email']['aria']} value="{$poll_mail|html}" />
                            {/if}
                        </div>
                    </div>
                    {if !empty($errors['email']['msg'])}
                        <div class="alert alert-danger">
                            <p id="poll_title_error">
                                {$errors['email']['msg']}
                            </p>
                        </div>
                    {/if}

                {/if}

                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-8">
                        <div class="radio">
                            <label>
                                <input type="radio" name="editable" id="editableByAll" {if $poll_editable==constant("Framadate\Editable::EDITABLE_BY_ALL")}checked{/if} value="{constant("Framadate\Editable::EDITABLE_BY_ALL")}">
                                {__('Step 1', 'All voters can modify any vote')}
                            </label>
                            <label>
                                <input type="radio" name="editable" {if $poll_editable==constant("Framadate\Editable::EDITABLE_BY_OWN")}checked{/if} value="{constant("Framadate\Editable::EDITABLE_BY_OWN")}">
                                {__('Step 1', 'Voters can modify their vote themselves')}
                            </label>
                            <label>
                                <input type="radio" name="editable" {if empty($poll_editable) or $poll_editable==constant("Framadate\Editable::NOT_EDITABLE")}checked{/if} value="{constant("Framadate\Editable::NOT_EDITABLE")}">
                                {__('Step 1', 'Votes cannot be modified')}
                            </label>
                        </div>
                    </div>
                </div>


                {if $use_smtp}
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="receiveNewVotes" {if $poll_receiveNewVotes}checked{/if}
                                    id="receiveNewVotes">
                                    {__('Step 1', 'To receive an email for each new vote')}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="receiveNewComments" {if $poll_receiveNewComments}checked{/if}
                                    id="receiveNewComments">
                                    {__('Step 1', 'To receive an email for each new comment')}
                                </label>
                            </div>
                        </div>
                    </div>
                {/if}

                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="hidden" {if $poll_hidden}checked{/if}
                                id="hidden">
                                {__('Step 1', "Only the poll maker can see the poll's results")}
                            </label>
                        </div>
                        <div id="hiddenWithBadEditionModeError" class="alert alert-danger hidden">
                            <p>
                                {__('Error', "You can't create a poll with hidden results with the following edition option:")}"{__('Step 1', 'All voters can modify any vote')}"
                            </p>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-offset-4 col-sm-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="use_password" {if $poll_use_password}checked{/if}
                                       id="use_password">
                                {__('Step 1', "Use a password to restrict access")}
                            </label>
                        </div>
                    </div>
                    <div id="password_options"{if !$poll_use_password} class="hidden"{/if}>
                        <label for="poll_password" class="col-sm-4 control-label">{__('Step 1', 'Poll password')}</label>
                        <div class="col-sm-8">
                            <input id="poll_password" type="password" name="password" class="form-control" {$errors['password']['aria']}/>
                        </div>
                        {if !empty($errors['password']['msg'])}
                            <div class="alert alert-danger">
                                <p id="poll_password_error">
                                    {$errors['password']['msg']}
                                </p>
                            </div>
                        {/if}
                        <label for="poll_password_repeat" class="col-sm-4 control-label">{__('Step 1', 'Confirm password')}</label>
                        <div class="col-sm-8">
                            <input id="poll_password_repeat" type="password" name="password_repeat" class="form-control" {$errors['password_repeat']['aria']}/>
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
                                           {if $poll_results_publicly_visible}checked{/if} id="results_publicly_visible"/>
                                    {__('Step 1', "The results are publicly visible")}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>






                <p class="text-right">
                    <input type="hidden" name="type" value="$poll_type"/>
                    <button name="{$goToStep2}" value="{$poll_type}" type="submit"
                            class="btn btn-success">{__('Step 1', 'Go to step 2')}</button>
                </p>

                <script type="text/javascript">document.formulaire.title.focus();</script>

            </form>
        </div>
    </div>
    <noscript>
        <div class="alert alert-danger">
            {__('Step 1', 'Javascript is disabled on your browser. Its activation is required to create a poll.')}
        </div>
    </noscript>
    <div id="cookie-warning" class="alert alert-danger" style="display:none">
        {__('Step 1', 'Cookies are disabled on your browser. Theirs activation is required to create a poll.')}
    </div>
{/block}