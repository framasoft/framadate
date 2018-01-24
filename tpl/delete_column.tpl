{extends file='page.tpl'}

{block name=main}
<form action="adminstuds.php?poll={$poll}&acce={$accessGranted}" method="POST">
    <div class="alert alert-danger text-center">
        <h2>You are delete the column {$column}</h2>
        <p><button class="btn btn-default" type="submit" name="cancel">Back to admin poll</button>
         
    </div>
</form>
{/block}
