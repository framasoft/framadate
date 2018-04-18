<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Rapha�l DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est r�gi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Rapha�l DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Framadate\AbstractMigration;
use Framadate\Utils;

/**
 * This migration RPad votes from version 0.8.
 * Because some votes does not have enough values for their poll.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class Version20150918000000 extends AbstractMigration
{
    public function description()
    {
        return 'RPad votes from version 0.8.';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\SkipMigrationException
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            $this->legacyCheck($schema, 'Framadate\Migration\RPadVotes_from_0_8'),
            'Migration has been executed in an earlier database migration system'
        );
        foreach ([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table(
            'comment'
        )] as $table) {
            $this->skipIf(!$schema->hasTable($table), 'Missing table ' . $table);
        }

        $driver_name = $this->connection->getDatabasePlatform()->getName();
        switch ($driver_name) {
            case 'mysql':
                $this->addSql(
                    'UPDATE ' . Utils::table('vote') . ' fv
INNER JOIN (
	SELECT v.id, RPAD(v.choices, inn.slots_count, \'0\') new_choices
	FROM ' . Utils::table('vote') . ' v
	INNER JOIN
		(SELECT s.poll_id, SUM(IFNULL(LENGTH(s.moments) - LENGTH(REPLACE(s.moments, \',\', \'\')) + 1, 1)) slots_count
		FROM ' . Utils::table('slot') . ' s
		GROUP BY s.poll_id
		ORDER BY s.poll_id) inn ON inn.poll_id = v.poll_id
	WHERE LENGTH(v.choices) != inn.slots_count
) computed ON fv.id = computed.id
SET fv.choices = computed.new_choices'
                );
                break;
            case 'postgresql':
                $this->addSql(
                    "UPDATE " . Utils::table('vote') . " fv
                    SET choices = computed.new_choices
FROM (
	SELECT v.id, RPAD(v.choices::text, inn.slots_count::int, '0') new_choices
	FROM " . Utils::table('vote') . " v
	INNER JOIN
		(SELECT s.poll_id, SUM(coalesce(LENGTH(s.moments) - LENGTH(REPLACE(s.moments, ',', '')) + 1, 1)) slots_count
		FROM " . Utils::table('slot') . " s
		GROUP BY s.poll_id
		ORDER BY s.poll_id) inn ON inn.poll_id = v.poll_id
	WHERE LENGTH(v.choices) != inn.slots_count
) computed WHERE fv.id = computed.id"
                );
                break;
            default:
                $this->skipIf(true, "Not on MySQL or PostgreSQL");
                break;
        }
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
