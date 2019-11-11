<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
use Framadate\Utils;

include_once __DIR__ . '/app/inc/init.php';

// bandeaux de titre
function bandeau_titre($titre)
{
    global $ALLOWED_LANGUAGES;
    $img = ( IMAGE_TITRE ) ? '<img src="' . Utils::get_server_name() . IMAGE_TITRE . '" alt="' . NOMAPPLICATION . '" class="img-responsive">' : '';
    echo '
    <header role="banner">';
    if(count($ALLOWED_LANGUAGES) > 1){
        echo '<form method="post" action="" class="hidden-print">
            <div class="input-group input-group-sm pull-right col-md-2 col-xs-4">
                <select name="lang" class="form-control" title="' . t('Language selector', 'Select language') . '" >' . liste_lang() . '</select>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default btn-sm" title="' . t('Language selector', 'Change language') . '">OK</button>
                </span>
            </div>
        </form>';
    }
    echo '
        <h1><a href="' . Utils::get_server_name() . '" title="' . t('Generic', 'Home') . ' - ' . NOMAPPLICATION . '">' . $img . '</a></h1>
        <h2 class="lead"><i>' . $titre . '</i></h2>
        <hr class="trait" role="presentation" />
    </header>
    <main role="main">';
}

function liste_lang()
{
    global $ALLOWED_LANGUAGES; global $locale;

    $str = '';

    foreach ($ALLOWED_LANGUAGES as $k => $v ) {
        if (substr($k,0,2)===$locale) {
            $str .= '<option lang="' . substr($k,0,2) . '" selected value="' . $k . '">' . $v . '</option>' . "\n" ;
        } else {
            $str .= '<option lang="' . substr($k,0,2) . '" value="' . $k . '">' . $v . '</option>' . "\n" ;
        }
    }

  return $str;
}

function bandeau_pied()
{
    echo '
    </main>
    </div> <!-- .container -->
</body>
</html>' . "\n";
}
