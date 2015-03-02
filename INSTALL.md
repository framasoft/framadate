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

#Avertissement
**Les instructions contenues ci-dessous dans ce fichier ne sont pas actualisées.** 
Vous trouverez là :  
=> **[le tutoriel d'installation complet](http://framacloud.org/cultiver-son-jardin/installation-de-framadate/)**  
(en Français uniquement pour le moment)

* * *

##Paramètres

Le fichier app/inc/constants.php.template contient le paramétrage par défaut de
l'application Framadate. Pour personnaliser votre installation, copiez
ce fichier sous le nom app/inc/constants.php et modifiez ce dernier.

##Configuration du fichier php.ini

Pour que les quotes simples soient acceptées dans la partie "Création de sondage", il faut que la variable magic_quotes_gpc soit activée ("On") dans le fichier php.ini.

##Base de données

STUdS fonctionne indépendemment de la base SQL utilisée, sous réserve que
le serveur dispose de l'extension [ADOdb](http://sourceforge.net/projects/adodb)

Cependant la base de donnée doit être créée au préalable.  
Deux scripts le faisant sont fournis :  
install.sql: pour postgresql  
install.mysql.sql: pour mysql

Pour postgresql :  
Après avoir renseigné les paramètres de la base de données, créez la
base et pré-chargez les données par défaut. Ceci ressemble à :

    % su - pgsql
    % createdb studs
    % psql -d studs -f install.sql

Attention : Si vous créez la base de données avec l'utilisateur "pgsql", il vous faudra faire un "grant all on <chaque table> to studs" pour donner les droits à l'utilisateur studs de lire et modifier la base. Les tables de l'applications sont décrites plus loin dans ce fichier dans la partie "Tables de la base de données".


##Accès à la page administrateur

Le répertoire admin/ contient un fichier .htaccess pour Apache, qui restreint l'accès
à la page d'administration de l'application.  
Modifiez le contenu de ce fichier .htaccess pour l'adapter au chemin du fichier .htpasswd
sur votre serveur.  
Le fichier .htpasswd à besoin d'être créé par vos soins en utilisant par exemple la commande
suivante :  
`htpasswd -mnb <admin_username> <admin_password>`

Un fichier `admin/logs_studs.txt` doit être créé et accessible en écriture
par votre serveur Web. Quelque chose comme :

    % touch admin/logs_studs.txt
    % chmod 700 admin/logs_studs.txt
    % chown www-data admin/logs_studs.txt

devrait convenir.

##Maintenance

Studs dispose d'une possibilité de mise en maintenance par le biais
d'un fichier .htaccess.  
La section `<Directory>` relative à Studs, dans la configuration d'Apache
doit au moins contenir :  
`AllowOverride AuthConfig Options`  
Le fichier .htaccess correspondant doit être modifier pour y configurer
l'adresse IP depuis laquelle s'effectue la maintenance.  
N'oubliez pas de le recommenter en intégralité une fois la maintenance effectuée.

##Tables de la base de données

Voici la structure des tables de l'application. La base se compose de trois tables :

- sondage : Le contenu de chacun des sondages,
- sujet_studs : les sujets ou dates de tous les sondages,
- user_studs : les identifiants des sondés de tous les sondages.

Chacune des tables contient les champs suivants :

SONDAGE

    Nom du champ                format              description

    id_sondage (clé primaire)   alpha-numérique     numéro du sondage aléatoire
    commentaires                text                commentaires liés au sondage
    mail_admin                  text                adresse de l'auteur du sondage
    nom_admin                   text                nom de l'auteur du sondage
    titre                       text                titre du sondage
    id_sondage_admin            alpha-numérique     numéro du sondage pour le lien d'administration
    date_fin                    alpha-numérique     date de fin su sondage au format SQL
    format                      text                format du sondage : D/D+ pour Date, A/A+ pour Autre
    mailsonde                   text                envoi de mail a l'auteur du sondage a chaque participation ("yes" ou vide)

SUJET_STUDS

    Nom du champ                format              description

    id_sondage (clé primaire)   alpha-numérique     numéro du sondage aléatoire
    sujet                       text                tous les sujets du sondage

USER_STUDS

    Nom du champ                format              description

    user                        text                nom du participant
    id_sondage (clé primaire)   alpha-numérique     numéro du sondage aléatoire
    reponses                    text                reponses a chacun des sujets proposés au vote (0 pour non, 1 pour OK)
    id_users                    alpha-numérique     numéro d'utilisateur par ordre croissant de participation pour garder l'ordre de participation

COMMENTS

    Nom du champ                format              description

    id_sondage (clé primaire)   alpha-numérique     numéro du sondage aléatoire
    comment                     text                commentaires d'un participant
    usercomment                 text                nom de l'utilisateur qui laisse le commentaire
    id_comment                  alpha-numérique     numéro de commentaire par ordre croissant de participation pour garder l'ordre de remplissage


##Traductions

Pour pouvoir bénéficier de toutes les traductions en FR, EN, DE et ES
il faut avoir installé les locales fr_FR, de_DE, en_US et es_ES sur le
serveur qui héberge l'application ainsi que disposer de l'extension PHP Gettext.

##Synthèses des librairies utilisées

[ADOdb](http://sourceforge.net/projects/adodb), 
paquet: php5-adodb

[gettext](https://launchpad.net/php-gettext),
paquet: php-gettext

Sous GNU/Linux,  
disposer des locales utf-8 suivantes pour la glibc:  
FR, EN, ES, DE (/etc/locales.gen)
