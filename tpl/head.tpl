<!DOCTYPE html>
    <html lang="{$lang}">
    <head>
        <meta charset="utf-8">

        {if !empty($title)}
            <title>{$title} - {$APPLICATION_NAME}</title>
        {else}
            <title>{$APPLICATION_NAME}</title>
        {/if}

        <link rel="stylesheet" href="{$SERVER_URL}css/bootstrap.min.css">
        <link rel="stylesheet" href="{$SERVER_URL}css/datepicker3.css">
        <link rel="stylesheet" href="{$SERVER_URL}css/style.css">
        <link rel="stylesheet" href="{$SERVER_URL}css/frama.css">
        <link rel="stylesheet" href="{$SERVER_URL}css/print.css" media="print">
        <script type="text/javascript" src="{$SERVER_URL}js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="{$SERVER_URL}js/bootstrap.min.js"></script>
        <script type="text/javascript" src="{$SERVER_URL}js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="{$SERVER_URL}js/locales/bootstrap-datepicker.{$lang}.js"></script>
        <script type="text/javascript" src="{$SERVER_URL}js/core.js"></script>
        
        {if !empty($nav_js)}
            <script src="/nav/nav.js" id="nav_js" type="text/javascript" charset="utf-8"></script><!-- /Framanav -->
        {/if}

    </head>
    <body>
    <div class="container ombre">