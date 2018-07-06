<div id="hint_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">{__('Generic', 'Information')}</h4>
            </div>
            <div class="modal-body">
                {if $active}
                    <div class="alert alert-info">
                        <p>{__('studs', 'If you want to vote in this poll, you have to give your name, make your choice, and submit it by selecting the save button at the end of the line.')}</p>

                        <p aria-hidden="true"><b>{__('Generic', 'Legend:')}</b> <span
                                    class="glyphicon glyphicon-ok"></span>
                            = {__('Generic', 'Yes')}, <b>(<span class="glyphicon glyphicon-ok"></span>)</b>
                            = {__('Generic', 'Under reserve')}, <span class="glyphicon glyphicon-ban-circle"></span>
                            = {__('Generic', 'No')}</p>
                    </div>
                {else}
                    <div class="alert alert-danger">
                        <p>{__('studs', 'The administrator locked this poll. Votes and comments are frozen, it is no longer possible to participate')}</p>

                        <p aria-hidden="true"><b>{__('Generic', 'Legend:')}</b> <span
                                    class="glyphicon glyphicon-ok"></span>
                            = {__('Generic', 'Yes')}, <b>(<span class="glyphicon glyphicon-ok"></span>)</b>
                            = {__('Generic', 'Under reserve')}, <span class="glyphicon glyphicon-ban-circle"></span>
                            = {__('Generic', 'No')}</p>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
