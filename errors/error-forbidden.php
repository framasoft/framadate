<?php
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Auteur : Guilhem BORGHESI
//Création : Février 2008
//
//borghesi@unistra.fr
//
//Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
//respectant les principes de diffusion des logiciels libres. Vous pouvez
//utiliser, modifier et/ou redistribuer ce programme sous les conditions
//de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA 
//sur le site "http://www.cecill.info".
//
//Le fait que vous puissiez accéder à cet en-tête signifie que vous avez 
//pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
//termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.
//
//==========================================================================
//
//Université de Strasbourg - Direction Informatique
//Author : Guilhem BORGHESI
//Creation : Feb 2008
//
//borghesi@unistra.fr
//
//This software is governed by the CeCILL-B license under French law and
//abiding by the rules of distribution of free software. You can  use, 
//modify and/ or redistribute the software under the terms of the CeCILL-B
//license as circulated by CEA, CNRS and INRIA at the following URL
//"http://www.cecill.info". 
//
//The fact that you are presently reading this means that you have had
//knowledge of the CeCILL-B license and that you accept its terms. You can
//find a copy of this license in the file LICENSE.
//
//==========================================================================


include '../bandeaux.php';

print_header ( _('Error!') );

echo '<body>'."\n";

bandeau_tete();
bandeau_titre(_("Make your polls"));

echo '
    <div class=corpscentre>
        <h2>' . _("You're not allowed to see this folder") . '</h2>
        <p>Vous devez, pour cela, initier votre connexion depuis une machine de l’Université.<br />
        Si vous avez un compte &agrave; l’Universit&eacute;, vous pouvez &eacute;galement utiliser le 
        <a href=\"https://www-crc.u-strasbg.fr/osiris/services/vpn\">VPN s&eacute;curis&eacute;</a>.</p>
        <p>' . _("Back to the homepage of ") . ' ' . '<a href="'.get_server_name().'">' . NOMAPPLICATION . '</a>.</p>
    </div>'."\n";

// Affichage du bandeau de pied
sur_bandeau_pied();
bandeau_pied();

echo '</body>
</html>';
