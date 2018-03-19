<?php

namespace Framadate\Controller;

use Doctrine\DBAL\DBALException;
use Framadate\Editable;
use Framadate\Entity\Choice;
use Framadate\Entity\Poll;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\ConcurrentVoteException;
use Framadate\Message;
use Framadate\Security\Token;
use Framadate\Services\InputService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

class VoteController extends Controller
{

    const GO_TO_STEP_2 = 'gotostep2';
    const USER_REMEMBER_VOTES_KEY = 'UserVotes';

    /**
     * @var PollService
     */
    protected $poll_service;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var TranslatorInterface
     */
    protected $i18n;

    /**
     * @var NotificationService
     */
    protected $input_service;

    /**
     * @var NotificationService
     */
    protected $notification_service;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    private $logger;

    protected $app_config;

    /**
     * PollController constructor.
     * @param PollService $poll_service
     * @param TranslatorInterface $i18n
     * @param InputService $input_service
     * @param NotificationService $notification_service
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     */
    public function __construct(PollService $poll_service,
                                TranslatorInterface $i18n,
                                InputService $input_service,
                                NotificationService $notification_service,
                                SessionInterface $session,
                                LoggerInterface $logger
)
    {
        $this->poll_service = $poll_service;
        $this->i18n = $i18n;
        $this->input_service = $input_service;
        $this->notification_service = $notification_service;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @Route("{poll_id}/vote", name="vote_poll")
     *
     * @param Request $request
     * @param $poll_id
     * @return RedirectResponse
     */
    public function voteAction(Request $request, $poll_id)
    {
        $this->logger->debug("Action on a poll vote");
        if (empty($poll_id)) {
            throw $this->createNotFoundException($this->i18n->trans('Error.This poll doesn\'t exist !'));
        }

        $poll = $this->poll_service->findById($poll_id);

        if (!$poll) {
            throw $this->createNotFoundException($this->i18n->trans('Error.This poll doesn\'t exist !'));
        }

        // TODO : verify password and granted
        $this->logger->debug("We have checked password and granted status");

        return $this->doVoteAction($request, $poll);
    }

    /**
     * @Route("{admin_poll_id}/admin/vote", name="vote_poll_admin")
     *
     * @param Request $request
     * @param $admin_poll_id
     * @return RedirectResponse
     * @throws DBALException
     */
    public function votePollAdminAction(Request $request, $admin_poll_id)
    {
        if (strlen($admin_poll_id) === 24) {
            $poll = $this->poll_service->findByAdminId($admin_poll_id);
        }
        if (!$poll) {
            throw $this->createNotFoundException($this->i18n->trans('Error.This poll doesn\'t exist !'));
        }
        return $this->doVoteAction($request, $poll, true);
    }

    public function doVoteAction(Request $request, Poll $poll, bool $redirect_to_admin = false): RedirectResponse
    {
        $message = null;
        $name = $this->input_service->filterName($request->get('name', null));
        $choices = $this->input_service->filterArray($request->get('choices', []), FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => Choice::CHOICE_REGEX]]);
        $slots_hash = $this->input_service->filterMD5($request->get('control', null));

        if ($name === null) {
            $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.The name is invalid.'));
        }
        if (count($choices) !== count($request->get('choices', []))) {
            $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.There is a problem with your choices'));
        }

        if ($message === null) {
            // Edit vote
            $editedVoteId = $request->get('save', null);
            if ($editedVoteId) {
                $this->logger->debug("Editing vote " . $editedVoteId . " from poll " . $poll->getId());
                try {
                    $result = $this->poll_service->updateVote($poll->getId(), $editedVoteId, $name, $choices, $slots_hash);
                    if ($result) {
                        $this->handleAddingOrUpdatingVoteResult($poll, $request->get('edited_vote', null), true);
                    } else {
                        $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Update vote failed'));
                    }

                } catch (ConcurrentEditionException $e) {
                    $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Poll has been updated before you vote'));
                } catch (ConcurrentVoteException $e) {
                    $this->session->getFlashBag()->add('danger', $this->i18n->trans("Error.Your vote wasn't counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry."));
                }
            } else {

                // Add vote
                try {
                    $this->logger->debug("Adding new vote for poll " . $poll->getId());
                    $result = $this->poll_service->addVote($poll->getId(), $name, $choices, $slots_hash);
                    if ($result) {
                        $this->handleAddingOrUpdatingVoteResult($poll, $result->getUniqId(), false);
                    } else {
                        $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Adding vote failed'));
                    }
                } catch (AlreadyExistsException $aee) {
                    $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.You already voted'));
                } catch (ConcurrentEditionException $cee) {
                    $this->session->getFlashBag()->add(
                        'danger',
                        $this->i18n->trans('Error.Poll has been updated before you vote')
                    );
                } catch (ConcurrentVoteException $cve) {
                    $this->logger->debug("concurrent vote exception");
                    $this->session->getFlashBag()->add(
                        'danger',
                        $this->i18n->trans(
                            "Error.Your vote wasn't counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry."
                        )
                    );
                }
            }
        }

        if ($redirect_to_admin) {
            return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
        }
        return $this->redirectToRoute('view_poll', ['poll_id' => $poll->getId()]);
    }

    /**
     * * @Route("{poll_id}/vote/{vote_uniq_id}/edit", name="edit_vote_poll")
     *
     * @param Request $request
     * @param $poll_id
     * @param $vote_uniq_id
     * @return string
     */
    public function editVoteAction(Request $request, $poll_id, $vote_uniq_id)
    {
        if (empty($poll_id)) {
            // return to previous page
            return null;
        }
        $poll = $this->poll_service->findById($poll_id);

        if (!$poll) {
            return $this->createNotFoundException();
        }

        $choices = $request->get('choices', null);
        if ($choices === null) {
            $accessGranted = true;
            $resultPubliclyVisible = true;
            $message = '';
            $comments = [];

            $poll = $this->poll_service->findById($poll_id);

            if (!$poll) {
                throw $this->createNotFoundException($this->i18n->trans('Error.This poll doesn\'t exist !'));
            }
            $editedVoteUniqueId = $this->session->get(VoteController::USER_REMEMBER_VOTES_KEY . $poll_id, 0);

            // TODO : Add back $resultPubliclyVisible and $accessGranted

            // Retrieve data
            if ($resultPubliclyVisible || $accessGranted) {
                $slots = $this->poll_service->allSlotsByPoll($poll);
                $votes = $this->poll_service->allVotesByPollId($poll_id);
                $comments = $this->poll_service->allCommentsByPollId($poll_id);
            }

            return $this->render('studs.twig', [
                'poll_id' => $poll_id,
                'poll' => $poll,
                'title' => $this->i18n->trans('Generic.Poll') . ' - ' . $poll->getTitle(),
                'expired' => $poll->getEndDate() < date('now'),
                'deletion_date' => $poll->getEndDate()->modify('+'. 60 .' day'),
                'slots' => $poll->getFormat() === 'D' ? $this->poll_service->splitSlots($slots) : $slots,
                'slots_hash' =>  $this->poll_service->hashSlots($slots),
                'votes' => $this->poll_service->splitVotes($votes),
                'best_choices' => $this->poll_service->computeBestChoices($votes),
                'comments' => $comments,
                'editingVoteId' => $vote_uniq_id,
                'message' => $message,
                'admin' => false,
                'hidden' => $poll->isHidden(),
                'accessGranted' => $accessGranted,
                'resultPubliclyVisible' => $resultPubliclyVisible,
                'editedVoteUniqueId' => $editedVoteUniqueId,
                'ValueMax' => $poll->getValueMax(),
            ]);
        }

        $name = $this->input_service->filterName($request->get('name'));
        $editedVote = filter_input(INPUT_POST, 'save', FILTER_VALIDATE_INT);
        $choices = $this->input_service->filterArray($choices, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);
        $slots_hash = $this->input_service->filterMD5($request->get('control'));

        if (empty($editedVote)) {
            $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Something is going wrong...'));
        }
        if (count($choices) !== count($_POST['choices'])) {
            $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.There is a problem with your choices'));
        }

        if ($message === null) {
            // Update vote
            try {
                $result = $this->poll_service->updateVote($poll_id, $editedVote, $name, $choices, $slots_hash);
                if ($result) {
                    if ($poll->getEditable() === Editable::EDITABLE_BY_OWN) {
                        $this->getMessageForOwnVoteEditableVote($vote_uniq_id, $poll_id, $name);
                    } else {
                        $this->session->getFlashBag()->add('success', $this->i18n->trans('studs.Update vote succeeded'));
                    }
                    $this->notification_service->sendUpdateNotification($poll, NotificationService::UPDATE_VOTE, $name);
                } else {
                    $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Update vote failed'));
                }
            } catch (ConcurrentEditionException $cee) {
                $this->session->getFlashBag()->add('danger', $this->i18n->trans('Error.Poll has been updated before you vote'));
            } catch (ConcurrentVoteException $cve) {
                $this->session->getFlashBag()->add('danger', $this->i18n->trans("Error.Your vote wasn't counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry."));
            }
        }

        return $this->redirectToRoute('view_poll', ['poll_id' => $poll_id]);
    }

    private function handleAddingOrUpdatingVoteResult(Poll $poll, $editedVoteUniqueId, $is_update)
    {
        if ($poll->getEditable() === Editable::EDITABLE_BY_OWN) {
            $this->getMessageForOwnVoteEditableVote($editedVoteUniqueId, $poll->getId());
        } else {
            $this->session->getFlashBag()->add('success', $this->i18n->trans('studs.Adding the vote succeeded'));
        }
        if ($is_update) {
            $this->notification_service->sendUpdateNotification($poll, NotificationService::UPDATE_VOTE);
        } else {
            $this->notification_service->sendUpdateNotification($poll, NotificationService::ADD_VOTE);
        }
    }

    private function getMessageForOwnVoteEditableVote($editedVoteUniqueId, $poll_id) {
        $this->session->set(self::USER_REMEMBER_VOTES_KEY . $poll_id, $editedVoteUniqueId);
        $urlEditVote = $this->url_generator->generate('edit_vote_poll', ['poll_id' => $poll_id, 'vote_uniq_id' => $editedVoteUniqueId], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->session->getFlashBag()->add('success', $this->i18n->trans('studs.Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:') . "<br>");

        $this->session->getFlashBag()->add('info', $urlEditVote);

        /*
         * $message = new Message(
            'success',
            $this->i18n->trans('studs', 'Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'),
            $urlEditVote,
            __f('Poll results', 'Edit the line: %s', $name),
            'glyphicon-pencil');
        */
        if ($this->app_config['use_smtp']) {
            $token = new Token();
            $this->session->set("Common" . SESSION_EDIT_LINK_TOKEN, $token);

           /* $smarty->assign('editedVoteUniqueId', $editedVoteUniqueId);
            $smarty->assign('token', $token->getValue());
            $smarty->assign('poll_id', $poll_id);
            $message->includeTemplate = $smarty->fetch('part/form_remember_edit_link.tpl');
            $smarty->clearAssign('token');*/
        }
        //return $message;
    }
}
