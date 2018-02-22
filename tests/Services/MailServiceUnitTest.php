<?php
namespace Framadate\Tests\Services;

use Framadate\Services\MailService;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Translation\TranslatorInterface;

class MailServiceUnitTest extends TestCase {
    const MSG_KEY = '666';

    public function test_should_send_a_2nd_mail_after_a_good_interval() {
        // Given
        $session = new Session(new MockFileSessionStorage());
        $mailService = new MailService($session, $this->getMailer(), new NullLogger(), $this->getTranslator(), true);
        $session->set(MailService::MAILSERVICE_KEY, [self::MSG_KEY => time() - 1000]);

        // When
        $canSendMsg = $mailService->canSendMsg(self::MSG_KEY);

        // Then
        $this->assertSame(true, $canSendMsg);
    }

    public function test_should_not_send_2_mails_in_a_short_interval() {
        // Given
        $session = new Session(new MockFileSessionStorage());
        $mailService = new MailService($session, $this->getMailer(), new NullLogger(), $this->getTranslator(), true);
        $session->set(MailService::MAILSERVICE_KEY, [self::MSG_KEY => time()]);

        // When
        $canSendMsg = $mailService->canSendMsg(self::MSG_KEY);

        // Then
        $this->assertSame(false, $canSendMsg);
    }

    private function getTranslator()
    {
        return $this->getMockBuilder(TranslatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getMailer()
    {
        return $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
