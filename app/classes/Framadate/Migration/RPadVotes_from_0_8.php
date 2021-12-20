<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphael DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphael DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Migration;

use Framadate\Utils;

/**
 * This migration RPad votes from version 0.8.
 * Because some votes does not have enough values for their poll.
 *
 * @package Framadate\Migration
 * @version 0.9
 */
class RPadVotes_from_0_8 implements Migration {
    public function description(): string {
        return 'RPad votes from version 0.8.';
    }

    public function preCondition(\PDO $pdo): bool {
        $stmt = $pdo->query('SHOW TABLES');
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        // Check if tables of v0.9 are presents
        $diff = array_diff([Utils::table('poll'), Utils::table('slot'), Utils::table('vote'), Utils::table('comment')], $tables);
        return count($diff) === 0;
    }

    public function execute(\PDO $pdo): bool {
        $pdo->beginTransaction();
        $this->rpadVotes($pdo);
        $pdo->commit();

        return true;
    }

    private function rpadVotes(\PDO $pdo): void
    {
        $pdo->exec('UPDATE ' . Utils::table('vote') . ' fv
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
SET fv.choices = computed.new_choices');
    }
}
