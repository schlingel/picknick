<?php

require_once dirname(__FILE__) . '/../../core/main.inc.php';

/**
 * The default sink parsing for form, tmp data and the location field.
 */
class DefaultSink implements IDataSink {

    protected $Data;

    public function Initialize() {
        $array = array();
        $array = $this->MergeArray($array, $_POST);
        $array = $this->MergeArray($array, $_GET);
        $this->Data = array();

        foreach($array as $key => $value) {
            $parts = explode('/', $key);

            if(count($parts) > 1) {
                $this->Data = $this->MergeDataWith($key, $value, $this->Data);
            }
            else {
                $this->Data[$key] = $value;
            }
        }
    }

    private function MergeArray($target, $mergeSource) {
        foreach($mergeSource as $key => $value) {
            if(is_array($target[$key]) && is_array($mergeSource[$key])) {
                $target[$key] = $this->MergeArray($target[$key], $mergeSource[$key]);
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
     */
    private function MergeDataWith($title, $value, $array) {
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
    private function BuildMultilevelAssocArray($array, $names, $value) {
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

    /**
     * Splits the given name by the '/' as delimiter and return the parts of the
     * name.
     */
    private function GetNames($name) {
        return explode('/', $name);
    }

    private function IsMultipartName($name) { 
        return count($this->GetNames($name)) > 0;
    }

    /**
     * Returns the data of this sink.
     * @return array(mixed)
     */
    public function GetData() { return $this->Data; }
}

?>
