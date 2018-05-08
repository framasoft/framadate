<?php
namespace Framadate\Services;

use Framadate\FramaTestCase;

class MailServiceUnitTest extends FramaTestCase {
    const MSG_KEY = '666';

    public function test_should_send_a_2nd_mail_after_a_good_interval() {
        // Given
        $mailService = new MailService(true);
        $_SESSION[MailService::MAILSERVICE_KEY] = [self::MSG_KEY => time() - 1000];

        // When
        $canSendMsg = $mailService->canSendMsg(self::MSG_KEY);

        // Then
        $this->assertSame(true, $canSendMsg);
    }

    public function test_should_not_send_2_mails_in_a_short_interval() {
        // Given
        $mailService = new MailService(true);
        $_SESSION[MailService::MAILSERVICE_KEY] = [self::MSG_KEY => time()];

        // When
        $canSendMsg = $mailService->canSendMsg(self::MSG_KEY);

        // Then
        $this->assertSame(false, $canSendMsg);
    }
}
