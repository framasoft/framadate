<?php
namespace Tests\Framadate;

use Silex\WebTestCase;

abstract class FramaWebTestCase extends WebTestCase {
    public function createApplication()
    {
        require __DIR__ . '/../../index.php';

        $app['debug'] = true;
        unset($app['exception_handler']);
        $app['session.test'] = true;

        return $app;
    }
}
