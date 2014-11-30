<?php

require_once __DIR__.'/InstallComposer.php';
require_once __DIR__.'/InstallConfiguration.php';
require_once __DIR__.'/InstallSql.php';

$configuration_file = __DIR__.'/../app/inc/constants.php';

if (file_exists($configuration_file) === true) {
    header('Location: ../index.php');
    exit;
}

if (isset($_POST['install']) === true) {
    try {
        // Composer installation
        $composer = new InstallComposer();
        if ($composer->check() === false) {
            ini_set('max_execution_time', 0);
            $composer->install();
        }

        // Save configuration
        $configuration = new InstallConfiguration($_POST);
        if ($configuration->checkValues() === false) {
            throw new \Exception('Bad value for configuration');
        }

        $configuration->copy($configuration_file.'.template', $configuration_file);

        // Inject database
        $sql = new InstallSql();
        $sql->inject();

        header('Location: ../index.php');
        die();
    } catch (Exception $e) {
        require_once __DIR__.'/error.html';
        die();
    }
}

require_once __DIR__.'/install.html';
