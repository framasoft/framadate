<?php
/**
 * Created by PhpStorm.
 * User: antonin
 * Date: 05/04/15
 * Time: 14:28
 */

namespace Framadate;


/**
 * Class Editable
 *
 * Is used to specify the poll's edition permissions.
 *
 * @package Framadate
 */
class Editable { // extends SplEnum
    const __default = self::EDITABLE_BY_ALL;

    const NOT_EDITABLE = 0;
    const EDITABLE_BY_ALL = 1;
    const EDITABLE_BY_OWN = 2;
}