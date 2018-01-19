{extends file='page.tpl'}

{block name="header"}
    <script src="{"js/jquery-ui.min.js"|resource}" type="text/javascript"></script>
    <script src="{"js/Chart.min.js"|resource}" type="text/javascript"></script>
    <script src="{"js/Chart.StackedBar.js"|resource}" type="text/javascript"></script>
    <script src="{"js/app/studs.js"|resource}" type="text/javascript"></script>
    <link rel="stylesheet" href="{'css/jquery-ui.min.css'|resource}">

{/block}

{block name=main}


{include 'part/password_request.tpl' active=$poll->active}

       
{/block}
