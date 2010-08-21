<?php

require_once dirname(__FILE__) . '/../../core/main.inc.php';

/**
 * The default sink parsing for form, tmp data and the location field.
 */
class DefaultSink implements IDataSink {

    protected $Data;

    public function Initialize() {
        $array = array();
        $array = MergeArray($array, $_POST);
        $array = MergeArray($array, $_GET);
        $this->Data = array();

        foreach($array as $key => $value) {
            $parts = explode('/', $key);

            if(count($parts) > 1) {
                $this->Data = MergeDataWith($key, $value, $this->Data);
            }
            else {
                $this->Data[$key] = $value;
            }
        }
    }
    
    /**
     * Splits the given name by the '/' as delimiter and return the parts of the
     * name.
     */
    private function GetNames($name) {
        return explode('/', $name);
    }

    /**
     * Returns the data of this sink.
     * @return array(mixed)
     */
    public function GetData() { return $this->Data; }
}

?>
