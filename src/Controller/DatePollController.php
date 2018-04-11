<?php

namespace Framadate\Controller;

use Framadate\Entity\DateChoice;
use Framadate\Entity\Moment;
use Framadate\Form\ArchiveType;
use Framadate\Entity\Poll;
use Framadate\Form\PollDateChoicesType;
use Framadate\Services\MailService;
use Framadate\Services\PollService;
use Framadate\Services\PurgeService;
use Framadate\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class DatePollController extends Controller
{
    /**
     * @var PollService
     */
    private $poll_service;

    /**
     * @var TranslatorInterface
     */
    private $i18n;

    /**
     * @var MailService
     */
    private $mail_service;

    /**
     * @var PurgeService
     */
    private $purge_service;

    /**
     * @var Session
     */
    private $session;

    public function __construct(
        PollService $poll_service,
                                MailService $mail_service,
                                PurgeService $purge_service,
                                TranslatorInterface $i18n,
                                SessionInterface $session
    ) {
        $this->poll_service = $poll_service;
        $this->i18n = $i18n;
        $this->mail_service = $mail_service;
        $this->purge_service = $purge_service;
        $this->session = $session;
    }

    /**
     * @Route("/p/new/date/2", name="new_date_poll_step_2")
     *
     * @param Request $request
     * @return Response
     */
    public function createPollActionStepTwo(Request $request)
    {
        /** @var Poll $poll */
        $poll = $this->session->get('form');

        // The poll format is DATE
        if ($poll->getFormat() !== 'D') {
            $poll->setFormat('D');
        }

        // Step 2/4 : Select dates of the poll

        // Prefill form->choices
        foreach ($poll->getChoices() as $choice) {
            /** @var DateChoice $choice */
            $count = 3 - count($choice->getMoments());
            for ($i = 0; $i < $count; $i++) {
                $choice->addMoment(new Moment(''));
            }
        }

        $count = 3 - count($poll->getChoices());
        for ($i = 0; $i < $count; $i++) {
            $choice = new DateChoice();
            $choice->addMoment(new Moment(''));
            $choice->addMoment(new Moment(''));
            $choice->addMoment(new Moment(''));
            $poll->addChoice($choice);
        }

        $form = $this->createForm(PollDateChoicesType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle Step2 submission

            $poll->clearEmptyChoices();

            $poll->sortChoices();
            return $this->redirectToRoute('new_date_poll_step_3');
        }

        // Display step 2

        return $this->render(
            'create_date_poll_step_2.twig',
            [
                'title' => $this->i18n->trans('Step 2 date.Poll dates (2 on 3)'),
                'choices' => $poll->getChoices(),
                'error' => null,
                'poll' => $poll,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/p/new/date/3", name="new_date_poll_step_3")
     *
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\DBALException
     */
    public function createPollActionStepThree(Request $request)
    {
        $min_expiry_time = $this->poll_service->minExpiryDate();
        $max_expiry_time = $this->poll_service->maxExpiryDate();

        /** @var Poll $poll */
        $poll = $this->session->get('form');

        $form = $this->createForm(ArchiveType::class, $poll);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Poll $poll */
            $poll = $this->session->get('form');

            // Step 4 : Data prepare before insert in DB

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

            // TODO : Probably useless
            $poll->clearEmptyChoices();
            // Insert poll in database
            $poll = $this->poll_service->createPoll($poll);

            // Send confirmation by mail if enabled
            if ($this->getParameter('app_use_smtp') === true) {
                $message = $this->render('mail/creation.twig', [
                    'poll' => $poll,
                    'admin' => false,
                ]);

                $message_admin = $this->render('mail/creation.twig', [
                    'poll' => $poll,
                    'admin' => true,
                ]);

                if ($this->mail_service->isValidEmail($poll->getAdminMail())) {
                    $this->mail_service->send($poll->getAdminMail(), '[' . $this->getParameter('app_name') . '][' . $this->i18n->trans('Mail.Author\'s message') . '] ' . $this->i18n->trans('Generic.Poll') . ': ' . Utils::htmlEscape($poll->getTitle()), $message_admin);
                    $this->mail_service->send($poll->getAdminMail(), '[' . $this->getParameter('app_name') . '][' . $this->i18n->trans('Mail.For sending to the polled users') . '] ' . $this->i18n->trans('Generic.Poll') . ': ' . Utils::htmlEscape($poll->getTitle()), $message);
                }
            }

            // Clean Form data in session
            $this->session->remove('form');

            // Delete old polls
            $this->purge_service->purgeOldPolls(60);

            $this->session->getFlashBag()->add('success', $this->i18n->trans('adminstuds.The poll is created.'));

            // Redirect to poll administration
            return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
        }

        $max_expiry_time = $this->poll_service->maxExpiryDate();

        // Step 3/4 : Confirm poll creation



        // Display step 3
        $choices = $poll->getChoices();

        return $this->render(
            'create_classic_poll_step_3.twig',
            [
                'title' => $this->i18n->trans('Step 3.Removal date and confirmation (3 on 3)'),
                'choices' => $choices,
                'poll_type' => $poll->getChoixSondage(),
                'default_poll_duration' => $this->getParameter('app_default_poll_duration'), // $this->app_config['default_poll_duration'],
                'use_smtp' => true, // $this->app_config['use_smtp'],
                'form' => $form->createView(),
            ]
        );
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
