<?php

namespace Framadate\Controller;

use DateTime;
use Doctrine\DBAL\DBALException;
use Framadate\Editable;
use Framadate\Entity\Choice;
use Framadate\Entity\DateChoice;
use Framadate\Entity\Moment;
use Framadate\Entity\Poll;
use Framadate\Exception\MomentAlreadyExistsException;
use Framadate\Form\NewColumnType;
use Framadate\Form\NewDateColumnType;
use Framadate\Security\PasswordHasher;
use Framadate\Services\AdminPollService;
use Framadate\Services\InputService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class AdminPollController extends Controller
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
     * @var Session
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var TranslatorInterface
     */
    private $i18n;

    private $app_config;

    /**
     * PollController constructor.
     * @param PollService $poll_service
     * @param InputService $input_service
     * @param AdminPollService $adminPollService
     * @param NotificationService $notificationService
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(PollService $poll_service, InputService $input_service, AdminPollService $adminPollService, NotificationService $notificationService, SessionInterface $session, LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->poll_service = $poll_service;
        $this->input_service = $input_service;
        $this->admin_poll_service = $adminPollService;
        $this->notification_service = $notificationService;
        $this->i18n = $translator;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @Route("/a/{admin_poll_id}", name="view_admin_poll")
     *
     * @param string $admin_poll_id
     * @return string
     */
    public function showAdminPollAction($admin_poll_id)
    {
        $message = '';
        $editingVoteId = 0;

        if (strlen($admin_poll_id) === 24) {
            $poll = $this->poll_service->findByAdminId($admin_poll_id);
        }

        if (!$poll) {
            throw $this->createNotFoundException($this->i18n->trans('Error.This poll doesn\'t exist !'));
        }

        // Retrieve data
        $choices = $this->poll_service->allChoicesByPoll($poll);
        $poll->setChoices($choices);
        $votes = $this->poll_service->allVotesByPollId($poll->getId());
        $comments = $this->poll_service->allCommentsByPollId($poll->getId());

        // Assign data to template

        return $this->render('studs.twig', [
            'poll' => $poll,
            'title' => $this->i18n->trans('Generic.Poll') . ' - ' . $poll->getTitle(),
            'expired' => $poll->getEndDate() < date('now'),
            'deletion_date' => $poll->getEndDate()->modify('+1 day'),
            'choices_hash' => $this->poll_service->hashChoices($poll->getChoices()),
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

    /**
     * @Route("/a/{admin_poll_id}/edit", name="edit_admin_poll")
     *
     * @param Request $request
     * @param $admin_poll_id
     * @return RedirectResponse|null
     */
    public function editPollAction(Request $request, $admin_poll_id)
    {
        if (strlen($admin_poll_id) !== 24) {
            // redirect to error page
            return null;
        }

        $poll = $this->poll_service->findByAdminId($admin_poll_id);

        if (!$poll) {
            throw $this->createNotFoundException($this->i18n->trans('Error.This poll doesn\'t exist !'));
        }

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
                $poll->setEndDate(DateTime::createFromFormat('Y-m-d H:i:s', $expiration_date));
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
            $this->session->getFlashBag()->add('success', $this->i18n->trans('adminstuds.Poll saved'));
        // if mail is activated
            // $this->notification_service->sendUpdateNotification($poll, NotificationService::UPDATE_POLL);
        } else {
            $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Failed to save poll'));
            $poll = $this->poll_service->findById($poll->getId());
        }

        return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
    }

    /**
     * * @Route("/a/{admin_poll_id}/add_column", name="add_column")
     *
     * @param Request $request
     * @param $admin_poll_id
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function addColumnAction(Request $request, $admin_poll_id)
    {
        if (strlen($admin_poll_id) !== 24) {
            // redirect to error page
            return null;
        }

        /** @var Poll $poll */
        $poll = $this->poll_service->findByAdminId($admin_poll_id);

        if ($poll->getFormat() !== 'D') {
            $choice = new Choice();
            $form = $this->createForm(NewColumnType::class, $choice);
        } else {
            $choice = new DateChoice();
            $choice->addMoment(new Moment(''));
            $choice->addMoment(new Moment(''));
            $choice->addMoment(new Moment(''));
            $form = $this->createForm(NewDateColumnType::class, $choice);
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $choice->setPollId($poll->getId());
            $this->logger->info("Submitting choice", [$choice]);
            $choice->clearEmptyMoments();
            try {
                if ($poll->isDate()) {
                    $this->logger->info("Creating choice", [$choice]);
                    $this->admin_poll_service->addDateChoice($choice);
                } else {
                    $this->admin_poll_service->addClassicChoice($choice);
                }

                $this->session->getFlashBag()->add('success', $this->i18n->trans('adminstuds.Choice added'));
                return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
            } catch (MomentAlreadyExistsException $e) {
                $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.The column already exists'));
                // return to form
            }
        }

        return $this->render('add_column.twig', [
            'poll' => $poll,
            'title' =>  $this->i18n->trans('Generic.Poll') . ' - ' . $poll->getTitle(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/a/{admin_poll_id}/delete_column/{column}", name="delete_column")
     *
     * @param $admin_poll_id
     * @param $column
     * @return null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteColumnAction(string $admin_poll_id, string $column)
    {
        if (strlen($admin_poll_id) !== 24) {
            // redirect to error page
            return null;
        }

        $poll = $this->poll_service->findByAdminId($admin_poll_id);

        $column = base64_decode($column);

        if ($poll->getFormat() === 'D') {
            $ex = explode('@', $column);

            $choice = new DateChoice();
            $choice->setDate((new DateTime())->setTimestamp($ex[0]));
            $choice->setMoments([new Moment($ex[1])]);

            $result = $this->admin_poll_service->deleteDateChoice($poll, $choice);
        } else {
            $choice = new Choice();
            $choice->setName($column);
            $result = $this->admin_poll_service->deleteClassicChoice($poll, $choice);
        }

        if ($result) {
            $this->session->getFlashBag()->add('success', $this->i18n->trans('adminstuds.Column removed'));
        } else {
            $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Failed to delete column'));
        }

        return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $admin_poll_id]);
    }

    /**
     * @Route("/a/{admin_poll_id}/remove_vote/{vote_id}", name="remove_vote")
     *
     * @param $admin_poll_id
     * @param $vote_id
     */
    public function removeVoteAction($admin_poll_id, $vote_id)
    {
        try {
            if (strlen($admin_poll_id) === 24) {
                $poll = $this->poll_service->findByAdminId($admin_poll_id);
            }
            if (!$poll) {
                throw $this->createNotFoundException($this->i18n->trans('Error.This poll doesn\'t exist !'));
            }

            $vote_id = base64_decode($vote_id);
            if ($vote_id && $this->admin_poll_service->deleteVote($poll->getId(), $vote_id)) {
                $this->session->getFlashBag()->add('success', $this->i18n->trans('adminstuds.Vote deleted'));
            } else {
                $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Failed to delete the vote!'));
            }
        } catch (DBALException $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
