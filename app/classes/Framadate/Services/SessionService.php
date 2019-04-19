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

        $returnValue = $defaultValue;
        if (isset($_SESSION[$section][$key])) {
            $returnValue = $_SESSION[$section][$key];
        }

        return $returnValue;
    }

    /**
     * Set a $value for $key in $section
     *
     * @param $section
     * @param $key
     * @param $value
     */
    public function set($section, $key, $value) {
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
    public function remove($section, $key) {
        assert(!empty($key));
        assert(!empty($section));

        unset($_SESSION[$section][$key]);
    }

    /**
     * Remove a session section
     *
     * @param $section
     */
    public function removeAll($section) {
        assert(!empty($section));

        unset($_SESSION[$section]);
    }

    private function initSectionIfNeeded($section) {
        if (!isset($_SESSION[$section])) {
            $_SESSION[$section] = [];
        }
    }
} 
