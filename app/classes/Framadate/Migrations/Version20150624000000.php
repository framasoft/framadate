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
namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Framadate\AbstractMigration;
use Framadate\Security\Token;
use Framadate\Utils;

/**
 * This migration generate uniqId for all legacy votes.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class Version20150624000000 extends AbstractMigration
{
    public function description()
    {
        return 'Generate "uniqId" in "vote" table for all legacy votes';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     */
    public function up(Schema $schema)
    {
        $this->skipIf($this->legacyCheck($schema, 'Framadate\Migration\Generate_uniqId_for_old_votes'), 'Migration has been executed in an earlier database migration system');
        foreach ([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table('comment')] as $table) {
            $this->skipIf(!$schema->hasTable($table), 'Missing table ' . $table);
        }

        $this->connection->beginTransaction();

        $select = $this->connection->query('
SELECT id
  FROM ' . Utils::table('vote') . '
 WHERE uniqid = \'\'');

        $update = $this->connection->prepare('
UPDATE ' . Utils::table('vote') . '
   SET uniqid = :uniqid
 WHERE id = :id');

        while ($row = $select->fetch(\PDO::FETCH_OBJ)) {
            $token = Token::getToken(16);
            $update->execute([
                                 'uniqid' => $token,
                                 'id' => $row->id
                             ]);
        }

        $this->connection->commit();
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
