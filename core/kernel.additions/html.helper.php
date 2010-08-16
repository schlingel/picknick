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
     * Creates a start tag with the given parameter as attributes.
     * @param string $name
     * @param array(mixed) $parameter
     * @return string
     */
    protected function GetTagStart($name, $parameter) {
        $attributes = '';

        foreach($parameter as $key => $value) {
            $attributes = $attributes . "{$key}=\"{$value}\" ";
        }

        return "<{$name} {$attributes} >";
    }

    /**
     * Returns a endtag of the given element name.
     * @param string $name
     * @return string
     */
    protected function GetEndTag($name) {
        return "<{$name} />";
    }

    /**
     * Returns a tag which can be used for tags which end which have no body.
     * (e.g. <br> <input> and so on)
     * @param string $name
     * @param array(mixed) $params
     * @param boolean $endWithSlash
     * @return string
     */
    protected function GetSingleTag($name, $params=array(), $endWithSlash=true) {
        $end = $endWithSlash ? '/>' : '>';
        $attributes = '';

        foreach($params as $key => $value) {
            $attributes = $attributes . "{$key}=\"{$value}\" ";
        }

        return "<{$name} {$attributes}{$end}";
    }
}

?>
