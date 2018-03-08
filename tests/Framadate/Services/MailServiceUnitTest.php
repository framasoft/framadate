<?php
namespace Tests\Framadate\Services;

use Framadate\Services\MailService;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Tests\Framadate\FramaTestCase;

class MailServiceUnitTest extends FramaTestCase {
    const MSG_KEY = '666';

    /**
     * @test
     */
    function should_send_a_2nd_mail_after_a_good_interval() {
        // Given
        $session = new Session(new MockFileSessionStorage());
        $mailService = new MailService($session, true);
        $session->set(MailService::MAILSERVICE_KEY, [self::MSG_KEY => time() - 1000]);

        // When
        $canSendMsg = $mailService->canSendMsg(self::MSG_KEY);

        // Then
        $this->assertSame(true, $canSendMsg);
    }

    /**
     * @test
     */
    function should_not_send_2_mails_in_a_short_interval() {
        // Given
        $session = new Session(new MockFileSessionStorage());
        $mailService = new MailService($session,true);
        $session->set(MailService::MAILSERVICE_KEY, [self::MSG_KEY => time()]);

        // When
        $canSendMsg = $mailService->canSendMsg(self::MSG_KEY);

        // Then
        $this->assertSame(false, $canSendMsg);
    }
}
