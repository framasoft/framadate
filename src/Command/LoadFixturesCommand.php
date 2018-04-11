<?php

namespace Framadate\Command;

use Doctrine\DBAL\DBALException;
use Framadate\Fixture\PollFixture;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class LoadFixturesCommand extends Command
{
    /**
     * @var PollFixture
     */
    private $pollFixture;

    public function __construct(PollFixture $pollFixture)
    {
        $this->pollFixture = $pollFixture;
        parent::__construct();
    }


    protected function configure()
    {
        $this
            ->setName('framadate:tests:fixtures')
            ->setDescription('Load test fixtures')
            ->setHelp('This command loads test data inside the testing database. Do not use in production !');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $this->pollFixture->load();
            $io->success("Poll fixture has been loaded");
        } catch (DBALException $e) {
            $io->error($e->getMessage());
        }
    }
}
