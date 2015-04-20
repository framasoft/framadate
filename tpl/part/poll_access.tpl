{if $has_access}
    {$panel_type = "success"}
    {$panel_title = __("Restricted poll", "Access to the results of a poll restricted by password.")}
{else}
    {if $poll->results_publicly_visible}
        {$panel_type = "warning"}
        {$panel_title = __("Restricted poll", "The access to the poll is restricted to the results")}
    {else}
        {$panel_type = "danger"}
        {$panel_title = __("Restricted poll", "The poll access is restricted.")}
    {/if}
{/if}

<div class="panel panel-{$panel_type}" id="access">
    <div class="panel-heading">
        <h3 class="panel-title">{$panel_title}</h3>
    </div>
    {if !$has_access or !empty($login_message)}
        <div class="panel-body">
            {if !$has_access}
                <form action="{poll_url id=$poll_id}{if $poll->results_publicly_visible}#access{/if}" method="POST" role="form" class="form-inline">
                    <div class="form-group">
                        <label for="password">{__("Restricted poll", "Password")}</label>
                        <input type="password" name="password" id="password" class="form-control" {if !empty($login_message) and $login_message->type=="danger"}aria-describeby="password_form_message"{/if}/>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm">
                        {__("Restricted poll", "Submit")}
                        <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span>
                    </button>
                </form>
            {/if}
            {if !empty($login_message)}
                <p id="password_form_message" class="text-{$login_message->type}">{$login_message->message}</p>
            {/if}
        </div>
    {/if}
</div>