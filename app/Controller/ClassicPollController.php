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

class ClassicPollController extends PollController
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

        /** @var Poll $poll */
        $poll = $this->session->get('form');


        // Display step 2
        $choices = $poll->getChoices();
        $nb_choices = count($choices);
        if ($nb_choices < 5) {
            $choices = array_merge($choices, array_fill($nb_choices, 5 - $nb_choices, new Choice()));
        }
        $poll->setChoices($choices);

        try {
            return $this->twig->render(
                'create_classic_poll_step_2.twig',
                [
                    'title' => $this->i18n->get('Step 2 date', 'Poll dates (2 on 3)'),
                    'choices' => $poll->getChoices(),
                    'error' => null,
                    'poll' => $poll,
                    'config' => $this->app_config,
                ]
            );
        } catch (\Twig_Error $e) {
            // log exception
            var_dump($e->getMessage());
            return null;
        }
    }

    /**
     * @param Request $request
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function createPollActionStepThree(Request $request)
    {
        $max_expiry_time = $this->poll_service->maxExpiryDate();

        /** @var Poll $poll */
        $poll = $this->session->get('form');

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
        $poll->setEndDate($max_expiry_time);

        $end_date_str = utf8_encode(strftime($this->i18n->get('Date', 'DATE'), $max_expiry_time)); //textual date

        return $this->twig->render('create_classic_poll_step_3.twig', [
            'title' => $this->i18n->get('Step 3', 'Removal date and confirmation (3 on 3)'),
            'choices' => $poll->getChoices(),
            'poll_type' => $poll->getChoixSondage(),
            'end_date_str' => $end_date_str,
            'default_poll_duration' => $this->app_config['default_poll_duration'],
            'use_smtp' => $this->app_config['use_smtp'],
        ]);


    }

    public function createPollFinalAction(Request $request)
    {
        $min_expiry_time = $this->poll_service->minExpiryDate();
        $max_expiry_time = $this->poll_service->maxExpiryDate();

        /** @var Poll $poll */
        $poll = $this->session->get('form');

        // Step 4 : Data prepare before insert in DB


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
