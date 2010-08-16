<?php

require_once dirname(__FILE__) . '/../main.inc.php';

/**
 * A little interface for delegate functionallity. This way it is easier to
 * add more than one tag writing functionality to the html helper object.
 */
interface IHtmlHelperTag {
    /**
     * Should return the name of the tag you want to print with this helper tag.
     */
    public function GetName();

    /**
     * Should return the correctly formatted tag.
     */
    public function GetTag($parameter);
}

abstract class HtmlHelperTag implements IHtmlHelperTag {
    /**
     * Contains the name of the helper tag object.
     * @var string
     */
    protected $Name;

    /**
     * Returns the name of the helper tag object.
     * @return string
     */
    public function GetName() { return $this->Name; }

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

        return "<{$name} {$attributes}>";
    }

    /**
     * Returns a endtag of the given element name.
     * @param string $name
     * @return string
     */
    protected function GetEndTag($name) {
        return "</{$name}>";
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
        $attributes = ' ';

        foreach($params as $key => $value) {
            $attributes = $attributes . "{$key}=\"{$value}\" ";
        }

        return "<{$name}{$attributes}{$end}";
    }
}

?>
