<?php

namespace Framadate\Tests\Controllers;

use Framadate\Tests\FramaWebTestCase;

class DatePollControllerTest extends FramaWebTestCase
{
    public function provideDataForSubmitDatePollForm()
    {
        return [
            ['admin@domain.tld'],
            [false]
        ];
    }


    /**
     * @dataProvider provideDataForSubmitDatePollForm
     *
     * @param $admin_email
     */
    public function testSubmitDatePollForm($admin_email)
    {
        $crawler = $this->client->request('GET', '/p/new/date');

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Step 1.You are in the poll creation section.")'));

        $form = $crawler->filter('button[id=poll_submit]')->form();

        $data = [
            'poll[admin_name]' => 'admin',
            'poll[title]' => 'my poll',
            'poll[description]' => 'my awesome poll',
            'poll[admin_mail]' => $admin_email,
        ];

        $this->client->submit($form, $data);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertContains('Step 2.Title', $crawler->filter('body')->extract(['_text'])[0]);

        $form = $crawler->filter('#poll_date_choices_submit')->form();

        $data = [
            'poll_date_choices' => [
                'choices' => [
                    [
                        'date' => '2019-02-17',
                        'moments' => [
                            [
                                'title' => '12h',
                                'title' => '13h'
                            ],
                        ]
                    ],
                    [
                        'date' => '2019-02-18'
                    ],
                    [
                        'date' => '2019-01-18'
                    ],
                ],
            ],
        ];

        $this->client->submit($form, $data);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('Step 3.List of your choices', $crawler->filter('body')->extract(['_text'])[0]);

        $form = $crawler->filter('button#archive_submit')->form();

        $data = [
            'archive[end_date]' => (new \DateTime('now'))->modify('+3 month')->format('Y-m-d'),
        ];

        $this->client->enableProfiler();
        $this->client->submit($form, $data);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        if ($admin_email) {
            $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(2, $mailCollector->getMessageCount());
        }

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('PollInfo.Admin link of the poll', $crawler->filter('body')->extract(['_text'])[0]);
    }


    public function invalidArchiveDateProvider()
    {
        return [
            [(new \DateTime('now'))->modify('+2 year'), 'This value should be less than'],
            [new \DateTime('now'), 'This value should be greater than'],
        ];
    }

    /**
     * @dataProvider invalidArchiveDateProvider
     *
     * @param \DateTime $date
     * @param string $error
     */
    public function testSubmitPollWithInvalidArchiveDate(\DateTime $date, string $error)
    {
        $crawler = $this->client->request('GET', '/p/new/date');

        $this->assertTrue($this->client->getResponse()->isOk());
        $this->assertCount(1, $crawler->filter('form[name="poll"]'));
        $this->assertCount(1, $crawler->filter('div.alert-info:contains("Step 1.You are in the poll creation section.")'));

        $form = $crawler->filter('button[id=poll_submit]')->form();

        $data = [
            'poll[admin_name]' => 'admin',
            'poll[title]' => 'my poll',
            'poll[description]' => 'my awesome poll',
        ];

        $this->client->submit($form, $data);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertContains('Step 2.Title', $crawler->filter('body')->extract(['_text'])[0]);

        $form = $crawler->filter('#poll_date_choices_submit')->form();

        $data = [
            'poll_date_choices' => [
                'choices' => [
                    [
                        'date' => '2019-02-17',
                        'moments' => [
                            [
                                'title' => '12h',
                                'title' => '13h'
                            ],
                        ]
                    ],
                    [
                        'date' => '2019-02-18'
                    ],
                    [
                        'date' => '2019-01-18'
                    ],
                ],
            ],
        ];

        $this->client->submit($form, $data);

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains('Step 3.List of your choices', $crawler->filter('body')->extract(['_text'])[0]);

        $form = $crawler->filter('button[id=archive_submit]')->form();

        $data = [
            'archive[end_date]' => $date->format('Y-m-d'),
        ];

        $this->client->enableProfiler();
        $crawler = $this->client->submit($form, $data);

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $this->assertContains($error, $crawler->filter('div.alert.alert-danger')->extract(['_text'])[0]);
    }
}
