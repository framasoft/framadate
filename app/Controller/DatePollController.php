<?php

namespace Framadate\Controller;

use DateTime;
use Framadate\Choice;
use Framadate\I18nWrapper;
use Framadate\Poll;
use Framadate\Services\MailService;
use Framadate\Services\PollService;
use Framadate\Services\PurgeService;
use Framadate\Utils;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Twig_Environment;

class DatePollController extends PollController
{
    /**
     * @var MailService
     */
    private $mail_service;

    /**
     * @var PurgeService
     */
    private $purge_service;

    public function __construct(PollService $poll_service,
                                UrlGenerator $url_generator,
                                MailService $mail_service,
                                PurgeService $purge_service,
                                Twig_Environment $twig,
                                I18nWrapper $i18n,
                                Session $session,
                                FormFactory $form_factory,
                                $app_config
    ) {
        parent::__construct($poll_service, $url_generator, $twig, $i18n, $session, $form_factory, $app_config);
        $this->mail_service = $mail_service;
        $this->purge_service = $purge_service;
    }

    public function createPollActionStepTwo(Request $request)
    {

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

        try {
            return $this->twig->render(
                'create_date_poll_step_2.twig',
                [
                    'title' => $this->i18n->get('Step 2 date', 'Poll dates (2 on 3)'),
                    'choices' => $poll->getChoices(),
                    'error' => null,
                    'poll' => $poll,
                ]
            );
        } catch (\Twig_Error $e) {
            // log exception
            return null;
        }
    }

    public function createPollActionStepThree()
    {
        $max_expiry_time = $this->poll_service->maxExpiryDate();

        /** @var Poll $poll */
        $poll = $this->session->get('form');
        // Step 3/4 : Confirm poll creation

        // Handle Step2 submission
        if (!empty($_POST['days'])) {
            // Remove empty dates
            $_POST['days'] = array_filter($_POST['days'], function ($d) {
                return !empty($d);
            });

            // Check if there are at most MAX_SLOTS_PER_POLL slots
            if (count($_POST['days']) > MAX_SLOTS_PER_POLL) {
                // Display step 2
                return $this->twig->render('create_date_poll_step_2.twig', [
                    'title', $this->i18n->get('Step 2 date', 'Poll dates (2 on 3)'),
                    'choices', $poll->getChoices(),
                    'error', __f('Error', 'You can\'t select more than %d dates', MAX_SLOTS_PER_POLL),
                ]);
            }

            // Clear previous choices
            $poll->clearChoices();

            // Reorder moments to deal with suppressed dates
            $moments = [];
            $i = 0;
            while(count($moments) < count($_POST['days'])) {
                if (!empty($_POST['horaires' . $i])) {
                    $moments[] = $_POST['horaires' . $i];
                }
                $i++;
            }

            for ($i = 0; $i < count($_POST['days']); $i++) {
                $day = $_POST['days'][$i];

                if (!empty($day)) {
                    // Add choice to Form data
                    $date = DateTime::createFromFormat($this->i18n->get('Date', 'datetime_parseformat'), $_POST['days'][$i])->setTime(0, 0, 0);
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

        $end_date_str = utf8_encode(strftime($this->i18n->get('Date', 'DATE'), $max_expiry_time)); // textual date

        try {
            return $this->twig->render(
                'create_classic_poll_step_3.twig',
                [
                    'title' => $this->i18n->get('Step 3', 'Removal date and confirmation (3 on 3)'),
                    'choices' => $choices,
                    'poll_type' => $poll->getChoixSondage(),
                    'end_date_str' => $end_date_str,
                    'default_poll_duration' => $this->app_config['default_poll_duration'],
                    'use_smtp' => $this->app_config['use_smtp'],
                ]
            );
        } catch (\Twig_Error $e) {
            var_dump($e->getMessage());
            return null;
        }
    }

    public function createPollFinalAction(Request $request)
    {
        $min_expiry_time = $this->poll_service->minExpiryDate();
        $max_expiry_time = $this->poll_service->maxExpiryDate();

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

        var_dump($poll);
        // Insert poll in database
        $ids = $this->poll_service->createPoll($poll);
        $poll_id = $ids[0];
        $admin_poll_id = $ids[1];

        // Send confirmation by mail if enabled
        if ($this->app_config['use_smtp'] === true) {
            $message = __('Mail', "This is the message you have to send to the people you want to poll. \nNow, you have to send this message to everyone you want to poll.");
            $message .= '<br/><br/>';
            $message .= Utils::htmlEscape($poll->getAdminName()) . ' ' . __('Mail', 'hast just created a poll called') . ' : "' . Utils::htmlEscape($poll->getTitle()) . '".<br/>';
            $message .= __('Mail', 'Thanks for filling the poll at the link above') . ' :<br/><br/><a href="%1$s">%1$s</a>';

            $message_admin = __('Mail', "This message should NOT be sent to the polled people. It is private for the poll's creator.\n\nYou can now modify it at the link above");
            $message_admin .= ' :<br/><br/><a href="%1$s">%1$s</a>';

            $message = sprintf($message, Utils::getUrlSondage($poll_id));
            $message_admin = sprintf($message_admin, Utils::getUrlSondage($admin_poll_id, true));

            if ($this->mail_service->isValidEmail($poll->getAdminMail())) {
                $this->mail_service->send($poll->getAdminMail(), '[' . NOMAPPLICATION . '][' . __('Mail', 'Author\'s message') . '] ' . __('Generic', 'Poll') . ': ' . Utils::htmlEscape($poll->getTitle()), $message_admin);
                $this->mail_service->send($poll->getAdminMail(), '[' . NOMAPPLICATION . '][' . __('Mail', 'For sending to the polled users') . '] ' . __('Generic', 'Poll') . ': ' . Utils::htmlEscape($poll->getTitle()), $message);
            }
        }

        // Clean Form data in $_SESSION
        unset($_SESSION['form']);

        // Delete old polls
        $this->purge_service->purgeOldPolls();

        // Redirect to poll administration
        return new RedirectResponse($this->url_generator->generate('view_admin_poll', ['admin_poll_id' => $admin_poll_id]));
    }

    /**
     * This method filter an array calling "filter_var" on each items.
     * Only items validated are added at their own indexes, the others are not returned.
     * @param array $arr The array to filter
     * @param int $type The type of filter to apply
     * @param array|null $options The associative array of options
     * @return array The filtered array
     */
    private function filterArray(array $arr, $type, $options = null) {
        $newArr = [];

        foreach($arr as $id=>$item) {
            $item = filter_var($item, $type, $options);
            if ($item !== false) {
                $newArr[$id] = $item;
            }
        }

        return $newArr;
    }
}
