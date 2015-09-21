<!DOCTYPE html>
<html lang="{$locale}">
<head>
    <meta charset="utf-8">

    {if !empty($title)}
        <title>{$title|html} - {$APPLICATION_NAME|html}</title>
    {else}
        <title>{$APPLICATION_NAME|html}</title>
    {/if}

    <link rel="stylesheet" href="{'css/bootstrap.min.css'|resource}">
    <link rel="stylesheet" href="{'css/datepicker3.css'|resource}">
    <link rel="stylesheet" href="{'css/style.css'|resource}">
    <link rel="stylesheet" href="{'css/frama.css'|resource}">
    <link rel="stylesheet" href="{'css/print.css'|resource}" media="print">
    <script type="text/javascript" src="{'js/jquery-1.11.1.min.js'|resource}"></script>
    <script type="text/javascript" src="{'js/bootstrap.min.js'|resource}"></script>
    <script type="text/javascript" src="{'js/bootstrap-datepicker.js'|resource}"></script>
    <script type="text/javascript" src="{"js/locales/bootstrap-datepicker.$locale.js"|resource}"></script>
    <script type="text/javascript" src="{'js/core.js'|resource}"></script>

    {block name="header"}{/block}

</head>
<body>
{if $use_nav_js}
    <script src="https://n2.framasoft.org/nav/nav.js" type="text/javascript"></script>
{/if}
<div class="container ombre">

{include file='header.tpl'}

{block name=main}{/block}

</main>
</div> <!-- .container -->
</body>
</html>
