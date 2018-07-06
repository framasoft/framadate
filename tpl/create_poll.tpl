{extends file='page.tpl'}

{block name="header"}
    <script src="{"js/simplemde.min.js"|resource}" type="text/javascript"></script>
    <script src="{"js/mde-wrapper.js"|resource}" type="text/javascript"></script>
    <script src="{"js/app/create_poll.js"|resource}" type="text/javascript"></script>
    <link rel="stylesheet" href="{"css/app/create_poll.css"|resource}">
    <link rel="stylesheet" href="{"css/simplemde.min.css"|resource}">
{/block}

{block name=main}
    <div class="row" style="display:none" id="form-block">
        <div class="col-md-8 col-md-offset-2">
            <form name="formulaire" id="formulaire" action="" method="POST" class="form-horizontal" role="form">


                {include 'part/create_poll_principal.tpl'}
                {include 'part/create_poll_collapsed.tpl'}

                <p class="text-right">
                    <button name="{$goToStep2}" value="{$poll_type}" type="submit"
                            class="btn btn-success">{__('Step 1', 'Go to step 2')}</button>
                </p>

                <script type="text/javascript">document.formulaire.title.focus();</script>

            </form>
        </div>
    </div>
    <noscript>
        <div class="alert alert-danger">
            {__('Step 1', 'JavaScript is disabled on your browser. It is required to create a poll.')}
        </div>
    </noscript>
    <div id="cookie-warning" class="alert alert-danger" style="display:none">
        {__('Step 1', 'Cookies are disabled on your browser. They are required to be able to create a poll.')}
    </div>
{/block}
