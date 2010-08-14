<?php

require_once dirname(__FILE__) . '/../main.inc.php';

/**
 * Data providers take care about storing data for the whole session.
 */
interface IDataProvider {
    /**
     * Returns the data of this IDataProvider as associative array.
     */
    public function GetData();

    /**
     * Gets the associative array which is constructed by the data sinks and
     * filters for data which should be stored.
     */
    public function Initialize($sinkData);
}
?>
