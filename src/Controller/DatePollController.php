<?php

namespace Framadate\Controller;

use DateTime;
use Framadate\Entity\Choice;
use Framadate\Form\ArchiveType;
use Framadate\Entity\Poll;
use Framadate\Services\MailService;
use Framadate\Services\PollService;
use Framadate\Services\PurgeService;
use Framadate\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var SessionInterface
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
     * @Route("/new/date/2", name="new_date_poll_step_2")
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
            $poll->clearChoices();
        }

        // Step 2/4 : Select dates of the poll

        // Prefill form->choices
        foreach ($poll->getChoices() as $c) {
            /** @var Choice $c */
            $count = 3 - count($c->getSlots());
            for ($i = 0; $i < $count; $i++) {
                $c->addSlot('');
            }
        }

        $count = 3 - count($poll->getChoices());
        for ($i = 0; $i < $count; $i++) {
            $c = new Choice('');
            $c->addSlot('');
            $c->addSlot('');
            $c->addSlot('');
            $poll->addChoice($c);
        }

        // Display step 2

        return $this->render(
            'create_date_poll_step_2.twig',
            [
                'title' => $this->i18n->trans('Step 2 date.Poll dates (2 on 3)'),
                'choices' => $poll->getChoices(),
                'error' => null,
                'poll' => $poll,
            ]
        );
    }

    /**
     * @Route("/new/date/3", name="new_date_poll_step_3")
     *
     * @param Request $request
     * @return Response
     * @throws \Doctrine\DBAL\ConnectionException
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

            // var_dump($poll);
            // Insert poll in database
            $poll = $this->poll_service->createPoll($poll);

            // Send confirmation by mail if enabled
            if (/*$this->app_config['use_smtp']*/false === true) {
                $message = __('Mail', "This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll.");
                $message .= '<br/><br/>';
                $message .= Utils::htmlEscape($poll->getAdminName()) . ' ' . __('Mail', 'hast just created a poll called') . ' : "' . Utils::htmlEscape($poll->getTitle()) . '".<br/>';
                $message .= __('Mail', 'Thanks for filling the poll at the link above') . ' :<br/><br/><a href="%1$s">%1$s</a>';

                $message_admin = __('Mail', "This message should NOT be sent to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above");
                $message_admin .= ' :<br/><br/><a href="%1$s">%1$s</a>';

                $message = sprintf($message, Utils::getUrlSondage($poll->getId()));
                $message_admin = sprintf($message_admin, Utils::getUrlSondage($poll->getAdminId(), true));

                if ($this->mail_service->isValidEmail($poll->getAdminMail())) {
                    $this->mail_service->send($poll->getAdminMail(), '[' . NOMAPPLICATION . '][' . $this->i18n->trans('Mail.Author\'s message') . '] ' . $this->i18n->trans('Generic.Poll') . ': ' . Utils::htmlEscape($poll->getTitle()), $message_admin);
                    $this->mail_service->send($poll->getAdminMail(), '[' . NOMAPPLICATION . '][' . $this->i18n->trans('Mail.For sending to the polled users') . '] ' . $this->i18n->trans('Generic.Poll') . ': ' . Utils::htmlEscape($poll->getTitle()), $message);
                }
            }

            // Clean Form data in session
            $this->session->remove('form');

            // Delete old polls
            $this->purge_service->purgeOldPolls(60);

            // Redirect to poll administration
            return $this->redirectToRoute('view_admin_poll', ['admin_poll_id' => $poll->getAdminId()]);
        }

        $max_expiry_time = $this->poll_service->maxExpiryDate();

        // Step 3/4 : Confirm poll creation

        // Handle Step2 submission
        if (!empty($request->get('days'))) {
            // Remove empty dates
            $days = array_filter($request->get('days'), function ($d) {
                return !empty($d);
            });

            // Check if there are at most MAX_SLOTS_PER_POLL slots
            if (count($days) > 366) {
                // Display step 2
                return $this->render('create_date_poll_step_2.twig', [
                    'title', $this->i18n->trans('Step 2 date.Poll dates (2 on 3)'),
                    'choices', $poll->getChoices(),
                    'error', $this->i18n->trans('Error.You can\'t select more than %d dates', ['%d' => MAX_SLOTS_PER_POLL]),
                ]);
            }

            // Clear previous choices
            $poll->clearChoices();

            // Reorder moments to deal with suppressed dates
            $moments = [];
            $i = 0;
            while (count($moments) < count($days)) {
                if (!empty($request->get('horaires' . $i))) {
                    $moments[] = $request->get('horaires' . $i);
                }
                $i++;
            }

            for ($i = 0; $i < count($days); $i++) {
                $day = $days[$i];

                if (!empty($day)) {
                    // Add choice to Form data
                    $date = DateTime::createFromFormat('Y-m-d', $days[$i])->setTime(0, 0, 0);
                    $time = $date->getTimestamp();
                    $choice = new Choice($time);
                    $poll->addChoice($choice);

                    $schedules = $this->filterArray($moments[$i], FILTER_DEFAULT);
                    for ($j = 0; $j < count($schedules); $j++) {
                        if (!empty($schedules[$j])) {
                            $choice->addSlot(strip_tags($schedules[$j]));
                        }
                    }
                }
            }
            $poll->sortChoices();
        }

        // Display step 3
        $choices = $poll->getChoices();

        return $this->render(
            'create_classic_poll_step_3.twig',
            [
                'title' => $this->i18n->trans('Step 3.Removal date and confirmation (3 on 3)'),
                'choices' => $choices,
                'poll_type' => $poll->getChoixSondage(),
                'default_poll_duration' => 180, // $this->app_config['default_poll_duration'],
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
