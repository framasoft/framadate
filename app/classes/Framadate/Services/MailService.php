<?php
namespace Framadate\Services;

class MailService {

    private $smtp_allowed;

    const DELAY_BEFORE_RESEND = 300;

    const MAILSERVICE_KEY = 'mailservice';

    private $logService;

    function __construct($smtp_allowed) {
        $this->logService = new LogService();
        $this->smtp_allowed = $smtp_allowed;
    }

    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function send($to, $subject, $body, $msgKey = null) {
        if ($this->smtp_allowed == true && $this->canSendMsg($msgKey)) {
            mb_internal_encoding('UTF-8');

            // Build headers

            $subject = mb_encode_mimeheader(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'), 'UTF-8', 'B', "\n", 9);

            $encoded_app = mb_encode_mimeheader(NOMAPPLICATION, 'UTF-8', 'B', "\n", 6);
            $size_encoded_app = (6 + strlen($encoded_app)) % 75;
            $size_admin_email = strlen(ADRESSEMAILADMIN);

            if (($size_encoded_app + $size_admin_email + 9) > 74) {
                $folding = "\n";
            } else {
                $folding = '';
            };

            $from = sprintf("From: %s%s <%s>\n", $encoded_app, $folding, ADRESSEMAILADMIN);

            $headers = $from;
            $headers .= 'Reply-To: ' . ADRESSEMAILREPONSEAUTO . "\n";
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\n";
            $headers .= "Content-Transfer-Encoding: 8bit\n";
            $headers .= "Auto-Submitted:auto-generated\n";
            $headers .= 'Return-Path: <>';

            // Build body

            $body = $body . '<br/><br/>' . __('Mail', 'Thanks for your trust.') . '<br/>' . NOMAPPLICATION . '<hr/>' . __('Mail', 'FOOTER');

            // Send mail

            $this->sendMail($to, $subject, $body, $msgKey, $headers);
        }
    }

    function canSendMsg($msgKey) {
        if ($msgKey == null) {
            return true;
        }

        if (!isset($_SESSION[self::MAILSERVICE_KEY])) {
            $_SESSION[self::MAILSERVICE_KEY] = [];
        }
        return !isset($_SESSION[self::MAILSERVICE_KEY][$msgKey]) || time() - $_SESSION[self::MAILSERVICE_KEY][$msgKey] > self::DELAY_BEFORE_RESEND;
    }

    private function sendMail($to, $subject, $body, $msgKey, $headers) {
        mail($to, $subject, $body, $headers, '');

        // Log

        $this->logService->log('MAIL', 'Mail sent to: ' . $to . ', key: ' . $msgKey);

        // Store the mail sending date

        $_SESSION[self::MAILSERVICE_KEY][$msgKey] = time();
    }

}
 