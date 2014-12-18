<?php

class InstallComposer
{
    /**
     * @var string
     */
    private $composer;

    /**
     * @return bool
     */
    public function check()
    {
        return file_exists(dirname(__DIR__).'/vendor/autoload.php');
    }

    public function install()
    {
        require_once 'phar://'.$this->getComposer().'/src/bootstrap.php';

        $this->initEnv();

        $application = new \Composer\Console\Application();
        $application->setAutoExit(false);

        $input = new \Symfony\Component\Console\Input\ArrayInput(array(
            'command'   => 'install',
            '-d'        => __DIR__.'/..',
            '-vvv',
            '--optimize-autoloader',
        ));
        $output = new \Symfony\Component\Console\Output\NullOutput();

        $application->run($input, $output);
    }

    /**
     * @return string
     */
    private function getComposer()
    {
        if (null === $this->composer) {
            $this->initComposer();
        }

        return $this->composer;
    }

    private function initComposer()
    {
        // Composer exist ?
        $locations = array(
            __DIR__.'/../composer.phar',
            '/usr/bin/composer.phar',
            '/usr/local/bin/composer.phar',
        );

        $this->composer = null;
        foreach ($locations as $location) {
            if (file_exists($location) === true) {
                $this->composer = $location;
                break;
            }
        }

        // If composer not found, download it !
        if (null === $this->composer) {
            if (!file_put_contents(
                __DIR__.'/../composer.phar',
                file_get_contents('https://getcomposer.org/composer.phar')
            )
            ) {
                throw new \Exception('Impossible to download composer');
            }

            $this->composer = __DIR__.'/../composer.phar';
        }
    }

    private function initEnv()
    {
        $composer_home = getenv('COMPOSER_HOME');
        $personal_home = getenv('HOME');
        if (empty($composer_home) === true && empty($personal_home) === true) {
            putenv('COMPOSER_HOME='.sys_get_temp_dir());
        }
    }

}
