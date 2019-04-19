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
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use InvalidArgumentException;

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

    public function filterId($id) {
        $filtered = filter_var($id, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => POLL_REGEX]]);
        return $filtered ? substr($filtered, 0, 64) : false;
    }

    public function filterName($name) {
        $filtered = trim($name);
        return $this->returnIfNotBlank($filtered);
    }

    public function filterMail($mail) {
    	///////////////////////////////////////////////////////////////////////////////////////
        // formatting

    	$mail = trim($mail);

    	///////////////////////////////////////////////////////////////////////////////////////
        // e-mail validation

        $resultat = FALSE;

    	$validator = new EmailValidator();

        if ($validator->isValid($mail, new RFCValidation())) {
            $resultat = $mail;
        }

        ///////////////////////////////////////////////////////////////////////////////////////
        // return

        return $resultat;
    }

    public function filterDescription($description) {
        $description = str_replace("\r\n", "\n", $description);
        return $description;
    }

    public function filterMD5($control) {
        return filter_var($control, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => MD5_REGEX]]);
    }

    public function filterInteger($int) {
        return filter_var($int, FILTER_VALIDATE_INT);
    }

    public function filterValueMax($int)
    {
        return $this->filterInteger($int) >= 1 ? $this->filterInteger($int) : false;
    }

    public function filterBoolean($boolean) {
        return !!filter_var($boolean, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => BOOLEAN_TRUE_REGEX]]);
    }

    public function filterEditable($editable) {
        return filter_var($editable, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => EDITABLE_CHOICE_REGEX]]);
    }

    public function filterCollectMail($collectMail) {
        return filter_var($collectMail, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => COLLECT_MAIL_CHOICE_REGEX]]);
    }

    public function filterComment($comment) {
        $comment = str_replace("\r\n", "\n", $comment);
        return $this->returnIfNotBlank($comment);
    }

    /**
     * @param string $date
     * @return DateTime
     */
    public function filterDate($date) {
        $dDate = parse_translation_date($date);
        if ($dDate) {
            return $dDate;
        }
        throw new InvalidArgumentException('Invalid date');
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
