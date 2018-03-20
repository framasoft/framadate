<?php
namespace Framadate\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Throwable;

abstract class FramaWebTestCase extends WebTestCase {

    /**
     * @var Client
     */
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    /**
     * {@inheritdoc}
     */
    protected function onNotSuccessfulTest(Throwable $exception)
    {
        $message = null;
        if ($this->client->getCrawler()) {
            $message = preg_replace('#\s{2,}#', '', $this->client->getCrawler()->filter('.exception-message')->text());
        }
        if ($message) {
            $exceptionClass = get_class($exception);
            throw new $exceptionClass($exception->getMessage() . ' | ' . $message);
        }
        throw $exception;
    }
}
