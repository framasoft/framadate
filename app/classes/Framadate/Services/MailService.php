<?php
namespace Framadate\Services;

class MailService {

    private $smtp_allowed;

    const DELAY_BEFORE_RESEND = 300;

    const MAILSERVICE_KEY = 'mailservice';

    function __construct($smtp_allowed) {
        $this->smtp_allowed = $smtp_allowed;
    }

    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function send($to, $subject, $body, $param = '', $msgKey = null) {
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

            mail($to, $subject, $body, $headers, $param);

            // Set date before resend in sessions

            $_SESSION[self::MAILSERVICE_KEY][$msgKey] = time() + self::DELAY_BEFORE_RESEND;
        }
    }

    function canSendMsg($msgKey) {
        if ($msgKey == null) {
            return true;
        }

        if (!isset($_SESSION[self::MAILSERVICE_KEY])) {
            $_SESSION[self::MAILSERVICE_KEY] = [];
        }
        return !isset($_SESSION[self::MAILSERVICE_KEY][$msgKey]) || $_SESSION[self::MAILSERVICE_KEY][$msgKey] < time();
    }

}
 