#!/bin/bash

# Read environment variables or set default values
FRAMADATE_CONFIG=${FRAMADATE_CONFIG:-/var/www/framadate/app/inc/config.php}
DOMAIN=${DOMAIN-localhost}
FORCE_HTTPS=${FORCE_HTTPS-false}
APP_NAME=${APP_NAME-Framadate}
ADMIN_MAIL=${ADMIN_MAIL-}
NO_REPLY_MAIL=${NO_REPLY_MAIL-}
MYSQL_USER=${MYSQL_USER-user}
MYSQL_PASSWORD=${MYSQL_PASSWORD-password}
MYSQL_DB=${MYSQL_DB-framadate}
MYSQL_HOST=${MYSQL_HOST-mysql}
MYSQL_PORT=${MYSQL_PORT-3306}
DISABLE_SMTP=${DISABLE_SMTP-false}

# Add configuration file if not exist
if [ ! -f $FRAMADATE_CONFIG ]; then
  echo "There is no configuration file. Create one with environment variables"
  cp /var/www/framadate/tpl/admin/config.tpl $FRAMADATE_CONFIG
  # Set values on configuration file
  sed -i -E "s/^(\/\/ )?const APP_URL( )?=.*;/const APP_URL = '$DOMAIN';/g" $FRAMADATE_CONFIG
  if [ "$FORCE_HTTPS" = true ]; then
    sed -i -E "s/^(\/\/ )?const FORCE_HTTPS\\s*=.*;/const FORCE_HTTPS = true;/" $FRAMADATE_CONFIG
  fi
  sed -i -E "s/^(\/\/ )?const NOMAPPLICATION( )?=.*;/const NOMAPPLICATION = '$APP_NAME';/g" $FRAMADATE_CONFIG
  # Configure mail
  sed -i -E "s/^(\/\/ )?const ADRESSEMAILADMIN( )?=.*;/const ADRESSEMAILADMIN = '$ADMIN_MAIL';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const ADRESSEMAILREPONSEAUTO( )?=.*;/const ADRESSEMAILREPONSEAUTO = '$NO_REPLY_MAIL';/g" $FRAMADATE_CONFIG
  # Database configuration
  sed -i -E "s/^(\/\/ )?const DB_USER( )?=.*;/const DB_USER = '$MYSQL_USER';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const DB_PASSWORD( )?=.*;/const DB_PASSWORD = '$MYSQL_PASSWORD';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const DB_DRIVER( )?=.*;/const DB_DRIVER = 'pdo_mysql';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const DB_NAME( )?=.*;/const DB_NAME = '$MYSQL_DB';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const DB_HOST( )?=.*;/const DB_HOST = '$MYSQL_HOST';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const DB_PORT( )?=.*;/const DB_PORT = '$MYSQL_PORT';/g" $FRAMADATE_CONFIG
  # SMTP config
  if [ "$DISABLE_SMTP" = "true" ]; then
    sed -i -E "s/'use_smtp' => true,/'use_smtp' => false,/g" $FRAMADATE_CONFIG
  fi
  sed -i -E "s/SMTP_SERVER/${SMTP_SERVER:-localhost}/g" $FRAMADATE_CONFIG
  # Framadate internal config
  sed -i -E "s/^(\/\/ )?const TABLENAME_PREFIX( )?=.*;/const TABLENAME_PREFIX = 'fd_';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const MIGRATION_TABLE( )?=.*;/const MIGRATION_TABLE = 'framadate_migration';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const DEFAULT_LANGUAGE( )?=.*;/const DEFAULT_LANGUAGE = 'fr';/g" $FRAMADATE_CONFIG
  sed -i -E "s/^(\/\/ )?const URL_PROPRE( )?=.*;/const URL_PROPRE = true;/g" $FRAMADATE_CONFIG
else
  echo "Using existing config file " $FRAMADATE_CONFIG
fi

# Configure /admin basic auth
if [ ! -f /var/www/framadate/admin/.htpasswd ]; then
  if [ "$ADMIN_USER" ] && [ "$ADMIN_PASSWORD" ]; then
    htpasswd -bc /var/www/framadate/admin/.htpasswd $ADMIN_USER $ADMIN_PASSWORD
  else
    echo "!!! You need to configure ADMIN_USER and ADMIN_PASSWORD environment variables !!!"
    exit 1
  fi
fi

if [ "$ENV" = "dev" ]; then
  echo Installing PHP development dependencies
  composer install --no-interaction --no-progress
else
  echo Installing PHP production dependencies
  composer install -o  --no-interaction --no-progress --prefer-dist --no-dev
  composer dump-autoload --optimize --no-dev --classmap-authoritative
fi

# Await MySQL Container being ready
until /usr/bin/mysql --host=$MYSQL_HOST --user=$MYSQL_USER --password=$MYSQL_PASSWORD --silent --execute "SELECT 1;" $MYSQL_DB; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 1
done

>&2 echo "Resuming setup"

echo "Setting up .htaccess"
cp /var/www/framadate/htaccess.txt /var/www/framadate/.htaccess

# Run Database migrations
echo "Running database migrations"
php /var/www/framadate/bin/doctrine migrations:status --no-interaction -vvv
php /var/www/framadate/bin/doctrine migrations:migrate --no-interaction -vvv

# Run apache server
# chown -R www-data:www-data /var/www/framadate
source /etc/apache2/envvars
exec apache2 -D FOREGROUND
