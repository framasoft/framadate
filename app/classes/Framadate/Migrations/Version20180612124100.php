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
use Framadate\Utils;

/**
 * This migration adds the columns closed, admin_choice and admin_choice_exp in the poll table
 *
 * @package Framadate\Migration
 * @version 1.2
 */
class Version20180612124100 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Adds the columns closed, admin_choice and admin_choice_exp in the poll table';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        $poll = $schema->getTable(Utils::table('poll'));
        $poll->addColumn('closed', 'boolean', ['default' => false]);
        $poll->addColumn('admin_choice', 'string');
        $poll->addColumn('admin_choice_exp', 'string');
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function down(Schema $schema)
    {
        $poll = $schema->getTable(Utils::table('poll'));
        $poll->dropColumn('closed');
        $poll->dropColumn('admin_choice');
        $poll->dropColumn('admin_choice_exp');
    }
}
