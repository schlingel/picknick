<?php
/*
 * This file contains functions which are usefull in more than one class which aren't
 * in any family relationship.
 */

/**
 * Merges the given elements of the mergeSource array to the target array.
 * @param array(mixed) $target The associative array to which the elements of mergeSource are written to.
 * @param array(mixed) $mergeSource The source array from which the elements are taken.
 * @return array(mixed)
 */
function MergeArray($target, $mergeSource) {
    foreach($mergeSource as $key => $value) {
        if(is_array($target[$key]) && is_array($mergeSource[$key])) {
            $target[$key] = MergeArray($target[$key], $mergeSource[$key]);
        }
        else {
            $target[$key] = $value;
        }
    }
    return $target;
}

/**
 * Parses the title and generates a hierachy of associative arrays which
 * the last part contains the given value. This is merged into the given
 * array and returned.
 * @param string $title The name of the element
 * @param string $value The value of the element.
 * @param array(mixed) $array The target to which the new element is written.
 */
function MergeDataWith($title, $value, $array) {
    $currentArray = &$array;
    $names = explode('/', $title);

    for($i = 0; $i < count($names); $i++) {
        $name = $names[$i];

        if(!isset($currentArray[$name]) || !is_array($currentArray[$name]))
            $currentArray[$name] = array();

        $currentArray = &$currentArray[$name];
    }

    $currentArray = $value;
    return $array;
}


/**
 * Adds the given value in the top-down array hierachy, determined by the
 * names list which is upwards tree, to the given array.
 * @param array(mixed) $array
 * @param array(string) $names
 * @param mixed $value
 */
function BuildMultilevelAssocArray($array, $names, $value) {
   $length = count($names) - 1;
   $key = $names[$length];
   $tmp = $array;

   for($i = 0; $i < $length; $i++) {
       $name = $names[$i];

       if(!is_array($tmp[$name]))
           $tmp[$name] = array();

       $tmp = $tmp[$name];
   }

   $tmp[$key] = $value;
   return $array;
}
?>
