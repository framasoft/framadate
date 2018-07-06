<?php
/**
 * Fichier de configuration pour l'authentification de Framadate avec SimpleSAMLPHP
 * Il est indispensable d'avoir configuré un Service Provider au préalable
 *
 */

/**
 * Chemin de la libraire PHPSimpleSAML
 */
const SAML_PATH = '/home/framadate/www/simplesamlphp/lib/_autoload.php';

/**
 * Active la connexion SSO
 */
const SAML_SSO_ACTIVE = true;

/**
 * Prérempli les coordonées (nom / email) dans le formulaire de création d'un sondage
 */
const AUTO_COMPLETE_USER = true;

/**
 * Lise des pages que l'on veut sécuriser
 */
$hashPagesToSecure = array(

    'front' => array(
        'create_poll'       => true,    // Front - Création d'un sondage
        'find_polls'        => true,    // Front - Ou sont mes sondages
        'studs'             => false,   // Front - Réponse à un sondage
        'adminstuds'        => true,    // Front - Edition d'un sondage
        'exportcsv'         => false,   // Front - export CSV d'un sondage
    ),

    'admin' => array(
        'admin/index'       => true,    // Administration - Accueil
        'admin/polls'       => true,    // Administration - Sondages
        'admin/purge'       => true,    // Administration - Purge
        'admin/logs'        => true,    // Administration - Historique
        'admin/migration'   => true,    // Administration - Migration
        'admin/check'       => true,    // Administration - Vérifications de l'installation
    )
);

/**
 * Champs de l'AD à mapper avec SAML
 */
$hashConfigFieldsMapping = array(
    'name'  => 'cn',    // Nom complet
    'email' => 'mail'   // Adresse mail
);

/**
 * Attributs de l'AD qui sont authorisés à se connecter aux différentes parties de Framadate
 */
$hashGroupAuthorized = array(
    'front' => array(
        'resMemberOf' => 'Framadate', // Valeurs à remplacer
    ),
    'admin' => array(
        'resMemberOf' => 'FramadateAdmin' // Valeurs à remplacer
    )
);

if(SAML_SSO_ACTIVE == true){

    if(!file_exists(SAML_PATH)){
        die('Impossible de charger la librairie SimplePHPSAML');
    }
    require_once(SAML_PATH);

    //On parcourt les pages des sections (front / back)
    foreach($hashPagesToSecure as $strSection => $hashPages){

        // On vérifie si la page courante doit-être sécurisée
        foreach($hashPages as $strPageName => $boolSecure){

            // SI page courante doit-être sécurisé on vérifie l'accès
            if(strpos($_SERVER['SCRIPT_FILENAME'], $strPageName) !== false && $boolSecure == true){
                $objAuthSaml = new SimpleSAML_Auth_Simple('framadate-sp');
                $objAuthSaml->requireAuth();

                $hashAuthAttributes = $objAuthSaml->getAttributes();

                // On récupère le nom et l'email de l'utilisateur
                $strUserName = array_shift($hashAuthAttributes[$hashConfigFieldsMapping['name']]);
                $strUserEmail = array_shift($hashAuthAttributes[$hashConfigFieldsMapping['email']]);

                // On assigne à Smarty l'URL de déconnexion + l'adresse email pour retrouver les sondages
                $strHttpPrepfix = (substr($_SERVER['SCRIPT_URI'], 0, 5) == 'https')? 'https' : 'http';
                $smarty->assign('saml_logout', $objAuthSaml->getLogoutURL($strHttpPrepfix.'://'.$_SERVER['HTTP_HOST']));
                $smarty->assign('email', $strUserEmail);

                // Si on est dans la section (front / back), on regarde si l'utilisateur est bien autorisé
                if(isset($hashGroupAuthorized[$strSection])) {
                    foreach ($hashGroupAuthorized[$strSection] as $strAttribute => $strValue) {
                        if (!in_array(trim($strValue), $hashAuthAttributes[$strAttribute])) {
                           header('Location:'.$strHttpPrepfix.'://'.$_SERVER['HTTP_HOST'].'/forbidden.php');
                           break;
                        }
                    }
                }

                // Si il s'agit d'un nouveau sondage on affecte les infos du user en session
                if(strpos($_SERVER['SCRIPT_FILENAME'], 'create_poll') !== FALSE && AUTO_COMPLETE_USER == true){
                    if(isset($_SESSION['form']) && empty($_SESSION['form']->id)){
                        $_SESSION['form']->admin_name = $strUserName;
                        $_SESSION['form']->admin_mail = $strUserEmail;
                    }
                }
                break;
            }
        }
    }
}