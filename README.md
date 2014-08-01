Framadate est un fork du projet STUdS : https://sourcesup.cru.fr/projects/studs/

Framadate est le projet qui motorise framadate.org pour framasoft.org

Les auteurs principaux de Framadate sont :
- Simon LEBLANC
- Pierre-Yves GOSSET

Les auteurs principaux du projet STUdS sont :
- Guilhem BORGHESI
- Raphaël DROZ


==========================================================================

Université de Strasbourg - Direction Informatique
Auteur : Guilhem BORGHESI
Création : Février 2008

borghesi@unistra.fr

Ce logiciel est régi par la licence CeCILL-B soumise au droit français et
respectant les principes de diffusion des logiciels libres. Vous pouvez
utiliser, modifier et/ou redistribuer ce programme sous les conditions
de la licence CeCILL-B telle que diffusée par le CEA, le CNRS et l'INRIA
sur le site "http://www.cecill.info".

Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
pris connaissance de la licence CeCILL-B, et que vous en avez accepté les
termes. Vous pouvez trouver une copie de la licence dans le fichier LICENCE.

==========================================================================

Université de Strasbourg - Direction Informatique
Author : Guilhem BORGHESI
Creation : Feb 2008

borghesi@unistra.fr

This software is governed by the CeCILL-B license under French law and
abiding by the rules of distribution of free software. You can  use,
modify and/ or redistribute the software under the terms of the CeCILL-B
license as circulated by CEA, CNRS and INRIA at the following URL
"http://www.cecill.info".

The fact that you are presently reading this means that you have had
knowledge of the CeCILL-B license and that you accept its terms. You can
find a copy of this license in the file LICENSE.

==========================================================================

=============================================================================
Fichiers de l'application
=============================================================================

index.php
	La page d'accueil de STUdS
studs.php
	La page de présentation de sondage
adminstuds.php
	La page d'administration réservée à l'auteur du sondage
infos_sondage.php
	La page (1/2) de création de sondage récupérant les informations générales
choix_date.php
	La page de création (2/2) pour un sondage pour déterminer une date
choix_autre.php
	La page de création (2/2) pour un sondage sur un sujet quelconque
creation_sondage.php
	Le fichier qui récupérent les informations des pages précédentes pour procéder à l'insertion du nouveau sondage dans la base PostgreSQL
style.css
	Le fichier CSS de style pour toute l'application
app/inc/constants.php
	Le fichier contenant les constantes à changer en fonction de la machine locale
app/inc/functions.php
	Le fichier contenant quelques fonctions récurrentes de l'application
app/inc/i18n.php
	Le fichier contenant quelques fonctions récurrentes de l'application relatives à l'internationalisation
README
	Ce fichier
INSTALL
	Le fichier contenant les informations d'installation sur l'application
CHANGELOG
	Le fichier contenant toutes les modifications de l'application entre les différentes versions
contacts.php
	La page permettant aux usagers de poser une question à l'administrateur de l'application
apropos.php
	La page expliquant les détails techniques relatifs à l'application et les dernieres modifications et celles à venir sur l'application
bandeaux.php
	Le fichier contenant tous les bandeaux des pages PHP de l'application
favicon.ico
	L'icone de favoris de l'application
sources.php
	La page qui propose les sources de l'application
exportics.php
	Le fichier d'export de la meilleure date au format iCAL (fichier .ICS)
exportcsv.php
	Le fichier d'export de tous le tableau des participants avec leurs réponses dans un tableur (format .CSV)
exportpdf.php
	Le fichier d'export de la lettre de convocation que le créateur du sondage pourra envoyer aux participants (format .PDF)

admin/
	Le répertoire réservé à l'administrateur de l'application
admin/.htaccess
	Le fichier gérant les droits restreints du répertoire ADMIN
admin/.htpasswd
	Le fichier contenant les passwd des logins ayant accès au répertoire ADMIN
admin/index.php
	La page présentant tous les sondages actuellement dans la base à l'administrateur
admin/log_studs.txt
	Le fichier contenant un historique de toutes les creations/suppressions de sondage dans la base

errors/
	Le répertoire contenant toutes les pages d'erreurs
errors/error-forbidden.php
	La page qui indique dans la charte graphique de l'application l'erreur "501 forbidden"
errors/maintenance.php
	La page qui indique que l'application est en maintenance temporaire

export/
	Le répertoire qui contient tous les exports ICS

iCalcreator/
	Le répertoire qui contient les librairies d'export en iCal

php2pdf/
	Le répertoire qui contient les librairies d'export en PDF

scripts/
	Le répertoire qui contient tous les scripts de l'application

sources/
	Le répertoire qui contient les sources de l'application disponible sur la page sources.php

locale/
	Le répertoire qui contient les fichiers de traduction modifiables (.po) et compilés (.mo)
	au format gettext

=============================================================================
	Validations des pages
=============================================================================

Toutes les pages de STUdS sont validées HTML 4.01 Strict.
La CSS de STUdS est validée CSS 2.1.

=============================================================================
Technologies utilisées
=============================================================================

- PHP 5.4.4, php-fpdf, php-adodb, php-gettext
- PostgreSQL, mysql
- Apache
- iCalcreator
- POedit
- Icônes : Deleket (http://deleket.deviantart.com/) et DryIcons (http://dryicons.com)

=============================================================================
Compatibilités des navigateurs
Dernière mise à jour le 21 avril 2014
=============================================================================

- Firefox : Ubuntu 13.10/FF28
- Chrome : Ubuntu 13.10/Chromium33
- Opera (non testé)
- Konqueror
- Links (non testé, inutile)
- Safari (non testé)
- IE : Win7/IE9

-----------------
Janvier 2008
Guilhem BORGHESI
Université de Strasbourg

Mai 2010
Raphaël DROZ, raphael.droz@gmail.com

