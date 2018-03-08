<?php

namespace Tests\Framadate\Controllers;

use Tests\Framadate\FramaWebTestCase;

class PollControllerTest extends FramaWebTestCase
{
    public function testHomePage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h2:contains("Organiser des rendez-vous simplement, librement.")'));

        $this->assertCount(3, $crawler->filter('p.home-choice'));
    }

    public function testDatePollForm()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/new/date');

        /*$this->assertTrue($client->getResponse()->isOk());
        $datePollButton = $crawler->filter('p.home-choice a')[0];
        $crawler = $client->click($datePollButton);*/

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Vous avez choisi de créer un nouveau sondage.")'));

    }
}
