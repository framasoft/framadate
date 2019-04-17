<?php
namespace Framadate\Services;

use PHPMailer\PHPMailer\PHPMailer;

class MailService {
    const DELAY_BEFORE_RESEND = 300;

    const MAILSERVICE_KEY = 'mailservice';

    /**
     * @var bool
     */
    private $smtp_allowed;

    /**
     * @var array
     */
    private $smtp_options = [];

    /**
     * @var bool
     */
    private $use_sendmail;

    /**
     * @var LogService
     */
    private $logService;

    /**
     * MailService constructor.
     * @param $smtp_allowed
     * @param array $smtp_options
     * @param bool $use_sendmail
     */
    public function __construct($smtp_allowed, $smtp_options = [], $use_sendmail = false) {
        $this->logService = new LogService();
        $this->smtp_allowed = $smtp_allowed;
        if (true === is_array($smtp_options)) {
            $this->smtp_options = $smtp_options;
        }
        $this->use_sendmail = $use_sendmail;
    }

    public function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function isEnabled() {
        return $this->smtp_allowed === true;
    }

    public function send($to, $subject, $body, $msgKey = null) {
        if ($this->isEnabled() && $this->canSendMsg($msgKey)) {
            $mail = new PHPMailer(true);
            $this->configureMailer($mail);

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
            $body = $body . ' <br/><br/>' . __('Mail', 'Thank you for your trust.') . ' <br/>' . NOMAPPLICATION . ' <hr/>' . __('Mail', "\"The road is long, but the way is clear…\"<br/>Framasoft lives only by your donations.<br/>Thank you in advance for your support https://soutenir.framasoft.org");
            $mail->isHTML(true);
            $mail->CharSet = PHPMailer::CHARSET_UTF8;
            $mail->msgHTML($body, ROOT_DIR, function ($html) use ($mail) {
                return $this->html2text($mail, $html);
            });

            // Build headers
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

    public function canSendMsg($msgKey) {
        if ($msgKey === null) {
            return true;
        }

        if (!isset($_SESSION[self::MAILSERVICE_KEY])) {
            $_SESSION[self::MAILSERVICE_KEY] = [];
        }
        return !isset($_SESSION[self::MAILSERVICE_KEY][$msgKey]) || time() - $_SESSION[self::MAILSERVICE_KEY][$msgKey] > self::DELAY_BEFORE_RESEND;
    }

    /**
     * Configure the mailer with the options
     *
     * @param PHPMailer $mailer
     */
    private function configureMailer(PHPMailer $mailer) {
        if ($this->use_sendmail) {
            $mailer->isSendmail();
        } else {
            $mailer->isSMTP();
        }

        $available_options = [
            'host' => 'Host',
            'auth' => 'SMTPAuth',
            'username' => 'Username',
            'password' => 'Password',
            'secure' => 'SMTPSecure',
            'port' => 'Port',
        ];

        foreach ($available_options as $config_option => $mailer_option) {
            if (true === isset($this->smtp_options[$config_option]) && false === empty($this->smtp_options[$config_option])) {
                $mailer->{$mailer_option} = $this->smtp_options[$config_option];
            }
        }
    }

    /**
     * Custom "advanced" callback to pass to msgHTML function
     *
     * @param PHPMailer $mailer  a PHPMailer instance
     * @param string    $html    the HTML body of an email
     */
    private function html2text(PHPMailer $mailer, $html) {
        $html = preg_replace('/<a[^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/si', '${2}: ${1}', $html);

        $html = preg_replace('/<br\/>/', "\n", $html);

        return $mailer->html2text($html);
    }
}
