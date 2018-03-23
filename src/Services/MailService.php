<?php
namespace Framadate\Services;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class MailService
{
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
     * @var string
     */
    private $app_name;

    /**
     * @var array
     */
    private $from;

    /**
     * @var array
     */
    private $reply_to;

    /**
     * MailService constructor.
     * @param SessionInterface $session
     * @param \Swift_Mailer $mailer
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param bool $smtp_allowed
     * @param string $app_name
     * @param array $from
     * @param array $reply_to
     */
    public function __construct(SessionInterface $session,
                                \Swift_Mailer $mailer,
                                LoggerInterface $logger,
                                TranslatorInterface $translator,
                                bool $smtp_allowed = true,
                                string $app_name = 'Framadate',
                                array $from = ['email' => 'admin@tld', 'name' => 'admin'],
                                array $reply_to = ['email' => 'auto@reply', 'name' => 'autoreply']
    )
    {
        $this->session = $session;
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->smtp_allowed = $smtp_allowed;
        $this->app_name = $app_name;
        $this->from = $from;
        $this->reply_to = $reply_to;
    }

    /**
     * @param $email
     * @return mixed
     */
    public function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param $to
     * @param $subject
     * @param $body
     * @param null $msgKey
     */
    public function send(string $to, $subject, $body, $msgKey = null)
    {
        if ($this->smtp_allowed === true && $this->canSendMsg($msgKey)) {
            $this->logger->info('Building email');
            $mail = (new \Swift_Message())
                ->setFrom($this->from['email'], $this->from['name'])
                ->setTo([$to])
                ->setReplyTo($this->reply_to['email'], $this->reply_to['name'])
                ->setBody($body . ' <br/><br/>' . $this->translator->trans('Mail.Thanks for your trust.') . ' <br/>' . $this->app_name . ' <hr/>' . $this->translator->trans('Mail.FOOTER'), 'text/html')
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
        } else {
            $this->logger->info("Not sending an email because SMTP is not allowed or we can't send emails yet");
        }
    }

    /**
     * @param $msgKey
     * @return bool
     */
    public function canSendMsg($msgKey)
    {
        if ($msgKey === null) {
            return true;
        }

        $mail_service_key = $this->session->get(self::MAILSERVICE_KEY, []);

        return !isset($mail_service_key[$msgKey]) || time() - $mail_service_key[$msgKey] > self::DELAY_BEFORE_RESEND;
    }
}
