<?php

namespace Framadate\Controller;

use Framadate\I18nWrapper;
use Framadate\Message;
use Framadate\Services\CommentService;
use Framadate\Services\InputService;
use Framadate\Services\MailService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Framadate\Services\SecurityService;
use Framadate\Utils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

class CommentController
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
     * @var UrlGenerator
     */
    private $url_generator;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var I18nWrapper
     */
    private $i18n;

    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var array
     */
    private $app_config;

    public function __construct(PollService $poll_service, CommentService $comment_service, SecurityService $security_service, MailService $mail_service, InputService $input_service, NotificationService $notification_service, UrlGenerator $url_generator, Session $session, I18nWrapper $i18n, Twig_Environment $twig, $app_config)
    {
        $this->poll_service = $poll_service;
        $this->comment_service = $comment_service;
        $this->security_service = $security_service;
        $this->mail_service = $mail_service;
        $this->input_service = $input_service;
        $this->notification_service = $notification_service;
        $this->url_generator = $url_generator;
        $this->session = $session;
        $this->i18n = $i18n;
        $this->twig = $twig;
        $this->app_config = $app_config;
    }

    public function createCommentAction(Request $request, $poll_id)
    {
        if (empty($poll_id)) {
            // return json response error
            return null;
        }
        $poll = $this->poll_service->findById($poll_id);

        $admin_poll_id = $request->get('poll_admin', null);
        if ($admin_poll_id != null && strlen($admin_poll_id) === 24) {
            $is_admin = ($this->poll_service->findByAdminId($admin_poll_id) !== null);
        }

        if (!$poll) {
            $message = new Message('error',  $this->i18n->get('Error', 'This poll doesn\'t exist !'));
        } else if ($poll && !$this->security_service->canAccessPoll($poll) && !$is_admin) {
            $message = new Message('error',  $this->i18n->get('Password', 'Wrong password'));
        } else {
            $name = $this->input_service->filterName($request->get('name'));
            $comment = $this->input_service->filterComment($request->get('comment'));

            if ($name === null) {
                $message = new Message('danger', $this->i18n->get('Error', 'The name is invalid.'));
            }

            if ($message === null) {
                // Add comment
                $result = $this->poll_service->addComment($poll_id, $name, $comment);
                if ($result) {
                    $message = new Message('success', $this->i18n->get('Comments', 'Comment added'));
                    $this->notification_service->sendUpdateNotification($poll, NotificationService::ADD_COMMENT, $name);
                } else {
                    $message = new Message('danger', $this->i18n->get('Error', 'Comment failed'));
                }
            }
            $comments = $this->poll_service->allCommentsByPollId($poll_id);
        }

        $comments_html = $this->twig->render('part/comments_list.twig', [
            'comments' => $comments,
            'admin' => $is_admin,
            'poll' => $poll
        ]);

        $response = ['result' => $result, 'message' => $message, 'comments' => $comments_html];

        return new JsonResponse($response);
    }

    /**
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
            $this->session->getFlashBag()->add('success', $this->i18n->get('adminstuds', 'Comment deleted'));
        } else {
            $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'Failed to delete the comment'));
        }
        return new RedirectResponse($this->url_generator->generate('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]));
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
        $editedVoteUniqueId = filter_input(INPUT_POST, 'editedVoteUniqueId', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);

        if (is_null($poll) || $this->app_config['use_smtp'] === false || is_null($token) || is_null($token_form_value)
            || !$token->check($token_form_value) || is_null($editedVoteUniqueId)) {
            $message = new Message('error', $this->i18n->get('Error', 'Something is going wrong...'));
        }

        if (is_null($message)) {
            $email = $this->mail_service->isValidEmail($_POST['email']);
            if (is_null($email)) {
                $message = new Message('error', $this->i18n->get('EditLink', 'The email address is not correct.'));
            }
        }

        if (is_null($message)) {
            $time = $this->session->get("Common", SESSION_EDIT_LINK_TIME);

            if (!empty($time)) {
                $remainingTime = TIME_EDIT_LINK_EMAIL - (time() - $time);

                if ($remainingTime > 0) {
                    $message = new Message('error', $this->i18n->get('EditLink', 'Please wait %d seconds before we can send an email to you then try again.', $remainingTime));
                }
            }
        }

        if (is_null($message)) {
            $url = Utils::getUrlSondage($poll_id, false, $editedVoteUniqueId);

            try {
                $body = $this->twig->render(
                    'mail/remember_edit_link.tpl',
                    [
                        'poll' => $poll,
                        'poll_id' => $poll_id,
                        'editedVoteUniqueId' => $editedVoteUniqueId,
                    ]
                );
            } catch (\Twig_Error $e) {
                // log error
            }

            $subject = '[' . NOMAPPLICATION . '][' . $this->i18n->get('EditLink', 'REMINDER') . '] ' . $this->i18n->get('EditLink', 'Edit link for poll "%s"', [$poll->getTitle()]);

            //$mailService->send($email, $subject, $body);
            $this->session->remove(SESSION_EDIT_LINK_TOKEN);
            $this->session->set(SESSION_EDIT_LINK_TIME, time());

            $message = new Message('success', $this->i18n->get('EditLink', 'Your reminder has been successfully sent!'));
            $result = true;
        }

        $response = ['result' => $result, 'message' => $message];

        return new JsonResponse($response);
    }
}
