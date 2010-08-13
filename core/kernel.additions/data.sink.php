<?php

require_once dirname(__FILE__) . '/../main.inc.php';

/*
 * A data sinkt takes care about getting data. Default data sinks in this project
 * are there for HTTP GET and HTTP POST data. This objects only collect data and
 * returns them in a associative array.
 */
interface IDataSink {
    /**
     * Initializes the specific data sink object.
     */
    public function Initialize();

    /**
     * Returns the data of this data sink as associative array.
     */
    public function GetData();
}
?>
