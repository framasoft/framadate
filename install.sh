#!/bin/bash
# Indique au système que l'argument qui suit est le programme utilisé pour exécuter ce fichier
# En règle générale, les "#" servent à mettre en commentaire le texte qui suit comme ici

chmod -R 777 ./

composer install

mkdir ./tpl_c

chmod -R 777 ./

xdg-open 'http://'$HOSTNAME'/framadate/admin/install.php'

    touch admin/stdout.log
    chmod 700 admin/stdout.log
    chown www-data admin/stdout.log

exit 0


