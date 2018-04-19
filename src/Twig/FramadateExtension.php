<?php

namespace Framadate\Twig;

use Framadate\Entity\Poll;
use Framadate\I18nWrapper;
use Framadate\Utils;
use Parsedown;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FramadateExtension extends AbstractExtension implements ExtensionInterface, GlobalsInterface
{
    /**
     * @var TranslatorInterface
     */
    private $i18n;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(TranslatorInterface $i18n, SessionInterface $session)
    {
        $this->i18n = $i18n;
        $this->session = $session;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('markdown', array($this, 'markdown')),
            new TwigFilter('base64_encode', 'base64_encode'),
            new TwigFilter('base64_decode', 'base64_decode'),
        );
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('__', [$this, 'trans']),
            new TwigFunction('poll_url', [$this, 'poll_url']),
        ];
    }

    public function getGlobals()
    {
        return [
            'APPLICATION_NAME' => 'framadate',
            'TITLE_IMAGE' => 'images/logo-framadate.png',
            'use_nav_js' => false, // strstr($_SERVER['SERVER_NAME'], 'framadate.org'));
            'locale' => $this->session->get('_locale', 'en'),
            'langs' => [
                'fr' => 'Français',
                'en' => 'English',
                'es' => 'Español',
                'de' => 'Deutsch',
                'it' => 'Italiano',
                'br' => 'Brezhoneg',
            ],
        ];
    }

    public function trans($section, $key, array $args = [])
    {
        return $this->i18n->trans($section.'.'.$key, $args);
    }

    public function poll_url($id, $admin = false, $action = false, $action_value = false, $vote_id = '')
    {
        $poll_id =  filter_var($id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => Poll::POLL_REGEX]]);
        $action = $action === false ?: Utils::htmlEscape($action);
        $action_value = $action_value === false ?: $action_value;
        $vote_unique_id = $vote_id === '' ?: filter_var($vote_id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => Poll::POLL_REGEX]]);

        // If filter_var fails (i.e.: hack tentative), it will return false. At least no leak is possible from this.

        return "";// Utils::getUrlSondage($poll_id, $admin, $vote_unique_id, $action, $action_value);
    }

    public function preg_match($pattern, $subject)
    {
        return preg_match($pattern, $subject);
    }

    public function addslashes_single_quote($string)
    {
        return addcslashes($string, '\\\'');
    }

    public function markdown($string)
    {
        $parsedown = new Parsedown();
        return $parsedown->text($string);
    }
}
