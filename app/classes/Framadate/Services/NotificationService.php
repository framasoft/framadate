<?php


namespace Framadate\Services;

use \stdClass;
use Framadate\Services\MailService;
use Framadate\Utils;

class NotificationService {
    const UPDATE_VOTE = 1;
    const ADD_VOTE = 2;
    const ADD_COMMENT = 3;
    const UPDATE_POLL = 10;
    const DELETED_POLL = 11;

    private $mailService;

    function __construct(MailService $mailService) {
        $this->mailService = $mailService;
    }

    /**
     * Send a notification to the poll admin to notify him about an update.
     *
     * @param $poll stdClass The poll
     * @param $name string The name user who triggered the notification
     * @param $type int cf: Constants on the top of this page
     */
    function sendUpdateNotification(stdClass $poll, $type, $name='') {
        if (!isset($_SESSION['mail_sent'])) {
            $_SESSION['mail_sent'] = [];
        }

        $isVoteAndCanSendIt = ($type === self::UPDATE_VOTE || $type === self::ADD_VOTE) && $poll->receiveNewVotes;
        $isCommentAndCanSendIt = $type === self::ADD_COMMENT && $poll->receiveNewComments;
        $isOtherType = $type !== self::UPDATE_VOTE && $type !== self::ADD_VOTE && $type !== self::ADD_COMMENT;

        if ($isVoteAndCanSendIt || $isCommentAndCanSendIt || $isOtherType) {
            if (self::isParticipation($type)) {
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
            $this->mailService->send($poll->admin_mail, $subject, $message, $messageTypeKey);
        }
    }

    function isParticipation($type)
    {
       return $type >= self::UPDATE_POLL;
    }
} 