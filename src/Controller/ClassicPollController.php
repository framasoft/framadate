<?php

namespace Framadate\Controller;

use Doctrine\DBAL\DBALException;
use Framadate\Entity\Choice;
use Framadate\Entity\Poll;
use Framadate\Form\ArchiveType;
use Framadate\Services\MailService;
use Framadate\Services\PollService;
use Framadate\Services\PurgeService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class ClassicPollController extends AbstractController
{
    /**
     * @var PollService
     */
    private $poll_service;

    /**
     * @var MailService
     */
    private $mail_service;

    /**
     * @var PurgeService
     */
    private $purge_service;

    /**
     * @var TranslatorInterface
     */
    private $i18n;

    private $logger;

    /**
     * @var Session
     */
    private $session;

    public function __construct(
        PollService $poll_service,
                                MailService $mail_service,
                                PurgeService $purge_service,
                                TranslatorInterface $i18n,
                                LoggerInterface $logger,
                                SessionInterface $session
    ) {
        $this->poll_service = $poll_service;
        $this->i18n = $i18n;
        $this->logger = $logger;
        $this->session = $session;
        $this->mail_service = $mail_service;
        $this->purge_service = $purge_service;
    }

    /**
     * @Route("/new/classic/2", name="new_classic_poll_step_2")
     *
     * @param Request $request
     * @return null
     */
    public function createPollActionStepTwo(Request $request)
    {

        /** @var Poll $poll */
        $poll = $this->session->get('form');
        var_dump($poll);

        // Display step 2
        $choices = $poll->getChoices();
        $nb_choices = count($choices);
        if ($nb_choices < 5) {
            $choices = array_merge($choices, array_fill($nb_choices, 5 - $nb_choices, new Choice()));
        }
        $poll->setChoices($choices);

        return $this->render(
            'create_classic_poll_step_2.twig',
                [
                    'title' => $this->i18n->trans('Step 2 date.Poll dates (2 on 3)'),
                    'choices' => $poll->getChoices(),
                    'error' => null,
                    'poll' => $poll,
                    //'config' => $this->app_config,
                ]
            );
    }

    /**
     * @Route("/new/classic/3", name="new_classic_poll_step_3")
     *
     * @param Request $request
     * @return string
     */
    public function createPollActionStepThree(Request $request)
    {
        $max_expiry_time = $this->poll_service->maxExpiryDate();

        /** @var Poll $poll */
        $poll = $this->session->get('form');

        $form = $this->createForm(ArchiveType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $min_expiry_time = $this->poll_service->minExpiryDate();
            $max_expiry_time = $this->poll_service->maxExpiryDate();

            /** @var Poll $poll */
            $poll = $this->session->get('form');

            // Define expiration date
            $enddate = filter_input(INPUT_POST, 'enddate', FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#']]);

            if (!empty($enddate)) {
                $registredate = explode('/', $enddate);

                if (is_array($registredate) && count($registredate) === 3) {
                    $time = mktime(0, 0, 0, $registredate[1], $registredate[0], $registredate[2]);

                    if ($time < $min_expiry_time) {
                        $poll->setEndDate($min_expiry_time);
                    } elseif ($max_expiry_time < $time) {
                        $poll->setEndDate($max_expiry_time);
                    } else {
                        $poll->setEndDate($time);
                    }
                }
            }

            if (empty($poll->getEndDate())) {
                // By default, expiration date is 6 months after last day
                $poll->setEndDate($max_expiry_time);
            }

            // Insert poll in database
            try {
                $poll = $this->poll_service->createPoll($poll);

                // Send confirmation by mail if enabled
                if (/*$this->app_config['use_smtp']*/
                    false === true) {
                    $message = __(
                        'Mail',
                        "This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll."
                    );
                    $message .= '<br/><br/>';
                    $message .= Utils::htmlEscape($poll->getAdminName()) . ' ' . __(
                            'Mail',
                            'hast just created a poll called'
                        ) . ' : "' . Utils::htmlEscape($poll->getTitle()) . '".<br/>';
                    $message .= __(
                            'Mail',
                            'Thanks for filling the poll at the link above'
                        ) . ' :<br/><br/><a href="%1$s">%1$s</a>';

                    $message_admin = __(
                        'Mail',
                        "This message should NOT be sent to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above"
                    );
                    $message_admin .= ' :<br/><br/><a href="%1$s">%1$s</a>';

                    $message = sprintf($message, Utils::getUrlSondage($poll->getId()));
                    $message_admin = sprintf($message_admin, Utils::getUrlSondage($poll->getAdminId(), true));

                    if ($this->mail_service->isValidEmail($poll->getAdminMail())) {
                        $this->mail_service->send(
                            $poll->getAdminMail(),
                            '[' . NOMAPPLICATION . '][' . $this->i18n->trans(
                                'Mail.Author\'s message'
                            ) . '] ' . $this->i18n->trans(
                                'Generic.Poll'
                            ) . ': ' . Utils::htmlEscape($poll->getTitle()),
                            $message_admin
                        );
                        $this->mail_service->send(
                            $poll->getAdminMail(),
                            '[' . NOMAPPLICATION . '][' . $this->i18n->trans(
                                'Mail.For sending to the polled users'
                            ) . '] ' . $this->i18n->trans(
                                'Generic.Poll'
                            ) . ': ' . Utils::htmlEscape($poll->getTitle()),
                            $message
                        );
                    }
                }

                // Clean Form data in session
                $this->session->remove('form');

                // Delete old polls
                $this->purge_service->purgeOldPolls(60);

                // Redirect to poll administration
                return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
            } catch (DBALException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        // Store choices in $_SESSION
        $choices = $request->get('choices', null);
        if ($choices) {
            $poll->clearChoices();
            foreach ($choices as $choice) {
                if (!empty($choice)) {
                    $choice = strip_tags($choice);
                    $choice = new Choice($choice);
                    $poll->addChoice($choice);
                }
            }
        }

        // Expiration date is initialised with config parameter. Value will be modified in step 4 if user has defined an other date
        if (empty($poll->getEndDate())) {
            // By default, expiration date is 6 months after last day
            $poll->setEndDate($max_expiry_time);
        }

        return $this->render('create_classic_poll_step_3.twig', [
            'title' => $this->i18n->trans('Step 3.Removal date and confirmation (3 on 3)'),
            'choices' => $poll->getChoices(),
            'poll_type' => $poll->getChoixSondage(),
            'form' => $form->createView(),
            'default_poll_duration' => 180, // $this->app_config['default_poll_duration'],
            'use_smtp' => true, // $this->app_config['use_smtp'],
        ]);
    }

    /**
     * This method filter an array calling "filter_var" on each items.
     * Only items validated are added at their own indexes, the others are not returned.
     * @param array $arr The array to filter
     * @param int $type The type of filter to apply
     * @param array|null $options The associative array of options
     * @return array The filtered array
     */
    private function filterArray(array $arr, $type, $options = null)
    {
        $newArr = [];

        foreach ($arr as $id=>$item) {
            $item = filter_var($item, $type, $options);
            if ($item !== false) {
                $newArr[$id] = $item;
            }
        }

        return $newArr;
    }
}
