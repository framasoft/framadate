<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Migration;

use Framadate\Utils;

/**
 * Class From_0_0_to_0_8_Migration
 *
 * @package Framadate\Migration
 * @version 0.8
 */
class From_0_0_to_0_8_Migration implements Migration {

    function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    function description() {
        return 'First installation of the Framadate application (v0.8)';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    function preCondition(\PDO $pdo) {
	//issue187 : pouvoir installer framadate dans une base contenant d'autres tables.
        $query_show_tables = null ;
	switch(DB_DRIVER_NAME){
	   case 'mysql':
                $query_show_tables = 'SHOW TABLES like \'' . TABLENAME_PREFIX . '%\'';
		break;
           case 'pgsql':
                $query_show_tables = 'SELECT tablename FROM pg_tables WHERE tablename !~ \'^pg_\' AND tablename !~ \'^sql_\' AND tablename like \'' . TABLENAME_PREFIX . '%\''; 
		break; 
        }
        $stmt = $pdo->query($query_show_tables);  //issue187 : pouvoir installer framadate dans une base contenant d'autres tables.
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Check if there is no tables but the MIGRATION_TABLE one
        $diff = array_diff($tables, [Utils::table(MIGRATION_TABLE)]);
        return count($diff) === 0;
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    function execute(\PDO $pdo) {
        $create_tables_queries = null ;
	switch(DB_DRIVER_NAME){

	   case 'mysql':
                $query_create_table_sondage = '
CREATE TABLE IF NOT EXISTS `sondage` (
  `id_sondage` char(16) NOT NULL,
  `commentaires` text,
  `mail_admin` varchar(128) DEFAULT NULL,
  `nom_admin` varchar(64) DEFAULT NULL,
  `titre` text,
  `id_sondage_admin` char(24) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_fin` timestamp NOT NULL,
  `format` varchar(2) DEFAULT NULL,
  `mailsonde` tinyint(1) DEFAULT \'0\',
  `statut` int(11) NOT NULL DEFAULT \'1\' COMMENT \'1 = actif ; 0 = inactif ; \',
  UNIQUE KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

		$query_create_table_sujet_studs = '
CREATE TABLE IF NOT EXISTS `sujet_studs` (
  `id_sondage` char(16) NOT NULL,
  `sujet` text,
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

                $query_create_table_comments = '
CREATE TABLE IF NOT EXISTS `comments` (
  `id_comment` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_sondage` char(16) NOT NULL,
  `comment` text NOT NULL,
  `usercomment` text,
  PRIMARY KEY (`id_comment`),
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;';

                $query_create_table_user_studs = '
CREATE TABLE IF NOT EXISTS `user_studs` (
  `id_users` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(64) NOT NULL,
  `id_sondage` char(16) NOT NULL,
  `reponses` text NOT NULL,
  PRIMARY KEY (`id_users`),
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;';

		break;

           case 'pgsql':
                $query_create_table_sondage = '
CREATE TABLE IF NOT EXISTS sondage (
  id_sondage char(16) NOT NULL UNIQUE,
  commentaires text,
  mail_admin varchar(128) DEFAULT NULL,
  nom_admin varchar(64) DEFAULT NULL,
  titre text,
  id_sondage_admin char(24) DEFAULT NULL,
  date_creation timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  date_fin timestamp NOT NULL DEFAULT \'epoch\',
  format varchar(2) DEFAULT NULL,
  mailsonde smallint DEFAULT \'0\',
  statut smallint NOT NULL DEFAULT \'1\' CHECK(statut in (0,1))
);
COMMENT ON COLUMN sondage.statut IS \'1 = actif; 0 = inactif\' ;';
		$query_create_table_sujet_studs = '
CREATE TABLE IF NOT EXISTS sujet_studs (
  id_sondage char(16) NOT NULL,
  sujet text
);
CREATE INDEX id_sondage_index ON sujet_studs (id_sondage);';

                $query_create_table_comments = '
CREATE TABLE IF NOT EXISTS comments (
  id_comment BIGSERIAL NOT NULL PRIMARY KEY,
  id_sondage char(16) NOT NULL,
  comment text NOT NULL,
  usercomment text
);
CREATE INDEX id_sondage_comments_index ON comments (id_sondage);';

                $query_create_table_user_studs = '
CREATE TABLE IF NOT EXISTS user_studs (
  id_users BIGSERIAL NOT NULL PRIMARY KEY,
  nom varchar(64) NOT NULL,
  id_sondage char(16) NOT NULL,
  reponses text NOT NULL
);
CREATE INDEX id_sondage_user_studs_index ON user_studs (id_sondage);';

		break;
        }

	$create_tables_queries = [
		$query_create_table_sondage,
		$query_create_table_sujet_studs,
		$query_create_table_comments,
		$query_create_table_user_studs
		];

	foreach ( $create_tables_queries as $query ){
           $pdo->exec($query);
	}
    }
}
