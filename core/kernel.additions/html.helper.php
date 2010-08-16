<?php

require_once dirname(__FILE__) . '/../main.inc.php';

/**
 * The interface for objects which write html code to the page. The place for
 * classes which implements the interface is in /etc/html.helper/
 */
interface IHtmlHelper {
    /**
     * Gets the name of the specific object.
     * @return string
     */
    public function GetName();

    /**
     * Initializes the specific object with the data in the data providers and
     * sinks.
     */
    public function Initialize($storedData, $tmpData);

    /**
     * Writes the specific element to the current position when the method is
     * called.
     * @param string name
     * @param array(mixed) params - contains element specific parameter.
     */
    public function WriteElement($name, $params);
}

/**
 * A little class to minimize the write effort for the user.
 */
abstract class HtmlHelper implements IHtmlHelper {

    /**
     * Contains the temporary data (data in data sinks)
     * @var array(mixed)
     */
    protected $TmpData;

    /**
     * Contains the the stored data. (data in data providers)
     * @var array(mixed)
     */
    protected $StoredData;

    /**
     * Initializes the object with the given stored and temporary saved data.
     */
    public function  Initialize($storedData, $tmpData) {
        $this->StoredData = $storedData;
        $this->TmpData = $tmpData;
    }
}

?>
