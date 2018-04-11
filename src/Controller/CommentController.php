<?php

namespace Framadate\Controller;

use Framadate\Entity\Comment;
use Framadate\Entity\Poll;
use Framadate\Message;
use Framadate\Services\CommentService;
use Framadate\Services\InputService;
use Framadate\Services\MailService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Framadate\Services\SecurityService;
use Framadate\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CommentController extends Controller
{
    /**
     * @var PollService
     */
    private $poll_service;

    /**
     * @var CommentService
     */
    private $comment_service;

    /**
     * @var SecurityService
     */
    private $security_service;

    /**
     * @var MailService
     */
    private $mail_service;

    /**
     * @var InputService
     */
    private $input_service;

    /**
     * @var NotificationService
     */
    private $notification_service;

    /**
     * @var TranslatorInterface
     */
    private $i18n;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    private $app_config;

    public function __construct(PollService $poll_service, CommentService $comment_service, SecurityService $security_service, MailService $mail_service, InputService $input_service, NotificationService $notification_service, TranslatorInterface $i18n, SessionInterface $session)
    {
        $this->poll_service = $poll_service;
        $this->comment_service = $comment_service;
        $this->security_service = $security_service;
        $this->mail_service = $mail_service;
        $this->input_service = $input_service;
        $this->notification_service = $notification_service;
        $this->i18n = $i18n;
        $this->session = $session;
    }

    /**
     * @Route("/p/{poll_id}/comment/new", name="new_comment")
     *
     * @param Request $request
     * @param $poll_id
     * @return null|JsonResponse
     */
    public function createCommentAction(Request $request, string $poll_id)
    {
        $result = true;
        $is_admin = false;
        if (empty($poll_id)) {
            // return json response error
            return null;
        }
        $poll = $this->poll_service->findById($poll_id);

        $admin_poll_id = $request->get('poll_admin', null);
        if ($admin_poll_id != null && strlen($admin_poll_id) === 24) {
            $is_admin = $this->poll_service->findByAdminId($admin_poll_id) !== null;
        }

        if (!$poll) {
            return new JsonResponse(['result' => false, 'error' => $this->i18n->trans('Error.This poll doesn\'t exist !')], 400);
        } elseif ($poll && !$this->security_service->canAccessPoll($poll) && !$is_admin) {
            return new JsonResponse(['result' => false, 'error' => $this->i18n->trans('Password.Wrong password')], 400);
        } else {
            $name = $this->input_service->filterName($request->get('name'));
            $content = $this->input_service->filterComment($request->get('comment'));

            if ($name === null) {
                return new JsonResponse(['result' => false, 'error' => $this->i18n->trans('Error.The name is invalid.')], 400);
            }

            // Add comment
            $comment = new Comment();
            $comment->setName($name)->setContent($content)->setPollId($poll_id);
            $result = $this->poll_service->addComment($comment);
            if ($result) {
                $this->notification_service->sendUpdateNotification($poll, NotificationService::ADD_COMMENT, $name);

                return new JsonResponse(['result' => true, 'comment' => $result]);
            } else {
                return new JsonResponse(['result' => false, 'error' => $this->i18n->trans('Error.Comment failed')], 400);
            }
        }
    }

    /**
     * @Route("/p/{poll_admin_id}/comment/{comment_id}/remove", name="remove_comment")
     *
     * @param string $poll_admin_id
     * @param string $comment_id
     * @return null
     */
    public function removeCommentAction(string $poll_admin_id, string $comment_id)
    {
        if (empty($poll_admin_id) || empty($comment_id)) {
            // return json response error
            return null;
        }

        $poll = $this->poll_service->findByAdminId($poll_admin_id);

        if ($this->comment_service->deleteComment($poll->getId(), $comment_id)) {
            $this->session->getFlashBag()->add('success', $this->i18n->trans('adminstuds.Comment deleted'));
        } else {
            $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Failed to delete the comment'));
        }
        return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
    }

    /**
     * @TODO : FIX ME
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEditLinkByEmailAction(Request $request)
    {
        // TODO : validate $poll_id against POLL_REGEX
        $poll_id = $request->get('poll_id');
        if (!empty($poll_id)) {
            $poll = $this->poll_service->findById($poll_id);
        }

        $token = $this->session->get("Common", SESSION_EDIT_LINK_TOKEN);
        $token_form_value = empty($_POST['token']) ? null : $_POST['token'];
        $editedVoteUniqueId = filter_input(INPUT_POST, 'editedVoteUniqueId', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => Poll::POLL_REGEX]]);

        if (is_null($poll) || $this->app_config['use_smtp'] === false || is_null($token) || is_null($token_form_value)
            || !$token->check($token_form_value) || is_null($editedVoteUniqueId)) {
            $message = new Message('error', $this->i18n->trans('Error.Something is going wrong...'));
        }

        if (is_null($message)) {
            $email = $this->mail_service->isValidEmail($_POST['email']);
            if (is_null($email)) {
                $message = new Message('error', $this->i18n->trans('EditLink.The email address is not correct.'));
            }
        }

        if (is_null($message)) {
            $time = $this->session->get("Common", SESSION_EDIT_LINK_TIME);

            if (!empty($time)) {
                $remainingTime = TIME_EDIT_LINK_EMAIL - (time() - $time);

                if ($remainingTime > 0) {
                    $message = new Message('error', $this->i18n->trans('EditLink.Please wait %d seconds before we can send an email to you then try again.', ['%d' => $remainingTime]));
                }
            }
        }

        if (is_null($message)) {
            $url = Utils::getUrlSondage($poll_id, false, $editedVoteUniqueId);

            $body = $this->render(
                'mail/remember_edit_link.twig',
                [
                    'poll' => $poll,
                    'editedVoteUniqueId' => $editedVoteUniqueId,
                ]
            );

            $subject = '[' . NOMAPPLICATION . '][' . $this->i18n->trans('EditLink.REMINDER') . '] ' . $this->i18n->trans('EditLink.Edit link for poll "%s"', ['%s' => $poll->getTitle()]);

            //$mailService->send($email, $subject, $body);
            $this->session->remove(SESSION_EDIT_LINK_TOKEN);
            $this->session->set(SESSION_EDIT_LINK_TIME, time());

            $message = new Message('success', $this->i18n->trans('EditLink.Your reminder has been successfully sent!'));
            $result = true;
        }

        $response = ['result' => $result, 'message' => $message];

        return new JsonResponse($response);
    }
}
