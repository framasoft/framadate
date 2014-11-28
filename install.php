<?php

$configuration_file = __DIR__.'/app/inc/constants.php';

if (file_exists($configuration_file) === true) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['install']) === true) {
    ini_set('max_execution_time', 0);
    ob_start();

    // Composer exist ?
    $locations = array(
        __DIR__.'/composer.phar',
        //'/usr/bin/composer',
        '/usr/bin/composer.phar',
        //'/usr/local/bin/composer',
        '/usr/local/bin/composer.phar',
    );
    $composer = null;
    foreach ($locations as $location) {
        if (file_exists($location) === true) {
            $composer = $location;
            break;
        }
    }

    // If composer not found, download it !
    if (null === $composer) {
        if (!file_put_contents(__DIR__.'/composer.phar', file_get_contents('https://getcomposer.org/composer.phar'))) {
            die('Installation impossible : impossible to find composer !');
        }
        $composer = __DIR__.'/composer.phar';
    }

    try {
        echo "Utilisation de ".$composer.'<br />';
        ob_flush();
        flush();

        require_once 'phar://'.$composer.'/src/bootstrap.php';
        ob_flush();
        flush();

        $composer_home = getenv('COMPOSER_HOME');
        $personal_home = getenv('HOME');
        if (empty($composer_home) === true && empty($personal_home) === true) {
            putenv('COMPOSER_HOME='.sys_get_temp_dir());
        }

        $application = new \Composer\Console\Application();
        $application->setAutoExit(false);
        $command = $application->find('install');
        $input = new \Symfony\Component\Console\Input\ArrayInput(array(
            'command' => 'install',
            '-d' => __DIR__,
            '-vvv',
            '--optimize-autoloader',
        ));
        $fhandle = fopen('php://output', 'wb');
        $output = new \Symfony\Component\Console\Output\StreamOutput($fhandle);

        $application->run($input, $output);
        fclose($fhandle);
        ob_flush();
        flush();

        // Save configuration
        $configuration = file_get_contents($configuration_file.'.template');
        if (false === $configuration) {
            throw new \Exception('Impossible to read template configuration');
        }

        $configuration = str_replace(
            array(
                '\'<Application name>\'',
                '\'<email address>\'',
                '\'<no-reply@mydomain.com>\'',
                '\'<database name>\'',
                '\'<database user>\'',
                '\'<database password>\'',
                '\'<database server>\'',
                '\'<database type>\'',
            ),
            array(
                var_export($_POST['title'], true),
                var_export($_POST['email'], true),
                var_export($_POST['no-reply-email'], true),
                var_export($_POST['db-name'], true),
                var_export($_POST['db-user'], true),
                var_export($_POST['db-pass'], true),
                var_export($_POST['db-host'], true),
                var_export($_POST['db-type'], true),
            ),
            $configuration
        );

        if (file_put_contents($configuration_file, $configuration) === false) {
            throw new \Exception('Impossible to save configuration');
        }

        // Inject database
        require_once __DIR__.'/app/inc/init.php';

        $sqls = explode("\n", file_get_contents(__DIR__.'/install.mysql.auto.sql'));
        foreach ($sqls as $sql) {
            $sql = trim($sql);
            if (empty($sql) === true) {
                continue;
            }

            $query = $connect->Prepare($sql);
            $cleaning = $connect->Execute($query);
        }

        ob_flush();
        flush();
        ob_end_clean();
    } catch (Exception $e) {
        echo '<br /><b>'.$e->getMessage().'</b><br />';
        echo "<pre>".$e->getTraceAsString()."</pre>";
        die('installation failed');
    }
}
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>OpenSondage Installation</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container">
            <h1>OpenSondage Installation</h1>
            <form action="" method="post" role="form">
                <fieldset>
                    <legend>General</legend>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Application name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Administrator email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email of the administrator" required>
                    </div>
                    <div class="form-group">
                        <label for="no-reply-email">No-reply email</label>
                        <input type="email" class="form-control" id="no-reply-email" name="no-reply-email" placeholder="Email for automatic responses" required>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Database</legend>

                    <div class="form-group">
                        <label for="db-type">Type</label>
                        <select name="db-type" id="db-type" required>
                            <option value="pdo">PDO - MySQL</option>
                            <option value="mysql">MySQL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="db-host">Host</label>
                        <input type="text" class="form-control" id="db-host" name="db-host" value="localhost" required>
                    </div>
                    <div class="form-group">
                        <label for="db-name">Database name</label>
                        <input type="text" class="form-control" id="db-name" name="db-name" value="opensondage" required>
                    </div>
                    <div class="form-group">
                        <label for="db-user">Username</label>
                        <input type="text" class="form-control" id="db-user" name="db-user" value="root" required>
                    </div>
                    <div class="form-group">
                        <label for="db-pass">Password</label>
                        <input type="password" class="form-control" id="db-pass" name="db-pass" value="">
                    </div>
                </fieldset>

                <input type="submit" class="btn btn-default" name="install" value="Install">
            </form>
        </div>
    </body>
</html>

