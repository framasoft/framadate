<?php
namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Framadate\Utils;

class Version20151205000000 extends AbstractMigration
{
    /**
     * This method should describe in english what is the purpose of the migration class.
     *
     * @return string The description of the migration class
     */
    public function description()
    {
        return 'Increase the size of id column in poll table';
    }

    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema)
    {
        $commentTable = $schema->getTable(Utils::table('comment'));

        $commentTable->changeColumn('poll_id', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true, 'customSchemaOptions' => ['collation' => 'utf8_general_ci']]);

        $pollTable = $schema->getTable(Utils::table('poll'));

        $pollTable->changeColumn('id', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true, 'customSchemaOptions' => ['collation' => 'utf8_general_ci']]);

        $slotTable = $schema->getTable(Utils::table('slot'));

        $slotTable->changeColumn('poll_id', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true, 'customSchemaOptions' => ['collation' => 'utf8_general_ci']]);

        $voteTable = $schema->getTable(Utils::table('vote'));

        $voteTable->changeColumn('poll_id', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true, 'customSchemaOptions' => ['collation' => 'utf8_general_ci']]);
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}
