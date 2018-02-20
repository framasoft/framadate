<?php
namespace Framadate\Services;

use PHPMailer;

class MailService {
    const DELAY_BEFORE_RESEND = 300;

    const MAILSERVICE_KEY = 'mailservice';

    private $smtp_allowed;

    private $logService;

    function __construct($smtp_allowed) {
        $this->logService = new LogService();
        $this->smtp_allowed = $smtp_allowed;
    }

    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function send($to, $subject, $body, $msgKey = null) {
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
            $_SESSION[self::MAILSERVICE_KEY][$msgKey] = time();
        }
    }

    function canSendMsg($msgKey) {
        if ($msgKey === null) {
            return true;
        }

        if (!isset($_SESSION[self::MAILSERVICE_KEY])) {
            $_SESSION[self::MAILSERVICE_KEY] = [];
        }
        return !isset($_SESSION[self::MAILSERVICE_KEY][$msgKey]) || time() - $_SESSION[self::MAILSERVICE_KEY][$msgKey] > self::DELAY_BEFORE_RESEND;
    }
}
 