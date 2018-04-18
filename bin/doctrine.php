#!/usr/bin/env php
<?php

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

try {
    require_once __DIR__ . '/../app/inc/init.php';

    $input = new ArgvInput();
    $output = new ConsoleOutput();
    $style = new SymfonyStyle($input, $output);

    if ($connect === null) {
        throw new \Exception("Undefined database connection\n");
    }

    // replace the ConsoleRunner::run() statement with:
    $cli = new Application('Doctrine Command Line Interface', VERSION);
    $cli->setCatchExceptions(true);

    $helperSet = new HelperSet(
        [
            'db' => new ConnectionHelper($connect),
            'question' => new QuestionHelper(),
        ]
    );

    $cli->setHelperSet($helperSet);

    $migrateCommand = new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand();
    $statusCommand = new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand();


    $migrationsDirectory = __DIR__ . '/../app/classes/Framadate/Migrations';

    $configuration = new Configuration($connect);
    $configuration->setMigrationsTableName(MIGRATION_TABLE . '_new');
    $configuration->setMigrationsDirectory($migrationsDirectory);
    $configuration->setMigrationsNamespace('DoctrineMigrations');
    $configuration->registerMigrationsFromDirectory($migrationsDirectory);
    $migrateCommand->setMigrationConfiguration($configuration);
    $statusCommand->setMigrationConfiguration($configuration);

    // Register All Doctrine Commands
    $cli->addCommands([$migrateCommand, $statusCommand]);

    // Runs console application
    $cli->run($input, $output);

} catch (\Exception $e) {
    var_dump($e);
    $style->error($e->getMessage());
}
