    <header class="clearfix">
    {if count($langs)>1}
        <form method="post" class="hidden-print">
            <div class="input-group input-group-sm pull-right col-xs-12 col-sm-2">
                <select name="lang" class="form-control" title="{__('Language selector', 'Select the language')}" >
                {foreach $langs as $lang_key=>$lang_value}
                    <option lang="{substr($lang_key, 0, 2)}" {if substr($lang_key, 0, 2)==$locale}selected{/if} value="{$lang_key|html}">{$lang_value|html}</option>
                {/foreach}
                </select>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-sm" title="{__('Language selector', 'Change the language')}">OK</button>
                </span>
            </div>
        </form>
    {/if}

        <h1 class="row col-xs-12 col-sm-10">
            <a href="{$SERVER_URL|html}" title="{__('Generic', 'Home')} - {$APPLICATION_NAME|html}" >
                <img src="{$TITLE_IMAGE|resource}" alt="{$APPLICATION_NAME|html}" class="img-responsive"/>
            </a>
        </h1>
        {if !empty($title)}<h2 class="lead col-xs-12"><i>{$title|html}</i></h2>{/if}
        <div class="trait col-xs-12" role="presentation"></div>
    </header>
    <main>
