{* Description buttons for markdown *}

<div class="btn-group" role="group" aria-label="...">
    <button type="button" id="rich-editor-button" class="btn btn-default btn-xs{if $default_to_marldown_editor} active{/if}">{__('PollInfo', 'Rich editor')}</button>
    <button type="button" id="simple-editor-button" class="btn btn-default btn-xs{if !$default_to_marldown_editor} active{/if}">{__('PollInfo', 'Simple editor')}</button>
</div>

<a href="" data-toggle="modal" data-target="#markdown_modal"><i class="glyphicon glyphicon-info-sign"></i></a><!-- TODO Add accessibility -->

<div id="markdown_modal" class="modal fade">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">{__('Generic', 'Markdown')}</h4>
            </div>
            <div class="modal-body">
                    <p>
                        {__('Step 1', 'To make the description more attractive, you can use the Markdown format.')}
                     </p>
                    <p>

                        {__('Step 1', 'You can enable or disable the editor at will.')}
                    </p>
                    <p>
                        {__('Step 1', 'More informations here:')}
                        <a href="http://{$locale|locale_2_lang}.wikipedia.org/wiki/Markdown">http://{$locale|locale_2_lang}.wikipedia.org/wiki/Markdown</a>
                    </p>
            </div>
        </div>
    </div>
</div>
