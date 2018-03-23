<?php

namespace Framadate\Fixture;

use Framadate\Entity\DateSlot;
use Framadate\Entity\Poll;
use Framadate\Services\PollService;

class PollFixture implements FixtureInterface
{

    /**
     * @var PollService
     */
    private $pollService;

    /**
     * PollFixture constructor.
     * @param PollService $pollService
     */
    public function __construct(PollService $pollService)
    {
        $this->pollService = $pollService;
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function load()
    {
        $poll1 = new Poll();
        $poll1->setId('mypoll')
            ->setTitle('My Poll')
            ->setChoixSondage('date')
            ->setAdminName('creator')
            ->setAdminMail('creator@frama.tld')
            ->setFormat('d')
            ->setDescription('MyDescription')
            ->setEndDate((new \DateTime())->modify('+3 month'))
            ;
        $slot1 = new DateSlot();
        $slot1->setTitle(new \DateTime("2018/06/30"))
            ->setMoments('afternoon');

        $poll1->setChoices($slot1);

        $this->pollService->createPoll($poll1);
    }
}
