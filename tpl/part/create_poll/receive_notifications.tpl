<div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="receiveNewVotes"
                       {if $poll_receiveNewVotes}checked{/if}
                       id="receiveNewVotes">
                {__('Step 1', 'Receive an email for each new vote')}
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-4 col-sm-8">
        <div class="checkbox">
            <label>
                <input type="checkbox" name="receiveNewComments"
                       {if $poll_receiveNewComments}checked{/if}
                       id="receiveNewComments">
                {__('Step 1', 'Receive an email for each new comment')}
            </label>
        </div>
    </div>
</div>
