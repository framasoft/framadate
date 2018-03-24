<?php
namespace Framadate\Tests;

use Framadate\Entity\Poll;
use Framadate\Services\PollService;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
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
        if ($this->client->getCrawler() && count($this->client->getCrawler()->filter('.exception-message')) > 0) {
            $message = preg_replace('#\s{2,}#', '', $this->client->getCrawler()->filter('.exception-message')->text());
        }
        if ($message) {
            $exceptionClass = get_class($exception);
            throw new $exceptionClass($exception->getMessage() . ' | ' . $message);
        }
        throw $exception;
    }

    /**
     * @param string $id
     * @return Poll
     */
    protected function getPollById(string $id): Poll
    {
        return $this->client->getContainer()->get('framadate.poll_service.public')->findById($id);
    }
}
