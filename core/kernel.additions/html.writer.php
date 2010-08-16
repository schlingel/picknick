<?php

require_once dirname(__FILE__) . '/../main.inc.php';

/**
 * This class encapsulates the html helper objects to delifer one consistent
 * interface for the kernel.
 */
class HtmlWriter {
    /**
     * The list of the html helpers in the object.
     * @var array(IHtmlHelper)
     */
    protected $HtmlHelpers;

    /**
     * The array of the data in the data providers.
     * @var array(mixed)
     */
    protected $StoredData;

    /**
     * The array of the data in the data sinks.
     * @var array(mixed)
     */
    protected $TmpData;

    /**
     * Initializes the HtmlWriter object.
     */
    public function  __construct($storedData, $tmpData) {
        $this->HtmlHelpers = array();
        $this->StoredData = $storedData;
        $this->TmpData = $tmpData;
    }

    /**
     * Adds a new html helper objects to the list.
     * @param IHtmlHelper $htmlHelper 
     */
    public function AddHtmlHelper($htmlHelper) {
        if(!($htmlHelper instanceof IHtmlHelper))
            throw new WrongTypeException ("The given object was not a IHtmlHelper object");

        $this->HtmlHelpers[$this->Count()] = $htmlHelper;
    }

    /**
     * Returns the count of the html helper objects in the list.
     * @return int
     */
    public function Count() { return count($this->HtmlHelpers); }

    /**
     * Removes the given element from the list.
     * @var IHtmlHelper $htmlHelper
     */
    public function RemoveHtmlHelper($htmlHelper) {
        $index = array_search($htmlHelper, $this->HtmlHelpers);

        if($index)
            unset($this->HtmlHelpers[$index]);
    }
}

?>
