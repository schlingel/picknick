<?php

require_once dirname(__FILE__) . "/../../core/main.inc.php";

/**
 * This data provider contains the HTTP-GET data.
 */
class GetProvider implements IDataProvider {
    private $Data;

    public function  __construct() {
        $this->Data = $_GET;
    }

    public function  GetData() {
        return $this->Data;
    }

    public function UnsetValue($name) {
        $index = array_search($name, $this->Data);

        if($index)
            unset ($this->Data[$name]);
    }
}

?>
