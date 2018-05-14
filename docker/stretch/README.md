# Framadate
**Cette image est en phase de test. NE PAS UTILISER EN PRODUCTION**

Image Docker pour le déploiement de Framadate

## Configuration
### Base de données
Pour fonctionner, Framadate a besoin d'une base de données. Dans notre cas nous utilisons MySQL, que l'on déploie avec Docker. Afin que Framadate fonctionne correctement, nous devons désactiver le *SQL MODE* `NO_ZERO_DATE` de MySQL. On peut donc déployer une base de données pour Framadate ainsi (avec Docker Compose):
```
framadate-db:
  image: mysql:5.7
  container_name: framadate-db
  command: --sql-mode="ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"
  volumes:
    - /path/to/data/volume:/var/lib/mysql
  environment:
    - MYSQL_ROOT_PASSWORD=myrootpassword
    - MYSQL_USER=framadate
    - MYSQL_PASSWORD=myframadatepassword
    - MYSQL_DATABASE=framadate
  restart: always
```

### Framadate
Pour initialiser Framadate, on utilise plusieurs variables d'environnement :
- `DOMAIN`: sous domaine du serveur Framadate (ex: `framadate.picasoft.net`)
- `APP_NAME`: nom de l'application (`Framadate` par défaut)
- `ADMIN_MAIL`: adresse mail de l'administrateur du serveur
- `NO_REPLY_MAIL`: adresse mail qui servira à l'envoi des mails
- `MYSQL_USER`: utilisateur MySQL
- `MYSQL_PASSWORD`: mot de passe de l'utilisateur MySQL
- `MYSQL_DB`: nom de la base de données
- `MYSQL_HOST`: adresse du serveur de base de données
- `MYSQL_PORT`: port du serveur MySQL (`3306` par défaut)
- `ADMIN_USER`: utilisateur de l'interface d'administration
- `ADMIN_PASSWORD`: mot de passe de l'interface d'administration
- `DISABLE_SMTP`: mettre à `true` pour désactiver SMTP (sinon `false` par défaut)
