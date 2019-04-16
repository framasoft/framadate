<!DOCTYPE html>
<html lang="{$locale}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    {if !empty($title)}
        <title>{$title|html} - {$APPLICATION_NAME|html}</title>
    {else}
        <title>{$APPLICATION_NAME|html}</title>
    {/if}
    <meta name="description" content="{__('Generic', 'Framadate is an online service for planning an appointment or making a decision quickly and easily.')}" />

    {if isset($favicon)}
        <link rel="icon" href="{$favicon|resource}">
    {/if}

    <link rel="stylesheet" href="{'css/bootstrap.min.css'|resource}">
    <link rel="stylesheet" href="{'css/datepicker3.css'|resource}">
    <link rel="stylesheet" href="{'css/style.css'|resource}">
    <link rel="stylesheet" href="{'css/frama.css'|resource}">
    <link rel="stylesheet" href="{'css/print.css'|resource}" media="print">
    {if $provide_fork_awesome}
        <link rel="stylesheet" href="{'css/fork-awesome.min.css'|resource}">
    {/if}
    <script type="text/javascript" src="{'js/jquery-1.12.4.min.js'|resource}"></script>
    <script type="text/javascript" src="{'js/bootstrap.min.js'|resource}"></script>
    <script type="text/javascript" src="{'js/bootstrap-datepicker.js'|resource}"></script>
    {if 'en' != $locale}
    <script type="text/javascript" src="{$locale|datepicker_path|resource}"></script>
    {/if}
    <script type="text/javascript" src="{'js/core.js'|resource}"></script>

    {block name="header"}{/block}

</head>
<body>
{if $use_nav_js}
    <script src="https://framasoft.org/nav/nav.js" type="text/javascript"></script>
{/if}
<div class="container ombre">

{include file='header.tpl'}

{block name=main}{/block}

</main>
</div> <!-- .container -->
{if isset($tracking_code)}
    {$tracking_code}
{/if}
<section class="footer">{__f('Version', 'Version %s', $APPLICATION_VERSION)}</section>
</body>
</html>
