<?php
namespace Framadate\Services;

/**
 * This class helps to clean all inputs from the users or external services.
 */
class InputService {

    function __construct() {}

    /**
     * This method filter an array calling "filter_var" on each items.
     * Only items validated are added at their own indexes, the others are not returned.
     */
    function filterArray($arr, $type, $options) {
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