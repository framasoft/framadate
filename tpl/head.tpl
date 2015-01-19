<!DOCTYPE html>
    <html lang="{$html_lang}">
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
        <script type="text/javascript" src="{"js/locales/bootstrap-datepicker.$html_lang.js"|resource}"></script>
        <script type="text/javascript" src="{'js/core.js'|resource}"></script>
        
        {if !empty($nav_js)}
            <script src="{'nav/nav.js'|resource}" id="nav_js" type="text/javascript" charset="utf-8"></script><!-- /Framanav -->
        {/if}

    </head>
    <body>
    <div class="container ombre">