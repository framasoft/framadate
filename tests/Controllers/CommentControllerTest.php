<?php

namespace Framadate\Tests\Controllers;

use Framadate\Tests\FramaWebTestCase;

class CommentControllerTest extends FramaWebTestCase
{
    public function testCreateAndDeleteComment()
    {
        $crawler = $this->client->request('GET', '/p/mypoll');

        $this->assertContains('My Poll', $crawler->filter('#title-form h3')->text());

        $this->client->request(
            'POST',
            '/p/mypoll/comment/new',
            ['name' => 'my name', 'comment' => 'my comment'],
            [],
            ['HTTP_Content-Type' => 'application/json']
            );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $json = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertNotEmpty($json);
        $this->assertEquals(true, $json['result']);
        $this->assertArrayHasKey('id', $json['comment']);

        $comment_id = $json['comment']['id'];
        $poll = $this->getPollById('mypoll');

        $this->client->request(
            'GET',
            '/p/'. $poll->getAdminId() . '/comment/' . $comment_id . '/remove'
        );

        $this->assertSame(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('adminstuds.Comment deleted', $crawler->filter('body')->text());

    }
}
