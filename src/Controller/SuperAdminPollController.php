<?php

namespace Framadate\Controller;

use Framadate\Services\AdminPollService;
use Framadate\Services\InputService;
use Framadate\Services\NotificationService;
use Framadate\Services\PollService;
use Psr\Log\LoggerInterface;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class SuperAdminPollController extends Controller
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
     * @Route("/admin", name="admin_view")
     */
    public function adminHomeAction()
    {
        return $this->render('admin/index.twig', [
            'title' => $this->i18n->trans('Admin.Administration'),
        ]);
    }

    /**
     * @Route("/admin/migrate", name="admin_migrate")
     *
     * @param KernelInterface $kernel
     * @return Response
     * @throws \Exception
     */
    public function adminMigrationAction(KernelInterface $kernel)
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
                                    'command' => 'doctrine:migrations:migrate',
                                ]);
        $input->setInteractive(false);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_NORMAL,
            true // true for decorated
        );
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $converter = new AnsiToHtmlConverter();
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return $this->render('admin/migration.twig', [
            'output' => $converter->convert($content),
            'title' => $this->i18n->trans('Admin.Migration'),
        ]);
    }
}
