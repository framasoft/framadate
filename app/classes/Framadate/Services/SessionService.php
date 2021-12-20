<?php

namespace Framadate\Services;

class SessionService {
    /**
     * Get value of $key in $section, or $defaultValue
     *
     * @param $section
     * @param $key
     * @param null $defaultValue
     * @return mixed
     */
    public function get($section, $key, $defaultValue=null) {
        assert(!empty($key));
        assert(!empty($section));

        $this->initSectionIfNeeded($section);

        return $_SESSION[$section][$key] ?? $defaultValue;
    }

    /**
     * Set a $value for $key in $section
     *
     * @param $section
     * @param $key
     * @param $value
     */
    public function set($section, $key, $value): void
    {
        assert(!empty($key));
        assert(!empty($section));

        $this->initSectionIfNeeded($section);

        $_SESSION[$section][$key] = $value;
    }

    /**
     * Remove a session value
     *
     * @param $section
     * @param $key
     */
    public function remove($section, $key): void
    {
        assert(!empty($key));
        assert(!empty($section));

        unset($_SESSION[$section][$key]);
    }

    private function initSectionIfNeeded($section): void
    {
        if (!isset($_SESSION[$section])) {
            $_SESSION[$section] = [];
        }
    }
}
