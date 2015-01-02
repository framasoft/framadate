<?php
namespace Framadate\Migration;

use Framadate\Utils;

class From_0_0_to_0_8_Migration implements Migration {

    function __construct() {
    }

    /**
     * This method could check if the execute method should be called.
     * It is called before the execute method.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the Migration should be executed.
     */
    function preCondition(\PDO $pdo) {
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Check if there is no tables but the MIGRATION_TABLE one
        $diff = array_diff($tables, [Utils::table(MIGRATION_TABLE)]);
        return count($diff) === 0;
    }

    /**
     * This methode is called only one time in the migration page.
     *
     * @param \PDO $pdo The connection to database
     * @return bool true is the execution succeeded
     */
    function execute(\PDO $pdo) {
        $pdo->exec('
CREATE TABLE IF NOT EXISTS `sondage` (
  `id_sondage` char(16) NOT NULL,
  `commentaires` text,
  `mail_admin` varchar(128) DEFAULT NULL,
  `nom_admin` varchar(64) DEFAULT NULL,
  `titre` text,
  `id_sondage_admin` char(24) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_fin` timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\',
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
    }
}
 