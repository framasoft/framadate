<div class="collapse{if $advanced_errors} in{/if}" id="optionnal" {if $advanced_errors}aria-expanded="true"{/if}>

    {include 'part/create_poll/value_max.tpl'}

    {include 'part/create_poll/customized_url.tpl'}

    {include 'part/create_poll/password.tpl'}

    {include 'part/create_poll/permissions.tpl'}


    {if $use_smtp}
        {include 'part/create_poll/receive_notifications.tpl'}
    {/if}

    {include 'part/create_poll/email_collection.tpl'}

</div> {* END div.collapse *}
