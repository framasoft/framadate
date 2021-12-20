<?php

namespace Framadate\Services;

use \stdClass;
use function __;
use function __f;
use Framadate\Utils;
use o80\i18n\CantLoadDictionaryException;
use PHPMailer\PHPMailer\Exception;

class NotificationService {
    public const UPDATE_VOTE = 1;
    public const ADD_VOTE = 2;
    public const ADD_COMMENT = 3;
    public const UPDATE_POLL = 10;
    public const DELETED_POLL = 11;

    private $mailService;

    public function __construct(MailService $mailService) {
        $this->mailService = $mailService;
    }

    /**
     * Send a notification to the poll admin to notify him about an update.
     *
     * @param $poll stdClass The poll
     * @param $name string The name user who triggered the notification
     * @param $type int cf: Constants on the top of this page
     * @throws Exception|CantLoadDictionaryException
     */
    public function sendUpdateNotification($poll, int $type, string $name=''): void
    {
        if (!isset($_SESSION['mail_sent'])) {
            $_SESSION['mail_sent'] = [];
        }

        $isVoteAndCanSendIt = ($type === self::UPDATE_VOTE || $type === self::ADD_VOTE) && $poll->receiveNewVotes;
        $isCommentAndCanSendIt = $type === self::ADD_COMMENT && $poll->receiveNewComments;
        $isOtherType = $type !== self::UPDATE_VOTE && $type !== self::ADD_VOTE && $type !== self::ADD_COMMENT;

        if ($isVoteAndCanSendIt || $isCommentAndCanSendIt || $isOtherType) {
            if ($this->isParticipation($type)) {
                $translationString = 'Poll\'s participation: %s';
            } else {
                $translationString = 'Notification of poll: %s';
            }

            $subject = '[' . NOMAPPLICATION . '] ' . __f('Mail', $translationString, $poll->title);

            $message = '';

            $urlSondage = Utils::getUrlSondage($poll->admin_id, true);
            $link = '<a href="' . $urlSondage . '">' . $urlSondage . '</a>' . "\n\n";

            switch ($type) {
                case self::UPDATE_VOTE:
                    $message .= $name . ' ';
                    $message .= __('Mail', "updated a vote.\nYou can find your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::ADD_VOTE:
                    $message .= $name . ' ';
                    $message .= __('Mail', "filled a vote.\nYou can find your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::ADD_COMMENT:
                    $message .= $name . ' ';
                    $message .= __('Mail', "wrote a comment.\nYou can find your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::UPDATE_POLL:
                    $message = __f('Mail', 'Someone just change your poll available at the following link %s.', Utils::getUrlSondage($poll->admin_id, true)) . "\n\n";
                    break;
                case self::DELETED_POLL:
                    $message = __f('Mail', 'Someone just delete your poll %s.', Utils::htmlEscape($poll->title)) . "\n\n";
                    break;
            }

            $messageTypeKey = $type . '-' . $poll->id;
            if ($poll->admin_mail) {
                $this->mailService->send($poll->admin_mail, $subject, $message, $messageTypeKey);
            }
        }
    }

    public function isParticipation(int $type): bool
    {
       return $type >= self::UPDATE_POLL;
    }
}
