<div id="hint_modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true">&times;</i>
                </button>
                <h4 class="modal-title">{t('Generic', 'Information')}</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <p>{t('adminstuds', 'As poll administrator, you can change all the lines of this poll with this button')}
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                        <span class="sr-only">{t('Generic', 'Edit')}</span>,
                        {t('adminstuds', 'remove a column or a line with')}
                        <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                        <span class="sr-only">{t('Generic', 'Remove')}</span>
                        {t('adminstuds', 'and add a new column with')}
                        <i class="fa fa-plus text-success" aria-hidden="true"></i>
                        <span class="sr-only">{t('adminstuds', 'Add a column')}</span>.
                    </p>

                    <p>{t('adminstuds', 'Finally, you can change the properties of this poll such as the title, the comments or your email address.')}</p>

                    <p aria-hidden="true">
                        <b>{t('Generic', 'Legend:')}</b>
                        <i class="fa fa-check"></i> = {t('Generic', 'Yes')},
                        <b>(<i class="fa fa-check"></i>)</b> = {t('Generic', 'Under reserve')},
                        <i class="fa fa-times"></i> = {t('Generic', 'No')}
                    </p>
                </div>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->
