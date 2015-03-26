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

**_Avertissement_**

Les instructions contenues ci-dessous dans ce fichier ne sont pas actualisées.

Vous trouverez là : **[(FR) le tutoriel d'installation complet](http://framacloud.org/cultiver-son-jardin/installation-de-framadate/)**

* * *

# Paramètres

Le fichier `app/inc/config.template.php` contient le paramétrage par défaut de
l'application Framadate. Pour personnaliser votre installation, copiez
ce fichier sous le nom `app/inc/config.php` et modifiez ce dernier.

## Base de données

Framadate fonctionne indépendemment de la base SQL utilisée.

Cependant la base de donnée doit être créée au préalable,
après avoir renseigné les paramètres de la base de données, créez la.

### PostgreSQL

```bash
    su - pgsql
    createdb framadate
```

Attention : Si vous créez la base de données avec l'utilisateur "pgsql",
il vous faudra faire un "grant all on <chaque table> to `framadate`" pour donner les droits à l'utilisateur `framadate` de lire et modifier la base.
Les tables de l'applications sont décrites plus loin dans ce fichier dans la partie "Tables de la base de données".

### MySQL

TODO

### Création des tables

Pour lancer la création des tables, rendez-vous sur la page `admin/` puis allez dans la partie `Migration`.
Cette page est charger:
* soit de créer les tables si c'est votre première installation;
* soit de mettre à jour l'application si vous avec installé une nouvelle version.

# Accès à la page administrateur

Le répertoire `admin/` fournit l'accès à certainnes actions et informations à protéger.
Il convient de mettre en place un fichier `.htaccess`, pour restreindre l'accès à la page d'administration de l'application.
Modifiez le contenu de ce fichier `.htaccess` pour l'adapter au chemin du fichier `.htpasswd` sur votre serveur.
Le fichier `.htpasswd` peut être créé, par exemple, via la commande suivante :
`htpasswd -mnb <admin_username> <admin_password>`

Un fichier `admin/stdout.log` doit être créé et accessible en écriture
par votre serveur Web. Quelque chose comme :

```bash
    touch admin/stdout.log
    chmod 700 admin/stdout.log
    chown www-data admin/stdout.log
```

devrait convenir.

# Maintenance

Framadate dispose d'une possibilité de mise en maintenance par le biais d'un fichier `.htaccess`.

La section `<Directory>` relative à Framadate, dans la configuration d'Apache doit au moins contenir :
`AllowOverride AuthConfig Options`

Le fichier `.htaccess` correspondant doit être modifier pour y configurer
l'adresse IP depuis laquelle s'effectue la maintenance.
N'oubliez pas de le recommenter en intégralité une fois la maintenance effectuée.

# Tables de la base de données

Voici la structure des tables de l'application, le nom des tables est donné sans préfixe.

La base se compose de quatre tables :

- `poll` : Le paramètrage des sondages;
- `slot` : les choix disponibles pour chaque sondage;
- `vote` : les votes effectués par les utilisateurs pour chaque sondage;
- `comment` : les commentaires apportés à chaque sondage.

# Traductions

Les traductions se trouvent dans le dossier `locale`. Chaque langue est dans un fichier JSON différent organisé par section.

# Synthèses des librairies utilisées

[Smarty](http://www.smarty.net/),
gestion des templates pour PHP

[o80-i18n](https://github.com/olivierperez/o80-i18n),
système d'internationalisation
