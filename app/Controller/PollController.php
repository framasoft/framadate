<?php

namespace Framadate\Controller;

use Framadate\Form\PollType;
use Framadate\Poll;
use Framadate\I18nWrapper;
use Framadate\Services\PollService;
use Framadate\Services\SecurityService;
use Framadate\Utils;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

class PollController
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
     * @var SecurityService
     */
    protected $security_service;

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
     * @var FormFactory
     */
    protected $form_factory;

    protected $app_config;

    /**
     * PollController constructor.
     * @param PollService $poll_service
     * @param UrlGenerator $url_generator
     * @param SecurityService $security_service
     * @param Twig_Environment $twig
     * @param I18nWrapper $i18n
     * @param Session $session
     * @param FormFactory $form_factory
     * @param $app_config
     */
    public function __construct(PollService $poll_service, UrlGenerator $url_generator, SecurityService $security_service, Twig_Environment $twig, I18nWrapper $i18n, Session $session, FormFactory $form_factory, $app_config)
    {
        $this->poll_service = $poll_service;
        $this->url_generator = $url_generator;
        $this->security_service = $security_service;
        $this->twig = $twig;
        $this->i18n = $i18n;
        $this->session = $session;
        $this->form_factory = $form_factory;
        $this->app_config = $app_config;
    }

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function indexAction()
    {
        $demoPoll = $this->poll_service->findById('aqg259dth55iuhwm');
        $nbcol = max($this->app_config['show_what_is_that'] + $this->app_config['show_the_software'] + $this->app_config['show_cultivate_your_garden'], 1 );

        return $this->twig->render('index.twig', [
            'show_what_is_that' => $this->app_config['show_what_is_that'],
            'show_the_software' => $this->app_config['show_the_software'],
            'show_cultivate_your_garden' => $this->app_config['show_cultivate_your_garden'],
            'col_size' => 12 / $nbcol,
            'demo_poll' => $demoPoll,
            'title' => $this->i18n->get('Generic', 'Make your polls'),
        ]);
    }

    /**
     * @param $poll_id
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showAction($poll_id)
    {
        $accessGranted = true;
        $resultPubliclyVisible = true;
        $message = '';
        $editingVoteId = 0;
        $editedVoteUniqueId = 0;
        $comments = [];

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

        // TODO : Add back $resultPubliclyVisible and $accessGranted

        // Retrieve data
        if ($resultPubliclyVisible || $accessGranted) {
            $slots = $this->poll_service->allSlotsByPoll($poll);
            $votes = $this->poll_service->allVotesByPollId($poll_id);
            $comments = $this->poll_service->allCommentsByPollId($poll_id);
        }

        return $this->twig->render('studs.twig', [
            'poll_id' => $poll_id,
            'poll' => $poll,
            'title' => $this->i18n->get('Generic', 'Poll') . ' - ' . $poll->getTitle(),
            'expired' => strtotime($poll->getEndDate()) < time(),
            'deletion_date' => strtotime($poll->getEndDate()) + PURGE_DELAY * 86400,
            'slots' => $poll->getFormat() === 'D' ? $this->poll_service->splitSlots($slots) : $slots,
            'slots_hash' =>  $this->poll_service->hashSlots($slots),
            'votes' => $this->poll_service->splitVotes($votes),
            'best_choices' => $this->poll_service->computeBestChoices($votes),
            'comments' => $comments,
            'editingVoteId' => $editingVoteId,
            'message' => $message,
            'admin' =>false,
            'hidden' => $poll->isHidden(),
            'accessGranted' => $accessGranted,
            'resultPubliclyVisible' => $resultPubliclyVisible,
            'editedVoteUniqueId' => $editedVoteUniqueId,
            'ValueMax' => $poll->getValueMax(),
        ]);
    }

    /**
     * @param Request $request
     * @param $type
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function createPollAction(Request $request, $type)
    {
        /** @var Poll $poll */
        //$poll = $this->session->get('form', new Poll($type));
        $poll = new Poll();
        $poll->setChoixSondage($type);

        $form = $this->form_factory->createBuilder(PollType::class, $poll, ['i18n' => $this->i18n])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->session->set('form', $poll);
            if ($poll->getChoixSondage() === 'date') {
                return new RedirectResponse($this->url_generator->generate('new_date_poll_step_2'));
            }
            return new RedirectResponse($this->url_generator->generate('new_classic_poll_step_2'));
        }

        // $useRemoteUser = USE_REMOTE_USER && isset($_SERVER['REMOTE_USER']);
        // $this->session->set('form', $poll);

        return $this->twig->render('create_poll.twig', [
            'title' => $this->i18n->get('Step 1', 'Poll creation (1 on 3)'),
            'use_smtp' => $this->app_config['use_smtp'],
            'default_to_markdown_editor' => $this->app_config['markdown_editor_by_default'],
            // 'useRemoteUser' => $useRemoteUser,
            'form' => $form->createView(),
            'base_url' => $request->getHost(),
        ]);

    }

    public function exportPollAction($poll_id, $format)
    {
        if (empty($poll_id)) {
            // redirect to previous page with referrer
            var_dump("empty poll id");
            return;
        }

        $poll = $this->poll_service->findById($poll_id);

        if (!$poll) {
            var_dump("no poll with this ID");
            // redirect to error page
            return;
        }

        $forbiddenBecauseOfPassword = !$poll->isResultsPubliclyVisible() && !$this->security_service->canAccessPoll($poll);
        $resultsAreHidden = $poll->isHidden();

        if ($resultsAreHidden || $forbiddenBecauseOfPassword) {
            // redirect to forbidden page
            var_dump("forbidden page");
            return;
        }

        switch ($format) {
            // TODO : Add more formats
            case 'CSV':
            default:
                $this->exportCSVPollAction($poll);
                break;
        }

    }

    private function exportCSVPollAction(Poll $poll) {

        $slots = $this->poll_service->allSlotsByPoll($poll);
        $votes = $this->poll_service->allVotesByPollId($poll->getId());

        // CSV header
        if ($poll->getFormat() === 'D') {
            $titles_line = ',';
            $moments_line = ',';
            foreach ($slots as $slot) {
                $title = Utils::csvEscape(strftime($this->i18n->get('Date', 'DATE'), $slot->title));
                $moments = explode(',', $slot->moments);

                $titles_line .= str_repeat($title . ',', count($moments));
                $moments_line .= implode(',', array_map('\Framadate\Utils::csvEscape', $moments)) . ',';
            }
            echo $titles_line . "\r\n";
            echo $moments_line . "\r\n";
        } else {
            echo ',';
            foreach ($slots as $slot) {
                echo Utils::markdown($slot->title, true) . ',';
            }
            echo "\r\n";
        }
        // END - CSV header

        // Vote lines
        foreach ($votes as $vote) {
            echo Utils::csvEscape($vote->name) . ',';
            $choices = str_split($vote->choices);
            foreach ($choices as $choice) {
                switch ($choice) {
                    case 0:
                        $text = $this->i18n->get('Generic', 'No');
                        break;
                    case 1:
                        $text = $this->i18n->get('Generic', 'Ifneedbe');
                        break;
                    case 2:
                        $text = $this->i18n->get('Generic', 'Yes');
                        break;
                    default:
                        $text = 'unkown';
                }
                echo Utils::csvEscape($text);
                echo ',';
            }
            echo "\r\n";
        }
        // END - Vote lines

        // HTTP headers
        $content = ob_get_clean();
        $filesize = strlen($content);
        $filename = Utils::cleanFilename($poll->getTitle()) . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Length: ' . $filesize);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=10');
        // END - HTTP headers

        echo $content;
    }
}
