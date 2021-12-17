{extends file='page.tpl'}

{block name=main}
    {if !empty($message)}
        <div class="alert alert-dismissible alert-{$message->type|html}" role="alert">{$message->message|html}{if $message->link != null}<br/><a href="{$message->link}">{$message->link}</a>{/if}<button type="button" class="close" data-dismiss="alert" aria-label="{__('Generic', 'Close')}"><span aria-hidden="true">&times;</span></button></div>
    {/if}
    <form method="post">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                <div class="form-group">
                    <div class="input-group">
                        <label for="mail" class="input-group-addon">{__('Generic', 'Your email address')}</label>
                        <input type="email" class="form-control" id="mail" name="mail" autofocus>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-md-offset-3 text-center">
                <button type="submit" class="btn btn-success">{__('FindPolls', 'Send me my polls')}</button>
            </div>
        </div>
    </form>
{/block}
