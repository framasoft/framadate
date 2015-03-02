[![](https://git.framasoft.org/assets/logo-black-f52905a40830b30aa287f784b537c823.png)](https://git.framasoft.org)

![English:](http://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Flag_of_the_United_Kingdom.svg/20px-Flag_of_the_United_Kingdom.svg.png) **Framasoft uses GitLab** for the development of its free softwares. Our Github repositories are only mirrors.
If you want to work with us, **fork us on [git.framasoft.org](https://git.framasoft.org)**. (no registration needed, you can sign in with your Github account)

![Français :](http://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Flag_of_France.svg/20px-Flag_of_France.svg.png) **Framasoft utilise GitLab** pour le développement de ses logiciels libres. Nos dépôts Github ne sont que des mirroirs.
Si vous souhaitez travailler avec nous, **forkez-nous sur [git.framasoft.org](https://git.framasoft.org)**. (l'inscription n'est pas nécessaire, vous pouvez vous connecter avec votre compte Github)
* * *

![English:](http://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Flag_of_the_United_Kingdom.svg/20px-Flag_of_the_United_Kingdom.svg.png)
This software is governed by the CeCILL-B license. If a copy of this license
is not distributed with this file, you can obtain one at
[http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt](http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt)

Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ  
Authors of Framadate/OpenSondate: [Framasoft](https://git.framasoft.org/framasoft/framadate)

![Français :](http://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Flag_of_France.svg/20px-Flag_of_France.svg.png) 
Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
[http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt](http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt)

Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ  
Auteurs de Framadate/OpenSondage : [Framasoft](https://git.framasoft.org/framasoft/framadate)

* * * 

#Framadate
[Framadate](https://framadate.org) est un fork du projet [STUdS](https://sourcesup.cru.fr/projects/studs/).  
Il est développé par l'association [Framasoft](http://framasoft.org).

##Fichiers de l'application

### Administration
* `/admin`  
    Le répertoire réservé à l'administrateur de l'application  
* `admin/index.php`  
    La page présentant tous les sondages actuellement dans la base à l'administrateur  
* `admin/log_studs.txt`  
    Le fichier contenant un historique de toutes les creations/suppressions de sondage dans la base  

* `install/` (pas utilisé - en développement)  
    Le répertoire qui contient les scripts chargés de simplifier la procédure d'installation  
* `scripts/` (pas utilisé)  
    Le répertoire qui contient quelques vieux scripts pour la maintenance de l'application

### Application
* `app/inc/constants.php.template`  
    Le fichier contenant les constantes à changer en fonction de la machine locale  
* `app/classes/Framadate/Utils.php`  
    Le fichier contenant quelques fonctions récurrentes de l'application  
* `app/inc/i18n.php`  
    Le fichier contenant quelques fonctions récurrentes de l'application relatives à l'internationalisation  
* `app/inc/init.php`  
    Le fichier qui charge les dépendances et ouvre la connexion à la base de données  

* `css/`  
    Les fichiers CSS de l'application (dont ceux de Bootstrap)  
* `fonts/`  
    Les fichiers des icônes de Bootstrap  
* `images/`  
    Logo et images de la page d'accueil  
* `js/`  
    Les fichiers javascript de l'application (dont ceux de Bootstrap et de jQuery)  

* `locale/`  
    Le répertoire qui contient les fichiers de traduction modifiables (.po) et compilés (.mo)
    au format gettext

* `index.php`  
    La page d'accueil de STUdS  
* `studs.php`  
    La page de présentation de sondage  
* `adminstuds.php`  
    La page d'administration réservée à l'auteur du sondage  
* `infos_sondage.php`  
    La page (1/2) de création de sondage récupérant les informations générales  
* `choix_date.php`  
    La page de création (2/2) pour un sondage pour déterminer une date  
* `choix_autre.php`  
    La page de création (2/2) pour un sondage sur un sujet quelconque  
* `creation_sondage.php`  
    Le fichier qui récupérent les informations des pages précédentes pour procéder à l'insertion du nouveau sondage dans la base PostgreSQL

* `bandeaux.php`  
    Le fichier contenant les éléments de l'entête et du pied de page de l'application  
* `exportcsv.php`  
    Le fichier d'export de tous le tableau des participants avec leurs réponses dans un tableur (format .CSV)  
* `favicon.ico`  
    L'icone de favoris de l'application  

### Infos
* `AUTHORS.md`  
    Liste des principaux développeurs du logiciel  
* `README.md`  
    Ce fichier  
* `INSTALL.md`  
    Le fichier contenant les informations d'installation sur l'application  
* `CHANGELOG.md`  
    Le fichier contenant la liste des principale modifications de l'application entre les différentes versions
    
##Technologies utilisées

- PHP 5.4.4, php-adodb, php-gettext, composer
- Bootstrap, jQuery, Bootstrap Datepicker
- MySQL
- Nginx, Apache
- POedit

##Compatibilités des navigateurs
(Dernière mise à jour le 21 avril 2014)

- Firefox : Ubuntu 13.10/FF28
- Chrome : Ubuntu 13.10/Chromium33
- Opera (non testé)
- Konqueror
- Safari (non testé)
- IE : Win7/IE9
