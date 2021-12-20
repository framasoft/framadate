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
use PDO;

/**
 * Class From_0_0_to_0_8_Migration
 *
 * @package Framadate\Migration
 * @version 0.8
 */
class From_0_0_to_0_8_Migration implements Migration {
    public function __construct() {
    }

    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description(): string {
        return 'First installation of the Framadate application (v0.8)';
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    public function preCondition(PDO $pdo): bool {
        $stmt = $pdo->query('SHOW TABLES like \'' . TABLENAME_PREFIX . '%\'');  //issue187 : pouvoir installer framadate dans une base contenant d'autres tables.
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Check if there is no tables but the MIGRATION_TABLE one
        $diff = array_diff($tables, [Utils::table(MIGRATION_TABLE)]);
        return count($diff) === 0;
    }

    /**
     * This method is called only one time in the migration page.
     *
     * @param PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    public function execute(PDO $pdo): bool {
        $pdo->exec('
CREATE TABLE IF NOT EXISTS `sondage` (
  `id_sondage` char(16) NOT NULL,
  `commentaires` text,
  `mail_admin` varchar(128) DEFAULT NULL,
  `nom_admin` varchar(64) DEFAULT NULL,
  `titre` text,
  `id_sondage_admin` char(24) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_fin` timestamp NULL DEFAULT NULL,
  `format` varchar(2) DEFAULT NULL,
  `mailsonde` tinyint(1) DEFAULT \'0\',
  `statut` int(11) NOT NULL DEFAULT \'1\' COMMENT \'1 = actif ; 0 = inactif ; \',
  UNIQUE KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $pdo->exec('
CREATE TABLE IF NOT EXISTS `sujet_studs` (
  `id_sondage` char(16) NOT NULL,
  `sujet` text,
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;');

        $pdo->exec('
CREATE TABLE IF NOT EXISTS `comments` (
  `id_comment` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_sondage` char(16) NOT NULL,
  `comment` text NOT NULL,
  `usercomment` text,
  PRIMARY KEY (`id_comment`),
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;');

        $pdo->exec('
CREATE TABLE IF NOT EXISTS `user_studs` (
  `id_users` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(64) NOT NULL,
  `id_sondage` char(16) NOT NULL,
  `reponses` text NOT NULL,
  PRIMARY KEY (`id_users`),
  KEY `id_sondage` (`id_sondage`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;');
        return true;
    }
}
