<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondate: Framasoft (https://github.com/framasoft)
 *
 * =============================
 *
 * Ce logiciel est régi par la licence CeCILL-B. Si une copie de cette licence
 * ne se trouve pas avec ce fichier vous pouvez l'obtenir sur
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 *
 * Auteurs de STUdS (projet initial) : Guilhem BORGHESI (borghesi@unistra.fr) et Raphaël DROZ
 * Auteurs de Framadate/OpenSondage : Framasoft (https://github.com/framasoft)
 */
namespace Framadate\Services;

/**
 * This class helps to clean all inputs from the users or external services.
 */
class InputService {

    function __construct() {}

    /**
     * This method filter an array calling "filter_var" on each items.
     * Only items validated are added at their own indexes, the others are not returned.
     * @param array $arr The array to filter
     * @param int $type The type of filter to apply
     * @param array|null $options The associative array of options
     * @return array The filtered array
     */
    function filterArray(array $arr, $type, $options = null) {
        $newArr = [];

        foreach($arr as $id=>$item) {
            $item = filter_var($item, $type, $options);
            if ($item !== false) {
                $newArr[$id] = $item;
            }
        }

        return $newArr;
    }

    function filterAllowedValues($value, array $allowedValues) {
        return in_array($value, $allowedValues, true) ? $value : null;
    }

    public function filterTitle($title) {
        return $this->returnIfNotBlank($title);
    }

    public function filterName($name) {
        $filtered = trim($name);
        return $this->returnIfNotBlank($filtered);
    }

    public function filterMail($mail) {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    public function filterDescription($description) {
        $description = str_replace("\r\n", "\n", $description);
        return $description;
    }

    public function filterMD5($control) {
        return filter_var($control, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => MD5_REGEX]]);
    }

    public function filterBoolean($boolean) {
        return !!filter_var($boolean, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_TRUE_REGEX]]);
    }

    public function filterEditable($editable) {
        return filter_var($editable, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => EDITABLE_CHOICE_REGEX]]);
    }

    public function filterComment($comment) {
        $comment = str_replace("\r\n", "\n", $comment);
        return $this->returnIfNotBlank($comment);
    }

    /**
     * Return the value if it's not blank.
     *
     * @param string $filtered The value
     * @return string|null
     */
    private function returnIfNotBlank($filtered) {
        if ($filtered) {
            $withoutSpaces = str_replace(' ', '', $filtered);
            if (!empty($withoutSpaces)) {
                return $filtered;
            }
        }

        return null;
    }

}