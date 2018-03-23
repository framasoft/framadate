<?php

namespace Framadate\Tests\Controllers;

use Framadate\Tests\FramaWebTestCase;

class DatePollControllerTest extends FramaWebTestCase
{
    public function testSubmitDatePollForm()
    {
        $crawler = $this->client->request('GET', '/p/new/date');

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

        $crawler = $this->client->submit($form, $data);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('Step 3.List of your choices', $crawler->filter('body')->extract(['_text'])[0]);

        $form = $crawler->filter('button[id=archive_submit]')->form();

        $data = [
            'archive[end_date]' => (new \DateTime('now'))->modify('+3 month')->format('Y-m-d'),
        ];

        $this->client->submit($form, $data);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('PollInfo.Admin link of the poll', $crawler->filter('body')->extract(['_text'])[0]);
    }
}
