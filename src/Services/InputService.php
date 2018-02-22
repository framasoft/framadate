<?php
/**
 * This software is governed by the CeCILL-B license. If a copy of this license
 * is not distributed with this file, you can obtain one at
 * http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 *
 * Authors of STUdS (initial project): Guilhem BORGHESI (borghesi@unistra.fr) and Raphaël DROZ
 * Authors of Framadate/OpenSondage: Framasoft (https://github.com/framasoft)
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

use DateTime;
use Framadate\Entity\Choice;
use Framadate\Entity\Poll;

/**
 * This class helps to clean all inputs from the users or external services.
 */
class InputService
{
    public function __construct()
    {
    }

    /**
     * This method filter an array calling "filter_var" on each items.
     * Only items validated are added at their own indexes, the others are not returned.
     * @param array $arr The array to filter
     * @param int $type The type of filter to apply
     * @param array|null $options The associative array of options
     * @return array The filtered array
     */
    public function filterArray(array $arr, $type, $options = null)
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

    public function filterAllowedValues($value, array $allowedValues)
    {
        return in_array($value, $allowedValues, true) ? $value : null;
    }

    public function filterTitle($title)
    {
        return $this->returnIfNotBlank($title);
    }

    public function filterId($id)
    {
        $filtered = filter_var($id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => Poll::POLL_REGEX]]);
        return $filtered ? substr($filtered, 0, 64) : false;
    }

    public function filterName($name)
    {
        return $this->returnIfNotBlank(trim($name));
    }

    public function filterMail($mail)
    {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }

    public function filterDescription($description)
    {
        return str_replace("\r\n", "\n", $description);
    }

    public function filterMD5($control)
    {
        return filter_var($control, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => Choice::MD5_REGEX]]);
    }

    public function filterInteger($int)
    {
        if (filter_var($int, FILTER_VALIDATE_INT)) {
            return $int;
        }
        return  null;
    }

    public function filterBoolean($boolean)
    {
        return !!filter_var($boolean, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_TRUE_REGEX]]);
    }

    public function filterEditable($editable)
    {
        return (int) filter_var($editable, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => EDITABLE_CHOICE_REGEX]]);
    }

    public function filterComment($comment)
    {
        $comment = str_replace("\r\n", "\n", $comment);
        return $this->returnIfNotBlank($comment);
    }

    /**
     * @param string $date
     * @return string
     */
    public function filterDate($date)
    {
        $dDate = DateTime::createFromFormat('Y-m-d', $date)->setTime(0, 0, 0);
        return $dDate->format('Y-m-d H:i:s');
    }

    /**
     * Return the value if it's not blank.
     *
     * @param string $filtered The value
     * @return string|null
     */
    private function returnIfNotBlank($filtered)
    {
        if ($filtered) {
            $withoutSpaces = str_replace(' ', '', $filtered);
            if (!empty($withoutSpaces)) {
                return $filtered;
            }
        }

        return null;
    }
}
