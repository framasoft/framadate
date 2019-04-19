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
    private $smarty;

    function __construct(MailService $mailService, \Smarty $smarty) {
        $this->mailService = $mailService;
        $this->smarty = $smarty;
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
                $translationString = 'Poll participation: %s';
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
                    $message .= __('Mail', "updated a vote.<br/>You can visit your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::ADD_VOTE:
                    $message .= $name . ' ';
                    $message .= __('Mail', "added a vote.<br/>You can visit your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::ADD_COMMENT:
                    $message .= $name . ' ';
                    $message .= __('Mail', "wrote a comment.<br/>You can visit your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::UPDATE_POLL:
                    $message = __f('Mail', 'Someone just changed your poll at the following link <a href=\"%1$s\">%1$s</a>.', Utils::getUrlSondage($poll->admin_id, true)) . "\n\n";
                    break;
                case self::DELETED_POLL:
                    $message = __f('Mail', 'Someone just deleted your poll "%s".', Utils::htmlEscape($poll->title)) . "\n\n";
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

    function sendPollCreationMails($creator_mail, $creator_name, $poll_name, $poll_id, $admin_poll_id) {
        if (!$this->mailService->isEnabled() || !$this->mailService->isValidEmail($creator_mail)) {
            return null;
        }

        $this->smarty->assign('poll_creator_name', Utils::htmlMailEscape($creator_name));
        $this->smarty->assign('poll_name', Utils::htmlMailEscape($poll_name));
        $this->smarty->assign('poll_url', Utils::getUrlSondage($poll_id));
        $message_participants = $this->smarty->fetch('mail/participants_forward_email.html.tpl');
        $this->mailService->send($creator_mail, '[' . NOMAPPLICATION . '][' . __('Mail', 'Participant link') . '] ' . __('Generic', 'Poll') . ': ' . $poll_name, $message_participants);

        $this->smarty->assign('poll_admin_url', Utils::getUrlSondage($admin_poll_id, true));
        $message_admin = $this->smarty->fetch('mail/creation_notification_email.html.tpl');
        $this->mailService->send($creator_mail, '[' . NOMAPPLICATION . '][' . __('Mail', 'Message for the author') . '] ' . __('Generic', 'Poll') . ': ' . $poll_name, $message_admin);
    }

    function sendEditedVoteNotification($email, &$poll, $poll_id, $edited_vote_id) {
        $url = Utils::getUrlSondage($poll_id, false, $edited_vote_id);

        $this->smarty->assign('poll', $poll);
        $this->smarty->assign('poll_id', $poll_id);
        $this->smarty->assign('editedVoteUniqueId', $edited_vote_id);
        $body = $this->smarty->fetch('mail/remember_edit_link.tpl');

        $subject = '[' . NOMAPPLICATION . '][' . __('EditLink', 'REMINDER') . '] ' . __f('EditLink', 'Edit link for poll "%s"', $poll->title);

        $this->mailService->send($email, $subject, $body);
    }

    function sendFindPollsByMailNotification($mail, &$polls) {
        $this->smarty->assign('polls', $polls);
        $body = $this->smarty->fetch('mail/find_polls.tpl');

        $this->mailService->send($mail, __('FindPolls', 'List of your polls') . ' - ' . NOMAPPLICATION, $body, 'SEND_POLLS');
    }
}
