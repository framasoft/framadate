# Treeview of framadate

Here are the main files and directories you need to know in order to develop on framadate project.

## Admin

* admin/
    * Le répertoire réservé à l'administrateur de l'application
* admin/index.php
    * The admin home page, it holds links to other admin pages
* admin/logs.php
    * The page to see the application logs
* admin/migration.php
    * This page do the needed migration at loading
* admin/polls.php
    * The list of all polls
* admin/purge.php
    * The page to manually trigger the purge (even if the purge is still executed at poll creation)

## Main

### Root files

* index.php
    * Landing page framadate
* studs.php
    * La page de présentation de sondage
* adminstuds.php
    * La page d'administration réservée à l'auteur du sondage
* create_poll.php
    * La page (1/2) de création de sondage récupérant les informations générales
* create_date_poll.php
    * La page de création (2/2) pour un sondage pour déterminer une date
* crete_classic_poll.php
    * La page de création (2/2) pour un sondage sur un sujet quelconque
* bandeaux.php
    * Le fichier contenant tous les bandeaux des pages PHP de l'application
* favicon.ico
    * L'icone de favoris de l'application
* exportcsv.php
    * Le fichier d'export de tous le tableau des participants avec leurs réponses dans un tableur (format .CSV)
* CHANGELOG.md
    * Le fichier contenant toutes les modifications de l'application entre les différentes versions

### app/ directory

* app/inc/config.template.php
    * Le fichier contenant le paramètrage de l'application, il faut le dupliquer vers app/inc/config.php avant de l'éditer
* app/inc/constants.php
    * Le fichier contenant les constantes de l'application
* app/inc/i18n.php
    * Le fichier contenant quelques fonctions récurrentes de l'application relatives à l'internationalisation
* app/inc/init.php
    * Le fichier chargé à l'initialisation de chaque page
* app/inc/smarty.php
    * Le fichier qui prépare le context de Smarty

### other directories

* tpl/
    * The directory that hold all the Smarty templates

* css/style.css et css/frama.css
    * Les fichiers CSS de style pour toute l'application

* scripts/
    * Le répertoire qui contient tous les scripts de l'application

* locale/
    * Le répertoire qui contient les fichiers de traduction modifiables (.po) et compilés (.mo) au format gettext
