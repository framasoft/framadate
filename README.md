# Présentation du projet

![Gitlab](https://git.framasoft.org/assets/logo-black-f52905a40830b30aa287f784b537c823.png)[https://git.framasoft.org](https://git.framasoft.org)

![English](https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Flag_of_the_United_Kingdom.svg/20px-Flag_of_the_United_Kingdom.svg.png) **Framasoft uses GitLab** for the development of its free softwares. Our Github repositories are only mirrors.
If you want to work with us, **fork us on [git.framasoft.org](https://git.framasoft.org)**. (no registration needed, you can sign in with your Github account)

![Français](https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Flag_of_France.svg/20px-Flag_of_France.svg.png) **Framasoft utilise GitLab** pour le développement de ses logiciels libres. Nos dépôts Github ne sont que des mirroirs.
Si vous souhaitez travailler avec nous, **forkez-nous sur [git.framasoft.org](https://git.framasoft.org)**. (l'inscription n'est pas nécessaire, vous pouvez vous connecter avec votre compte Github)

---

# Compatibilités des navigateurs
_Dernière mise à jour le 21 avril 2014_

| Navigateur | Version testée          |
|------------|-------------------------|
| Firefox    | Ubuntu 13.10/FF28       |
| Chrome     | Ubuntu 13.10/Chromium33 |
| Opera      | (non testé)             |
| Konqueror  | (non testé)             |
| Links      | (non testé, inutile)    |
| Safari     | (non testé)             |
| IE         | Win7/IE9                |

# Installation

Un fichier est dédié à l'installation de framadate : [INSTALL.md](INSTALL.md).

# Comment contribuer

## De votre côté

1. Créer un compte sur [https://git.framasoft.org](https://git.framasoft.org)
1. Créer un fork du projet principal : [Créer le fork](https://git.framasoft.org/framasoft/framadate/fork/new)
1. Créer une branche nommée feature/[Description]
    * Où [Description] est une description en anglais très courte de ce qui va être fait
1. Faire des commits dans votre branche
1. Pusher la branche sur votre fork
1. Demander une merge request

## La suite se passe de notre côté

1. Quelqu'un relira votre travail
    * Essayez de rendre ce travail plus facile en organisant vos commits
1. S'il y a des remarques sur le travail, le relecteur fera des commentaires sur la merge request
1. Si la merge request lui semble correcte il peut merger votre travail avec la branche **develop**

## Corrections suite à une relecture

La relecture de la merge request peut vous amener à faire des corrections.
Vous pouvez faire ces corrections dans votre branche, ce qui aura pour effet de les ajouter à la merge request.

## Comprendre le code

Un fichier est dédié à l'appréhension du code de framadate : [Document technique](doc/TECHNICAL.md).

# Traductions

Les traductions se trouvent dans le dossier `locale`. Chaque langue est dans un fichier JSON différent organisé par section.

# Synthèses des librairies utilisées

[Smarty](http://www.smarty.net/),
gestion des templates pour PHP

[o80-i18n](https://github.com/olivierperez/o80-i18n),
système d'internationalisation

[PHP 5.4.4](http://php.net)

PostgreSQL ou MySQL

---

Framadate est un fork du projet [STUdS](https://sourcesup.cru.fr/projects/studs/), il motorise framadate.org pour framasoft.org

Les auteurs principaux de Framadate sont :
* Simon LEBLANC
* Pierre-Yves GOSSET

Les auteurs principaux du projet STUdS sont :
* Guilhem BORGHESI
* Raphaël DROZ

---

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

---

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

---

    Janvier 2008
    Guilhem BORGHESI
    Université de Strasbourg

    Mai 2010
    Raphaël DROZ, raphael.droz@gmail.com
