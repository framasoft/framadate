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

    public function emailPollFinderProvider()
    {
        return [
            ['admin@admin.tld', false],
            ['creator@frama.tld', true],
        ];
    }

    /**
     * @dataProvider emailPollFinderProvider
     *
     * @param $email
     * @param $res
     */
    public function testPollFinderAction($email, $res)
    {
        $crawler = $this->client->request('GET', '/find_poll');

        $this->assertTrue($this->client->getResponse()->isOk());

        $form = $crawler->filter('button[id=finder_submit]')->form();

        $data = [
            'finder[adminMail]' => $email,
        ];

        $this->client->enableProfiler();

        $crawler = $this->client->submit($form, $data);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        if ($res) {
            $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(1, $mailCollector->getMessageCount());
            $this->assertContains('FindPolls.Polls sent', $crawler->filter('body')->extract(['_text'])[0]);
        } else {
            $this->assertContains('Error.No polls found', $crawler->filter('body')->extract(['_text'])[0]);
        }
    }
}
