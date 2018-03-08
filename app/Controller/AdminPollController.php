<?php

namespace Framadate\Controller;

use Framadate\Editable;
use Framadate\I18nWrapper;
use Framadate\Security\PasswordHasher;
use Framadate\Services\AdminPollService;
use Framadate\Services\InputService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

class AdminPollController
{
    /**
     * @var PollService
     */
    private $poll_service;

    /**
     * @var InputService
     */
    private $input_service;

    /**
     * @var AdminPollService
     */
    protected $admin_poll_service;

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
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var I18nWrapper
     */
    private $i18n;

    private $app_config;

    /**
     * PollController constructor.
     * @param PollService $poll_service
     * @param InputService $input_service
     * @param AdminPollService $adminPollService
     * @param NotificationService $notificationService
     * @param UrlGenerator $url_generator
     * @param Session $session
     * @param Twig_Environment $twig
     * @param I18nWrapper $i18n
     * @param $app_config
     */
    public function __construct(PollService $poll_service, InputService $input_service, AdminPollService $adminPollService, NotificationService $notificationService, UrlGenerator $url_generator, Session $session, Twig_Environment $twig, I18nWrapper $i18n, $app_config)
    {
        $this->poll_service = $poll_service;
        $this->input_service = $input_service;
        $this->admin_poll_service = $adminPollService;
        $this->notification_service = $notificationService;
        $this->url_generator = $url_generator;
        $this->session = $session;
        $this->twig = $twig;
        $this->i18n = $i18n;
        $this->app_config = $app_config;
    }

    /**
     * @param string $admin_poll_id
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showAdminPollAction($admin_poll_id)
    {
        $message = '';
        $editingVoteId = 0;

        if (strlen($admin_poll_id) === 24) {
            $poll = $this->poll_service->findByAdminId($admin_poll_id);
        }

        if ($poll) {
            $poll_id = $poll->getId();
        } else {
            return $this->twig->render('error.twig', [
                'error' => $this->i18n->get('Error', 'This poll doesn\'t exist !'),
            ]);
        }

        // Retrieve data
        $slots = $this->poll_service->allSlotsByPoll($poll);
        $votes = $this->poll_service->allVotesByPollId($poll_id);
        $comments = $this->poll_service->allCommentsByPollId($poll_id);

        // Assign data to template

        return $this->twig->render('studs.twig', [
            'poll' => $poll,
            'title' => $this->i18n->get('Generic', 'Poll') . ' - ' . $poll->getTitle(),
            'expired' => strtotime($poll->getEndDate()) < time(),
            'deletion_date' => strtotime($poll->getEndDate()) + PURGE_DELAY * 86400,
            'slots' => $poll->getFormat() === 'D' ? $this->poll_service->splitSlots($slots) : $slots,
            'slots_hash' => $this->poll_service->hashSlots($slots),
            'votes' => $this->poll_service->splitVotes($votes),
            'best_choices' => $this->poll_service->computeBestChoices($votes),
            'comments' => $comments,
            'editingVoteId' => $editingVoteId,
            'message' => $message,
            'admin' => true,
            'hidden' => false,
            'accessGranted' => true,
            'resultPubliclyVisible' => true,
            'editedVoteUniqueId' => '',
            'default_to_markdown_editor' => $this->app_config['markdown_editor_by_default'],
        ]);
    }

    public function editPollAction(Request $request, $admin_poll_id)
    {
        if (strlen($admin_poll_id) !== 24) {
            // redirect to error page
            return null;
        }

        $poll = $this->poll_service->findByAdminId($admin_poll_id);

        /*if ($poll) {
            $poll_id = $poll->getId();
        } else {
            try {
                return $this->twig->render(
                    'error.twig',
                    [
                        'error' => $this->i18n->get('Error', 'This poll doesn\'t exist !'),
                    ]
                );
            } catch (\Twig_Error $e) {
                var_dump($e->getMessage());
            }
        }*/

        $updated = false;
        $field = $this->input_service->filterAllowedValues($request->get('update_poll_info'), ['title', 'admin_mail', 'description',
            'rules', 'expiration_date', 'name', 'hidden', 'removePassword', 'password']);

        // Update the right poll field
        if ($field === 'title') {
            $title = $this->input_service->filterTitle($request->get('title'));
            if ($title) {
                $poll->setTitle($title);
                $updated = true;
            }
        } elseif ($field === 'admin_mail') {
            $admin_mail = $this->input_service->filterMail($request->get('admin_mail'));
            if ($admin_mail) {
                $poll->setAdminMail($admin_mail);
                $updated = true;
            }
        } elseif ($field === 'description') {
            $description = $this->input_service->filterDescription($request->get('description'));
            if ($description) {
                $poll->setDescription($description);
                $updated = true;
            }
        } elseif ($field === 'rules') {
            $rules = (int) strip_tags($request->get('rules'));
            switch ($rules) {
                case 1:
                    $poll->setActive(true);
                    $poll->setEditable(Editable::NOT_EDITABLE);
                    $updated = true;
                    break;
                case 2:
                    $poll->setActive(true);
                    $poll->setEditable(Editable::EDITABLE_BY_ALL);
                    $updated = true;
                    break;
                case 3:
                    $poll->setActive(true);
                    $poll->setEditable(Editable::EDITABLE_BY_OWN);
                    $updated = true;
                    break;
                case 0:
                default:
                    $poll->setActive(false);
                    $poll->setEditable(Editable::NOT_EDITABLE());
                    $updated = true;
                    break;
            }
        } elseif ($field === 'expiration_date') {
            $expiration_date = $this->input_service->filterDate($request->get('expiration_date'));
            if ($expiration_date) {
                $poll->setEndDate($expiration_date);
                $updated = true;
            }
        } elseif ($field === 'name') {
            $admin_name = $this->input_service->filterName($request->get('name'));
            if ($admin_name) {
                $poll->setAdminName($admin_name);
                $updated = true;
            }
        } elseif ($field === 'hidden') {
            $hidden = $this->input_service->filterBoolean($request->get('hidden', false));
            if ($hidden !== $poll->isHidden()) {
                $poll->setHidden($hidden);
                $poll->setResultsPubliclyVisible(false);
                $updated = true;
            }
        } elseif ($field === 'removePassword') {
            $removePassword = $this->input_service->filterBoolean($request->get('removePassword', false));
            if ($removePassword) {
                $poll->setResultsPubliclyVisible(false);
                $poll->setPasswordHash(null);
                $updated = true;
            }
        } elseif ($field === 'password') {
            $password = $request->get('password', null);

            /**
             * Did the user choose results to be publicly visible ?
             */
            $resultsPubliclyVisible = $this->input_service->filterBoolean($request->get('resultsPubliclyVisible', false));
            /**
             * If there's one, save the password
             */
            if (!empty($password)) {
                $poll->setPasswordHash(PasswordHasher::hash($password));
                $updated = true;
            }

            /**
             * If not pasword was set and the poll should be hidden, hide the results
             */
            if ($poll->getPasswordHash() === null || $poll->isHidden() === true) {
                $poll->setResultsPubliclyVisible(false);
            }

            /**
             * We don't have a password, the poll is hidden and we change the results public visibility
             */
            if ($resultsPubliclyVisible !== $poll->isResultsPubliclyVisible() && $poll->getPasswordHash() !== null && $poll->isHidden() === false) {
                $poll->setResultsPubliclyVisible($resultsPubliclyVisible);
                $updated = true;
            }
        }

        // Update poll in database
        if ($updated && $this->admin_poll_service->updatePoll($poll)) {
            $this->session->getFlashBag()->add('success', $this->i18n->get('adminstuds', 'Poll saved'));
            $this->notification_service->sendUpdateNotification($poll, NotificationService::UPDATE_POLL);
        } else {
            $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'Failed to save poll'));
            $poll = $this->poll_service->findById($poll->getId());
        }

        return new RedirectResponse($this->url_generator->generate('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]));
    }
}
