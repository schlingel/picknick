<?php

require_once dirname(__FILE__) . '/../../core/main.inc.php';

/**
 * The html helper class which creates links which works with GET parameter.
 */
class DefaultLinkHelper extends HtmlHelper {
    public function GetName() { return 'link'; }

    public function  __construct() {
        $this->HtmlHelperTags = array(
            new DefaultLinkHrefTag(),
            new DefaultHrefTag()
        );
    }
}

/**
 * This class manages to create urls with data in the GET parameter style.
 */
// This is needed as extra class to provide the user a way to get hrefs which
// are compatible to the picknick system without hacking something extra ordinary
// or to parse the href parameter from the html tag.
class DefaultHrefTag extends HtmlHelperTag {

    /**
     * @var NonLinkParameter contains the names of the fields which shouldn't be
     * in the a-tag.
     */
    protected static $NonLinkParameter = array('alt', 'text', 'kernel', 'location');

    /**
     * Returns the name of this html helper tag. 'href'
     */
    public function GetName() { return 'href'; }

    /**
     * Returns a link which is points to the given kernel file with the wanted
     * location and the other parameters.
     * @param array(mixed) $parameter
     * @return string
     */
    public function  GetTag($parameter) {
        if(!isset ($parameter['location']))
            new LocationNotFoundException("No location was set!");

        $location = $parameter['location'];

        if(!$this->PageExist($location))
            throw new LocationNotFoundException("The location {$lcoation} does not exist!");

        $alt = (isset($parameter['alt'])) ? $parameter['alt'] : "Link to {$location}";
        $kernelFile = (isset($parameter['kernel'])) ? $parameter['kernel'] : __DEFAULT_KERNEL_FILE__;

        $parameter = $this->UnsetParameters(self::$NonLinkParameter, $parameter);
        return $this->GetHrefForPage($location, $parameter, $kernelFile);
    }

    /**
     * Unsets the given keys in the parameter array and returns the filtered array.
     * @param array(string) $names
     * @param array(mixed) $array
     * @return array(mixed)
     */
    protected function UnsetParameters($names, $array) {
        foreach($names as $name) {
            if(isset($array[$name])) {
                unset($array[$name]);
            }
        }

        return $array;
    }

    /**
     * Checks if the given location is mapped to an existing page class file.
     * @param string $location
     * @return boolean
     */
    protected function PageExist($location) {
        $path = dirname(__FILE__) . "/../../page/{$location}.php";
        return file_exists($path);
    }

    /**
     * Creates a href link to the given kernelFile to the specified location
     * with the given parameters as GET parameter.
     * @param string $location
     * @param array(mixed) parameter
     * @param string kernelFile
     * @return string
     */
    protected function GetHrefForPage($location, $parameter=array(), $kernelFile=__DEFAULT_KERNEL_FILE__) {
        $getparams = "?location={$location}";

        foreach($parameter as $key => $value) {
            $getparams = $getparams . "&{$key}={$value}";
        }

        $href = __URL__ . "{$kernelFile}{$getparams}";

        return $href;
    }
}

/**
 * The HTML helper tag class which creates links which works with GET parameters.
 */
class DefaultLinkHrefTag extends DefaultHrefTag {
    /**
     * Returns the name of this html helper tag. 'a'
     */
    public function GetName() { return 'a'; }

    /**
     * Returns a link which is points to the given kernel file with the wanted
     * location and the other parameters.
     * @param array(mixed) $parameter
     * @return string
     */
    public function  GetTag($parameter) {
        $text = $parameter['text'];
        $parameter['href'] = parent::GetTag($parameter);
        $parameter = $this->UnsetParameters(self::$NonLinkParameter, $parameter);
        
        $aTag = $this->GetTagStart('a', $parameter);
        $aTag = "{$aTag}{$text}" . $this->GetEndTag('a');
        return $aTag;
    }
}
?>
