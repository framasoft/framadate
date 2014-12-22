<?php
namespace Framadate\Services;

class MailService {

    private $smtp_allowed;

    function __construct($smtp_allowed) {
        $this->smtp_allowed = $smtp_allowed;
    }

    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function send($to, $subject, $body, $param = '') {
        if($this->smtp_allowed == true) {
            mb_internal_encoding('UTF-8');

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
            $headers .= "Content-Type: text/plain; charset=UTF-8\n";
            $headers .= "Content-Transfer-Encoding: 8bit\n";
            $headers .= "Auto-Submitted:auto-generated\n";
            $headers .= 'Return-Path: <>';

            $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8') . _('\n--\n\n« La route est longue, mais la voie est libre… »\nFramasoft ne vit que par vos dons (déductibles des impôts).\nMerci d\'avance pour votre soutien http://soutenir.framasoft.org.');

            mail($to, $subject, $body, $headers, $param);
        }
    }

}
 