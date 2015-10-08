# Pré-installation

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

```sql
    -- Créer une base de données
    CREATE DATABASE IF NOT EXISTS `framadate_db` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

    -- Créer un utilisateur
    CREATE USER 'framadate_user'@'localhost' IDENTIFIED BY '<password>';
    GRANT ALL PRIVILEGES ON `framadate_db`.* TO 'framadate_user'@'localhost';
```

# Installation

Pour installer l'application Framadate, rendez-vous sur la page http://monsite/admin/install.php et remplisez le formulaire.

Une fois le formulaire rempli et envoyé, un script va générer le fichier `app/inc/config.php` puis vous rediriger vers la page de migration.

La page de migration s'occupe :
- D'installer toute la base de données (tables + données d'exemple)
- De mettre à jour la base de données lors des mises à jour de l'applciation.

! Attention, le chargement de la page de migration peu prendre du temps étant donné qu'elle applique toutes les mises à jours requises !

# Accès à la page administrateur

Le répertoire `admin/` fournit l'accès à certainnes actions et informations à protéger.

Il convient de mettre en place un couple de fichiers `.htaccess`/`.htpasswd`, pour restreindre l'accès à la page d'administration de l'application.
Il existe une multitude de tutoriels sur internet à ce sujet.

# Journal de l'application

Un fichier `admin/stdout.log` doit être créé et accessible en écriture
par votre serveur Web. Quelque chose comme devrait convenir:

```bash
    touch admin/stdout.log
    chmod 700 admin/stdout.log
    chown www-data admin/stdout.log
```

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