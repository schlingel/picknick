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
    public function Initialize($storedData);

    /**
     * Returns the wanted tag as string instead of the writing it direct to the
     * page.
     * @param string $name
     * @param array(mixed) $params
     * @return string
     */
    public function GetElement($name, $params);

    /**
     * Writes the specific element to the current position when the method is
     * called.
     * @param string $name
     * @param array(mixed) $params - contains element specific parameter.
     */
    public function WriteElement($name, $params);
}

/**
 * A little class to minimize the write effort for the user.
 */
abstract class HtmlHelper implements IHtmlHelper {
    /**
     * Contains the html helper tag objects.
     */
    protected $HtmlHelperTags;

    /**
     * Contains the the stored data. (data in data providers)
     * @var array(mixed)
     */
    protected $StoredData;

    /**
     * Initializes the object with the given stored and temporary saved data.
     */
    public function  Initialize($storedData) {
        $this->StoredData = $storedData;
    }

    /**
     * Writes the wanted element to the page.
     * @param string $name The name of the tag
     * @param array(mixed) $params The attributes of the tag.
     * @return void
     */
    public function  WriteElement($name, $params) {
        echo $this->GetElement($name, $params);
    }

    /**
     * Gets the wanted element and returns it as string.
     * @param string $name
     * @param array(mixed) $params
     * @return string
     */
    public function GetElement($name, $params) {
        foreach($this->HtmlHelperTags as $helperTag) {
            if(strcasecmp($name, $helperTag->GetName()) == 0) {
                return $helperTag->GetTag($params);
            }
        }

        throw new ObjectNotFoundException("Couldn't find the wanted tag helper object!");
    }
}


?>
