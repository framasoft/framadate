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
    <meta name="description" content="{__('Generic', 'Framadate is an online service for planning an appointment or make a decision quickly and easily.')}" />

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
    <script src="{'js/jquery-3.6.0.min.js'|resource}"></script>
    <script src="{'js/bootstrap.min.js'|resource}"></script>
    <script src="{'js/bootstrap-datepicker.js'|resource}"></script>
    {if 'en' != $locale}
    <script src="{$locale|datepicker_path|resource}"></script>
    {/if}
    <script src="{'js/core.js'|resource}"></script>

    {block name="header"}{/block}

</head>
<body>
{if $use_nav_js}
    <script src="https://framasoft.org/nav/nav.js"></script>
{/if}
<div class="container ombre">

{include file='header.tpl'}

{block name=main}{/block}

</main>
</div> <!-- .container -->
{if isset($tracking_code)}
    {$tracking_code}
{/if}
</body>
</html>
