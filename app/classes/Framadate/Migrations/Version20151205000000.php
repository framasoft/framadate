<?php
namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\Type;
use Doctrine\Migrations\Exception\SkipMigration;
use Framadate\AbstractMigration;
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
     * @throws SchemaException
     * @throws DBALException
     * @throws SkipMigration
     */
    public function up(Schema $schema): void
    {
        $this->skipIf($this->legacyCheck($schema, 'Framadate\Migration\Increase_pollId_size'), 'Migration has been executed in an earlier database migration system');
        $commentTable = $schema->getTable(Utils::table('comment'));

        $commentTable->changeColumn('poll_id', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true]);

        $pollTable = $schema->getTable(Utils::table('poll'));

        $pollTable->changeColumn('id', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true]);

        $slotTable = $schema->getTable(Utils::table('slot'));

        $slotTable->changeColumn('poll_id', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true]);

        $voteTable = $schema->getTable(Utils::table('vote'));

        $voteTable->changeColumn('poll_id', ['type' => Type::getType('string'), 'length' => 64, 'notnull' => true]);
    }

    public function down(Schema $schema): void
    {
        // TODO: Implement down() method.
    }
}
