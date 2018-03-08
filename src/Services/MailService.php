<?php
namespace Framadate\Services;

use PHPMailer;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MailService {
    const DELAY_BEFORE_RESEND = 300;

    const MAILSERVICE_KEY = 'mailservice';

    /**
     * @var SessionInterface
     */
    private $session;

    private $smtp_allowed;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MailService constructor.
     * @param SessionInterface $session
     * @param \Swift_Mailer $mailer
     * @param LoggerInterface $logger
     * @param bool $smtp_allowed
     */
    function __construct(SessionInterface $session, \Swift_Mailer $mailer, LoggerInterface $logger, TranslatorInterface $translator, bool $smtp_allowed = true) {
        $this->session = $session;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->smtp_allowed = $smtp_allowed;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $to
     * @param $subject
     * @param $body
     * @param null $msgKey
     */
    public function send($to, $subject, $body, $msgKey = null) {
        if ($this->smtp_allowed === true && $this->canSendMsg($msgKey)) {
            $mail = (new \Swift_Message())
                ->setFrom('admin@tld', 'admin')
                ->setTo([$to])
                ->setReplyTo('auto@reply', 'autoreply')
                ->setBody($body . ' <br/><br/>' . $this->translator->trans('Mail.Thanks for your trust.') . ' <br/>' . 'framadate' . ' <hr/>' . $this->translator->trans('Mail.FOOTER'), 'text/html')
                ->setSubject($subject)
                ->setCharset('UTF-8')
                ;
            $headers = $mail->getHeaders();
            $headers->addTextHeader('Auto-Submitted', 'auto-generated');
            $headers->addTextHeader('Return-Path', '<>');

            $this->mailer->send($mail);

            // Log
            $this->logger->info('MAIL : Mail sent to: ' . $to . ', key: ' . $msgKey);

            // Store the mail sending date
            $this->session->set(self::MAILSERVICE_KEY, [$msgKey => time()]);
        }
    }

    /**
     * @param $msgKey
     * @return bool
     */
    public function canSendMsg($msgKey) {
        if ($msgKey === null) {
            return true;
        }

        $mail_service_key = $this->session->get(self::MAILSERVICE_KEY, []);

        return !isset($mail_service_key[$msgKey]) || time() - $mail_service_key[$msgKey] > self::DELAY_BEFORE_RESEND;
    }
}
