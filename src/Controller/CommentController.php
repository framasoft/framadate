<?php

namespace Framadate\Controller;

use Framadate\Entity\Poll;
use Framadate\I18nWrapper;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

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
     * @Route("{poll_id}/comment/new", name="new_comment")
     *
     * @param Request $request
     * @param $poll_id
     * @return null|JsonResponse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createCommentAction(Request $request, $poll_id)
    {
        $message = null;
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
            $message = new Message('error',  $this->i18n->trans('Error.This poll doesn\'t exist !'));
        } else if ($poll && !$this->security_service->canAccessPoll($poll) && !$is_admin) {
            $message = new Message('error',  $this->i18n->trans('Password.Wrong password'));
        } else {
            $name = $this->input_service->filterName($request->get('name'));
            $comment = $this->input_service->filterComment($request->get('comment'));

            if ($name === null) {
                $message = new Message('danger', $this->i18n->trans('Error.The name is invalid.'));
            }

            if ($message === null) {
                // Add comment
                $result = $this->poll_service->addComment($poll_id, $name, $comment);
                if ($result) {
                    $message = new Message('success', $this->i18n->trans('Comments.Comment added'));
                    $this->notification_service->sendUpdateNotification($poll, NotificationService::ADD_COMMENT, $name);
                } else {
                    $message = new Message('danger', $this->i18n->trans('Error.Comment failed'));
                }
            }
            $comments = $this->poll_service->allCommentsByPollId($poll_id);
        }

        $comments_html = $this->render('part/comments_list.twig', [
            'comments' => $comments,
            'admin' => $is_admin,
            'poll' => $poll
        ]);

        $response = ['result' => $result, 'message' => $message, 'comments' => $comments_html->getContent()];

        return new JsonResponse($response);
    }

    /**
     * @Route("/{poll_id}/comment/remove", name="remove_comment")
     *
     * @param Request $request
     * @param $poll_id
     * @return null
     */
    public function removeCommentAction(Request $request, $poll_id)
    {
        if (empty($poll_id)) {
            // return json response error
            return null;
        }

        $poll = $this->poll_service->findById($poll_id);
        $comment_id = $request->get('delete_comment');

        if ($this->comment_service->deleteComment($poll_id, $comment_id)) {
            $this->session->getFlashBag()->add('success', $this->i18n->trans('adminstuds.Comment deleted'));
        } else {
            $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Failed to delete the comment'));
        }
        return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
    }

    /**
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

            try {
                $body = $this->render(
                    'mail/remember_edit_link.twig',
                    [
                        'poll' => $poll,
                        'editedVoteUniqueId' => $editedVoteUniqueId,
                    ]
                );
            } catch (\Twig_Error $e) {
                // log error
            }

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
