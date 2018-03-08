<?php

namespace Framadate\Controller;

use Framadate\Editable;
use Framadate\Exception\AlreadyExistsException;
use Framadate\Exception\ConcurrentEditionException;
use Framadate\Exception\ConcurrentVoteException;
use Framadate\Message;
use Framadate\I18nWrapper;
use Framadate\Security\Token;
use Framadate\Services\InputService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Framadate\Utils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

class VoteController
{

    const GO_TO_STEP_2 = 'gotostep2';
    /**
     * @var PollService
     */
    protected $poll_service;

    /**
     * @var UrlGenerator
     */
    protected $url_generator;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * @var I18nWrapper
     */
    protected $i18n;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var NotificationService
     */
    protected $input_service;

    /**
     * @var NotificationService
     */
    protected $notification_service;

    protected $app_config;

    /**
     * PollController constructor.
     * @param PollService $poll_service
     * @param UrlGenerator $url_generator
     * @param Twig_Environment $twig
     * @param I18nWrapper $i18n
     * @param Session $session
     * @param InputService $input_service
     * @param NotificationService $notification_service
     * @param $app_config
     */
    public function __construct(PollService $poll_service,
                                UrlGenerator $url_generator,
                                Twig_Environment $twig,
                                I18nWrapper $i18n,
                                Session $session,
                                InputService $input_service,
                                NotificationService $notification_service,
                                $app_config)
    {
        $this->poll_service = $poll_service;
        $this->url_generator = $url_generator;
        $this->twig = $twig;
        $this->i18n = $i18n;
        $this->session = $session;
        $this->input_service = $input_service;
        $this->notification_service = $notification_service;
        $this->app_config = $app_config;
    }

    /**
     * @param Request $request
     * @param $poll_id
     * @return string|RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function voteAction(Request $request, $poll_id)
    {
        $message = null;
        if (empty($poll_id)) {
            return new RedirectResponse($this->url_generator->generate('home'));
        }

        $poll = $this->poll_service->findById($poll_id);

        if (!$poll) {
            return $this->twig->render(
                'error.twig',
                [
                    'title' => $this->i18n->get('Error', 'This poll doesn\'t exist !'),
                    'error' => $this->i18n->get('Error', 'This poll doesn\'t exist !'),
                ]
            );
        }

        // TODO : verify password and granted

        $name = $this->input_service->filterName($request->get('name', null));
        $choices = $this->input_service->filterArray($request->get('choices', []), FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);
        $slots_hash = $this->input_service->filterMD5($request->get('control', null));

        if ($name === null) {
            $message = new Message('danger', $this->i18n->get('Error', 'The name is invalid.'));
        }
        if (count($choices) !== count($request->get('choices', []))) {
            $message = new Message('danger', $this->i18n->get('Error', 'There is a problem with your choices'));
        }

        if ($message === null) {
            // Add vote
            try {
                $result = $this->poll_service->addVote($poll_id, $name, $choices, $slots_hash);
                if ($result) {
                    if ($poll->getEditable() === Editable::EDITABLE_BY_OWN) {
                        $editedVoteUniqueId = $result->getUniqId();
                        $this->getMessageForOwnVoteEditableVote($editedVoteUniqueId, $poll_id, $name);
                    } else {
                        $this->session->getFlashBag()->add('success', $this->i18n->get('studs', 'Adding the vote succeeded'));
                    }
                    $this->notification_service->sendUpdateNotification($poll, NotificationService::ADD_VOTE, $name);
                } else {
                    $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'Adding vote failed'));
                }
            } catch (AlreadyExistsException $aee) {
                $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'You already voted'));
            } catch (ConcurrentEditionException $cee) {
                $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'Poll has been updated before you vote'));
            } catch (ConcurrentVoteException $cve) {
                $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', "Your vote wasn't counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry."));
            }
        }

        return new RedirectResponse($this->url_generator->generate('view_poll', ['poll_id' => $poll_id]));

    }

    /**
     * @param Request $request
     * @param $admin_poll_id
     * @return null|string|RedirectResponse
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function voteAdminAction(Request $request, $admin_poll_id)
    {
        if (strlen($admin_poll_id) !== 24) {
            // redirect to error page
            return null;
        }

        $poll = $this->poll_service->findByAdminId($admin_poll_id);
        return $this->voteAction($request, $poll->getId());
    }

    /**
     * @param Request $request
     * @param $poll_id
     * @param $vote_uniq_id
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editVoteAction(Request $request, $poll_id, $vote_uniq_id)
    {
        if (empty($poll_id)) {
            // return to previous page
            return null;
        }
        $poll = $this->poll_service->findById($poll_id);

        if (!$poll) {
            return $this->twig->render('error.twig', [
                'error' => $this->i18n->get('Error', 'This poll doesn\'t exist !'),
            ]);
        }

        $choices = $request->get('choices', null);
        if ($choices === null) {
            return new RedirectResponse($this->url_generator->generate('view_poll', ['poll_id' => $poll_id]));
        }

        $name = $this->input_service->filterName($request->get('name'));
        $editedVote = filter_input(INPUT_POST, 'save', FILTER_VALIDATE_INT);
        $choices = $this->input_service->filterArray($choices, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => CHOICE_REGEX]]);
        $slots_hash = $this->input_service->filterMD5($request->get('control'));

        if (empty($editedVote)) {
            $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'Something is going wrong...'));
        }
        if (count($choices) !== count($_POST['choices'])) {
            $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'There is a problem with your choices'));
        }

        if ($message === null) {
            // Update vote
            try {
                $result = $this->poll_service->updateVote($poll_id, $editedVote, $name, $choices, $slots_hash);
                if ($result) {
                    if ($poll->getEditable() === Editable::EDITABLE_BY_OWN) {
                        $this->getMessageForOwnVoteEditableVote($vote_uniq_id, $poll_id, $name);
                    } else {
                        $this->session->getFlashBag()->add('success', $this->i18n->get('studs', 'Update vote succeeded'));
                    }
                    $this->notification_service->sendUpdateNotification($poll, NotificationService::UPDATE_VOTE, $name);
                } else {
                    $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'Update vote failed'));
                }
            } catch (ConcurrentEditionException $cee) {
                $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', 'Poll has been updated before you vote'));
            } catch (ConcurrentVoteException $cve) {
                $this->session->getFlashBag()->add('danger', $this->i18n->get('Error', "Your vote wasn't counted, because someone voted in the meantime and it conflicted with your choices and the poll conditions. Please retry."));
            }
        }

        return new RedirectResponse($this->url_generator->generate('view_poll', ['poll_id' => $poll_id]));
    }

    private function getMessageForOwnVoteEditableVote($editedVoteUniqueId, $poll_id, $name) {
        $this->session->set(USER_REMEMBER_VOTES_KEY . $poll_id, $editedVoteUniqueId);
        $urlEditVote = $this->url_generator->generate('edit_vote_poll', ['poll_id' => $poll_id, 'vote_uniq_id' => $editedVoteUniqueId], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->session->getFlashBag()->add('success', $this->i18n->get('studs', 'Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'));

        $this->session->getFlashBag()->add('info', $urlEditVote);

        /*
         * $message = new Message(
            'success',
            $this->i18n->get('studs', 'Your vote has been registered successfully, but be careful: regarding this poll options, you need to keep this personal link to edit your own vote:'),
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
