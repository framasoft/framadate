<?php

namespace Framadate\Controller;

use Framadate\Form\PollType;
use Framadate\Entity\Poll;
use Framadate\Services\PollService;
use Framadate\Services\SecurityService;
use Framadate\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;

class PollController extends Controller
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
     * @var TranslatorInterface
     */
    protected $i18n;

    /**
     * @var SessionInterface
     */
    protected $session;

    protected $app_config;

    /**
     * PollController constructor.
     * @param PollService $poll_service
     * @param SecurityService $security_service
     * @param TranslatorInterface $i18n
     * @param SessionInterface $session
     */
    public function __construct(PollService $poll_service, SecurityService $security_service, TranslatorInterface $i18n, SessionInterface $session)
    {
        $this->poll_service = $poll_service;
        $this->security_service = $security_service;
        $this->i18n = $i18n;
        $this->session = $session;
    }

    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        $demoPoll = $this->poll_service->findById('aqg259dth55iuhwm');
        $nbcol = 3; //max($this->app_config['show_what_is_that'] + $this->app_config['show_the_software'] + $this->app_config['show_cultivate_your_garden'], 1);

        return $this->render('index.twig', [
            'show_what_is_that' => true, //$this->app_config['show_what_is_that'],
            'show_the_software' => true, // $this->app_config['show_the_software'],
            'show_cultivate_your_garden' => true, // $this->app_config['show_cultivate_your_garden'],
            'col_size' => 12 / $nbcol,
            'demo_poll' => $demoPoll,
            'title' => $this->i18n->trans('Generic.Make your polls'),
        ]);
    }

    /**
     * @param $poll_id
     * @return string
     *
     * @Route("/p/{poll_id}", name="view_poll", requirements={"poll_id": "^([a-zA-Z0-9-]*)$"})
     */
    public function showAction($poll_id)
    {
        $accessGranted = true;
        $resultPubliclyVisible = true;
        $message = '';
        $editingVoteId = 0;
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
            'editingVoteId' => $editingVoteId,
            'message' => $message,
            'admin' => false,
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
     *
     * @Route("/p/new/{type}", name="new_poll")
     */
    public function createPollAction(Request $request, $type)
    {
        /** @var Poll $poll */
        //$poll = $this->session->get('form', new Poll($type));
        $poll = new Poll();
        $poll->setChoixSondage($type);

        $form = $this->createForm(PollType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->session->set('form', $poll);
            if ($poll->getChoixSondage() === 'date') {
                return $this->redirectToRoute('new_date_poll_step_2');
            }
            return $this->redirectToRoute('new_classic_poll_step_2');
        }

        // $useRemoteUser = USE_REMOTE_USER && isset($_SERVER['REMOTE_USER']);
        // $this->session->set('form', $poll);

        return $this->render('create_poll.twig', [
            'title' => $this->i18n->trans('Step 1.Poll creation (1 on 3)'),
            'use_smtp' => true, // $this->app_config['use_smtp'],
            'default_to_markdown_editor' => true, //$this->app_config['markdown_editor_by_default'],
            // 'useRemoteUser' => $useRemoteUser,
            'poll' => $poll,
            'form' => $form->createView(),
            'base_url' => $request->getHost(),
        ]);
    }

    /**
     * @Route("/p/{poll_id}/export.{format}", name="export_poll", defaults={"format": "CSV"})
     *
     * @param $poll_id
     * @param $format
     */
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

    private function exportCSVPollAction(Poll $poll)
    {
        $slots = $this->poll_service->allSlotsByPoll($poll);
        $votes = $this->poll_service->allVotesByPollId($poll->getId());

        // CSV header
        if ($poll->getFormat() === 'D') {
            $titles_line = ',';
            $moments_line = ',';
            foreach ($slots as $slot) {
                $title = Utils::csvEscape(strftime($this->i18n->trans('Date.DATE'), $slot['title']));
                $moments = explode(',', $slot['moments']);

                $titles_line .= str_repeat($title . ',', count($moments));
                $moments_line .= implode(',', array_map('\Framadate\Utils::csvEscape', $moments)) . ',';
            }
            echo $titles_line . "\r\n";
            echo $moments_line . "\r\n";
        } else {
            echo ',';
            foreach ($slots as $slot) {
                echo Utils::markdown($slot['title'], true) . ',';
            }
            echo "\r\n";
        }
        // END - CSV header

        // Vote lines
        foreach ($votes as $vote) {
            echo Utils::csvEscape($vote['name']) . ',';
            $choices = str_split($vote['choices']);
            foreach ($choices as $choice) {
                switch ($choice) {
                    case 0:
                        $text = $this->i18n->trans('Generic.No');
                        break;
                    case 1:
                        $text = $this->i18n->trans('Generic.Ifneedbe');
                        break;
                    case 2:
                        $text = $this->i18n->trans('Generic.Yes');
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

    /**
     * @Route("/_locale", name="edit-locale", methods={"POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function changeLanguageAction(Request $request)
    {
        $locale = $request->get('locale', 'en');
        if (in_array($locale, array_keys([
                                             'fr' => 'Français',
                                             'en' => 'English',
                                             'es' => 'Español',
                                             'de' => 'Deutsch',
                                             'it' => 'Italiano',
                                             'br' => 'Brezhoneg',
                                         ]), true)) {
            $this->i18n->setLocale($locale);
            $this->session->set('_locale', $locale);
        }
        $url = $this->matchReferrer($request->headers->get('referer'), $request->getSchemeAndHttpHost());
        return $this->redirect($url);
    }

    /**
     * This function is used in case of false or wrong referer when changing locale
     *
     * @param string $referer
     * @param string $baseUrl
     * @return string
     */
    private function matchReferrer($referer, $baseUrl)
    {
        $router = $this->get('router');
        $url = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));
        if ($router->match($url)) {
            return $url;
        }
        return $this->generateUrl('home');
    }
}
