<?php

namespace Framadate\Tests\Controllers;

use Framadate\Tests\FramaWebTestCase;

class PollControllerTest extends FramaWebTestCase
{
    public function testHomePage()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h2:contains("Generic.Make your polls")'));

        $this->assertCount(3, $crawler->filter('p.home-choice'));
    }

    public function testDatePollForm()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $datePollButton = $crawler->filter('p.home-choice a')->first()->link();
        $crawler = $client->click($datePollButton);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Step 1.You are in the poll creation section.")'));

    }

    public function testSubmitDatePollForm()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/new/date');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Step 1.You are in the poll creation section.")'));

        $form = $crawler->filter('button[id=poll_submit]')->form();

        $data = [
            'poll[admin_name]' => 'admin',
            'poll[title]' => 'my poll',
            'poll[description]' => 'my awesome poll'
        ];

        $client->submit($form, $data);

        $this->assertSame(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertContains('Step 2.Title', $crawler->filter('body')->extract(['_text'])[0]);

        $form = $crawler->filter('button[name=choixheures]')->form();

        $data = [
            'days' => ['2019-02-17', '2019-02-18'],
            'horaires0' => ['12h', '13h'],
        ];

        $client->submit($form, $data);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testClassicPollForm()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $datePollButton = $crawler->filter('p.home-choice a')->eq(1)->link();
        $crawler = $client->click($datePollButton);

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Step 1.You are in the poll creation section.")'));

    }
}
