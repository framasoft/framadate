<?php
namespace Framadate\Services;

use PHPMailer;
use Symfony\Component\HttpFoundation\Session\Session;

class MailService {
    const DELAY_BEFORE_RESEND = 300;

    const MAILSERVICE_KEY = 'mailservice';

    /**
     * @var Session
     */
    private $session;

    private $smtp_allowed;

    /**
     * @var LogService
     */
    private $logService;

    /**
     * MailService constructor.
     * @param Session $session
     * @param $smtp_allowed
     */
    function __construct(Session $session, $smtp_allowed) {
        $this->session = $session;
        $this->logService = new LogService();
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
     * @throws \phpmailerException
     */
    public function send($to, $subject, $body, $msgKey = null) {
        if ($this->smtp_allowed === true && $this->canSendMsg($msgKey)) {
            $mail = new PHPMailer(true);
            $mail->isSMTP();

            // From
            $mail->FromName = NOMAPPLICATION;
            $mail->From = ADRESSEMAILADMIN;
            if ($this->isValidEmail(ADRESSEMAILREPONSEAUTO)) {
                $mail->addReplyTo(ADRESSEMAILREPONSEAUTO);
            }

            // To
            $mail->addAddress($to);

            // Subject
            $mail->Subject = $subject;

            // Bodies
            $body = $body . ' <br/><br/>' . __('Mail', 'Thanks for your trust.') . ' <br/>' . NOMAPPLICATION . ' <hr/>' . __('Mail', 'FOOTER');
            $mail->isHTML(true);
            $mail->msgHTML($body, ROOT_DIR, true);

            // Build headers
            $mail->CharSet = 'UTF-8';
            $mail->addCustomHeader('Auto-Submitted', 'auto-generated');
            $mail->addCustomHeader('Return-Path', '<>');

            // Send mail
            $mail->send();

            // Log
            $this->logService->log('MAIL', 'Mail sent to: ' . $to . ', key: ' . $msgKey);

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
