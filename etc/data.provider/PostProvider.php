<?php
require_once dirname(__FILE__) . "/../../core/main.inc.php";

/**
 * This data provider contains the HTTP-POST data.
 */
class PostProvider implements IDataProvider {
    private $Data;

    public function  __construct() {
        $this->Data = $_GET;
    }

    public function  GetData() {
        return $this->Data;
    }

    public function UnsetValue($name) {
        $index = array_search($name);

        if($index)
            unset ($this->Data[$name]);
    }
}

?>
