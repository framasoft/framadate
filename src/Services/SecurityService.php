<?php
namespace Framadate\Services;

use Framadate\Entity\Poll;
use Framadate\Security\PasswordHasher;
use Framadate\Security\Token;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityService {

    /**
     * @var Session
     */
    protected $session;

    /**
     * SecurityService constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }

    /**
     * Get a CSRF token by name, or (re)create it.
     *
     * It creates a new token if :
     * <ul>
     *  <li>There no token with the given name in session</li>
     *  <li>The token time is in past</li>
     * </ul>
     *
     * @param $tokan_name string The name of the CSRF token
     * @return Token The token
     */
    function getToken($tokan_name) {
        $tokens = $this->session->get('tokens', []);

        if (!isset($tokens[$tokan_name]) || $tokens[$tokan_name]->isGone()) {
            $tokens[$tokan_name] = new Token();
            $this->session->set('tokens', $tokens);
        }

        return $tokens[$tokan_name]->getValue();
    }

    /**
     * Check if a given value is corresponding to the token in session.
     *
     * @param $tokan_name string Name of the token
     * @param $csrf string Value to check
     * @return bool true if the token is well checked
     */
    public function checkCsrf($tokan_name, $csrf) {
        $tokens = $this->session->get('tokens', [$tokan_name => new Token()]);
        $checked = $tokens[$tokan_name]->getValue() === $csrf;

        if ($checked) {
            unset($tokens[$tokan_name]);
            $this->session->set('tokens', $checked);
        }

        return $checked;
    }

    /**
     * Verify if the current session allows to access given poll.
     *
     * @param Poll $poll The poll which we seek access
     * @return bool true if the current session can access this poll
     */
    public function canAccessPoll(Poll $poll) {
        if (is_null($poll->getPasswordHash())) {
            return true;
        }

        $this->ensureSessionPollSecurityIsCreated();

        $poll_security = $this->session->get('poll_security', []);
        $currentPassword = isset($poll_security[$poll->getId()]) ? $poll_security[$poll->getId()] : null;
        if (!empty($currentPassword) && PasswordHasher::verify($currentPassword, $poll->getPasswordHash())) {
            return true;
        }

        unset($poll_security[$poll->getId()]);
        $this->session->set('poll_security', $poll_security);
        return false;
    }

    /**
     * Submit to the session a poll password
     *
     * @param Poll $poll The poll which we seek access
     * @param string $password The password to compare
     */
    public function submitPollAccess(Poll $poll, $password) {
        $poll_security = $this->session->get('poll_security', []);
        if (!empty($password)) {
            $this->ensureSessionPollSecurityIsCreated();
            $poll_security[$poll->getId()] = $password;
            $this->session->set('poll_security', $poll_security);
        }
    }

    private function ensureSessionPollSecurityIsCreated() {
        if (!$this->session->has('poll_security')) {
            $this->session->set('poll_security', []);
        }
    }
}
