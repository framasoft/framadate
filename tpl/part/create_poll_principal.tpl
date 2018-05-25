<div class="alert alert-info">
    <p>
        {__('Step 1', 'You are in the poll creation section.')}<br/>
        {__('Step 1', 'Required fields cannot be left blank.')}
    </p>
</div>

<div class="form-group {$errors['name']['class']}">
    <label for="yourname" class="col-sm-4 control-label">{__('Generic', 'Your name')} *</label>

    <div class="col-sm-8">
        {if $useRemoteUser}
            <input type="hidden" name="name" value="{$form->admin_name}" />
            {$form->admin_name}
        {else}
            <input id="yourname" type="text" required name="name"
                   class="form-control" {$errors['name']['aria']} value="{$poll_name|html}"/>
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
                <input id="email" required type="email" name="mail"
                       class="form-control" {$errors['email']['aria']} value="{$poll_mail|html}"/>
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

<div class="form-group {$errors['title']['class']}">
    <label for="poll_title" class="col-sm-4 control-label">{__('Step 1', 'Poll title')} *</label>

    <div class="col-sm-8">
        <input id="poll_title" type="text" name="title" class="form-control"
               required {$errors['title']['aria']}
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

<div class="form-group {$errors['description']['class']}">
    <label for="poll_comments" class="col-sm-4 control-label">{__('Generic', 'Description')}</label>

    <div class="col-sm-8">
        {include 'part/description_markdown.tpl'}
        <div>
            <textarea id="poll_comments" name="description" class="form-control" {$errors['description']['aria']} rows="5">{$poll_description|escape}</textarea>
        </div>
    </div>
</div>
{if !empty($errors['description']['msg'])}
    <div class="alert alert-danger">
        <p id="poll_title_error">
            {$errors['description']['msg']}
        </p>
    </div>
{/if}

{* Optionnal parameters *}
<div class="col-sm-offset-3 col-sm-1 hidden-xs">
    <p class="lead">
        <i class="glyphicon glyphicon-cog" aria-hidden="true"></i>
    </p>
</div>
<div class="col-sm-8 col-xs-12">
    <span class="lead visible-xs-inline">
        <i class="glyphicon glyphicon-cog" aria-hidden="true"></i>
    </span>
    <a class="optionnal-parameters {if !$advanced_errors}collapsed{/if} lead" role="button"
       data-toggle="collapse" href="#optionnal"
       aria-expanded="{if $advanced_errors}false{else}true{/if}" aria-controls="optionnal">
        {__('Step 1', "Optional parameters")}
        <i class="caret" aria-hidden="true"></i>
        <i class="caret caret-up" aria-hidden="true"></i>
    </a>

</div>
<div class="clearfix"></div>
