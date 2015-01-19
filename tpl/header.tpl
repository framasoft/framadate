    <header role="banner">
    {if count($langs)>1}
        <form method="post" action="" class="hidden-print">
            <div class="input-group input-group-sm pull-right col-md-2 col-xs-4">
                <select name="lang" class="form-control" title="{_("Select the language")}" >
                {foreach $langs as $lang_key=>$lang_value}
                    <option lang="{substr($lang_key, 0, 2)}" {if substr($lang_key, 0, 2)==$html_lang}selected{/if} value="{$lang_key|html}">{$lang_value|html}</option>
                {/foreach}
                </select>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-sm" title="{_("Change the language")}">OK</button>
                </span>
            </div>
        </form>
    {/if}

        <h1><a href="{$SERVER_URL|html}" title="{_("Home")} - {$APPLICATION_NAME|html}"><img src="{$TITLE_IMAGE|resource}" alt="{$APPLICATION_NAME|html}"/></a></h1>
        {if !empty($title)}<h2 class="lead"><i>{$title|html}</i></h2>{/if}
        <hr class="trait" role="presentation" />
    </header>
    <main role="main">