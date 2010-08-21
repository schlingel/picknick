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
     * Initializes the HtmlWriter object.
     */
    public function  __construct() {
        $this->HtmlHelpers = array();
        $this->StoredData = array();
    }

    /**
     * Initilizes the html helper objects with the given stored data.
     * @param array(mixed) $storedData
     */
    public function Initialize($storedData) {
        foreach($this->HtmlHelpers as $htmlHelper) {
            $htmlHelper->Initialize($this->StoredData);
        }
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

    /**
     * Writes the given data out to the page. Throws an ObjectNotFoundException
     * when the needed html helper object is not in the list.
     * @param string $name The name of the html helper object.
     * @param string $tag The name of the tag which the html helper should print.
     * @param array(mixed) $parameter the associative array of tag attributes.
     */
    public function Write($name, $tag, $parameter = array()) { echo $this->Get($name, $tag, $parameter); }

    /**
     * Instead of writing the wanted tag direct to the page, this method just returns the
     * string. If the wanted tag does not exist it throws an ObjectNotFoundException.
     * @param string $name The name of html helber object.
     * @param string $tag The name of the tag which the html helper should return.
     * @param array(mixed) $parameter The associative array which contains the tag attributes.
     */
    public function Get($name, $tag, $parameter=array()) {
        foreach($this->HtmlHelpers as $htmlHelper) {
            if(strcasecmp($name, $htmlHelper->GetName()) == 0) {
                return $htmlHelper->GetElement($tag, $parameter);
            }
        }

        throw new ObjectNotFoundException("Couldn't find the given HtmlHelper object!");
    }
}

?>
