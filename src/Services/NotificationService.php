<?php


namespace Framadate\Services;

use Framadate\I18nWrapper;
use Framadate\Entity\Poll;
use Framadate\Utils;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class NotificationService
{
    const UPDATE_VOTE = 1;
    const ADD_VOTE = 2;
    const ADD_COMMENT = 3;
    const UPDATE_POLL = 10;
    const DELETED_POLL = 11;

    /**
     * @var MailService
     */
    private $mailService;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TranslatorInterface
     */
    private $i18n;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * NotificationService constructor.
     * @param MailService $mailService
     * @param SessionInterface $session
     * @param TranslatorInterface $i18n
     */
    public function __construct(MailService $mailService, SessionInterface $session, TranslatorInterface $i18n, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailService = $mailService;
        $this->session = $session;
        $this->i18n = $i18n;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Send a notification to the poll admin to notify him about an update.
     *
     * @param $poll Poll The poll
     * @param $name string The name user who triggered the notification
     * @param $type int cf: Constants on the top of this page
     */
    public function sendUpdateNotification(Poll $poll, $type, $name = '')
    {
        if (!$this->session->has('mail_sent')) {
            $this->session->set('mail_sent', []);
        }

        $isVoteAndCanSendIt = ($type === self::UPDATE_VOTE || $type === self::ADD_VOTE) && $poll->getReceiveNewVotes();
        $isCommentAndCanSendIt = $type === self::ADD_COMMENT && $poll->getReceiveNewComments();
        $isOtherType = $type !== self::UPDATE_VOTE && $type !== self::ADD_VOTE && $type !== self::ADD_COMMENT;

        if ($isVoteAndCanSendIt || $isCommentAndCanSendIt || $isOtherType) {
            if (self::isParticipation($type)) {
                $translationString = 'Poll\'s participation: %s';
            } else {
                $translationString = 'Notification of poll: %s';
            }

            $subject = '[Framadate] ' . $this->i18n->trans('Mail' . $translationString, ['%s' => $poll->getTitle()]);

            $message = '';

            $urlSondage = $this->urlGenerator->generate('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
            $link = '<a href="' . $urlSondage . '">' . $urlSondage . '</a>' . "\n\n";

            switch ($type) {
                case self::UPDATE_VOTE:
                    $message .= $name . ' ';
                    $message .= $this->i18n->trans("Mail.updated a vote.\nYou can find your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::ADD_VOTE:
                    $message .= $name . ' ';
                    $message .= $this->i18n->trans("Mail.filled a vote.\nYou can find your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::ADD_COMMENT:
                    $message .= $name . ' ';
                    $message .= $this->i18n->trans("Mail.wrote a comment.\nYou can find your poll at the link") . " :\n\n";
                    $message .= $link;
                    break;
                case self::UPDATE_POLL:
                    $message = $this->i18n->trans("Mail.Someone just change your poll available at the following link %s.", ['%s' => $link]) . "\n\n";
                    break;
                case self::DELETED_POLL:
                    $message = $this->i18n->trans("Mail.Someone just delete your poll %s.", Utils::htmlEscape($poll->getTitle())) . "\n\n";
                    break;
            }

            $messageTypeKey = $type . '-' . $poll->getId();
            $this->mailService->send($poll->getAdminMail(), $subject, $message, $messageTypeKey);
        }
    }

    /**
     * @param $type
     * @return bool
     */
    public function isParticipation($type)
    {
        return $type >= self::UPDATE_POLL;
    }
}
