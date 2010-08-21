<?php

require_once dirname(__FILE__) . '/../../core/main.inc.php';

/**
 * The default sink parsing for form, tmp data and the location field.
 */
class DefaultSink implements IDataSink {

    protected $Data;

    /**
     * Initializes and parses the data from the GET and the POST variables.
     * @return void
     */
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
     * Returns the data of this sink.
     * @return array(mixed)
     */
    public function GetData() { return $this->Data; }
}

?>
