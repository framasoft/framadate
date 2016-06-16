## Pour utiliser l’environement de dev Docker

```bash
git clone https://framagit.org/framasoft/framadate.git
cp app/inc/config.docker_mariadb.php app/inc/config.php
mkdir tpl_c && chmod 777 tpl_c
touch admin/stdout.log && chmod 777 admin/stdout.log
docker-compose up -d
docker-compose run app composer install
```

Pour terminer l’installation : http://localhost:8000/admin/migration.php
