<?php

namespace Framadate\Tests\Controllers;

use Framadate\Tests\FramaWebTestCase;

class PollControllerTest extends FramaWebTestCase
{
    public function testHomePage()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('h2:contains("Generic.Make your polls")'));

        $this->assertCount(3, $crawler->filter('p.home-choice'));
    }

    public function testDatePollForm()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertTrue($this->client->getResponse()->isOk());
        $datePollButton = $crawler->filter('p.home-choice a')->first()->link();
        $crawler = $this->client->click($datePollButton);

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Step 1.You are in the poll creation section.")'));

    }

    public function testSubmitDatePollForm()
    {
        $crawler = $this->client->request('GET', '/new/date');

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Step 1.You are in the poll creation section.")'));

        $form = $crawler->filter('button[id=poll_submit]')->form();

        $data = [
            'poll[admin_name]' => 'admin',
            'poll[title]' => 'my poll',
            'poll[description]' => 'my awesome poll'
        ];

        $this->client->submit($form, $data);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertContains('Step 2.Title', $crawler->filter('body')->extract(['_text'])[0]);

        $form = $crawler->filter('button[name=choixheures]')->form();

        $data = [
            'days' => ['2019-02-17', '2019-02-18'],
            'horaires0' => ['12h', '13h'],
        ];

        $this->client->submit($form, $data);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testClassicPollForm()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertTrue($this->client->getResponse()->isOk());
        $datePollButton = $crawler->filter('p.home-choice a')->eq(1)->link();
        $crawler = $this->client->click($datePollButton);

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Step 1.You are in the poll creation section.")'));

    }
}
