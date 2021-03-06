<?php
/**
 * The HTML helper tag class which creates links which works with GET parameters.
 */
class DefaultLinkHrefTag extends HtmlHelperTag {
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
        if(!isset ($parameter['location']))
            new LocationNotFoundeException("No location was set!");

        $location = $parameter['location'];

        if(!$this->PageExist($location))
            throw new LocationNotFoundException("The location {$lcoation} does not exist!");

        unset($parameter['location']);

        $kernelFile = 'index.php';

        if(isset($parameter['kernel'])) {
            $kernelFile = $parameter['kernel'];
            unset($parameter['kernel']);
        }

        $parameter['href'] = $this->GetHrefForPage($location, $kernelFile);


        return $this->GetSingleTag('a', $parameter, true);
    }

    /**
     * Checks if the given location is mapped to an existing page class file.
     * @param string $location
     * @return boolean
     */
    private function PageExist($location) {
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
    protected function GetHrefForPage($location, $parameter, $kernelFile) {
        $getparams = "?location={$location}";

        foreach($parameter as $key => $value) {
            $getparams = $getparams . "&{$key}={$value}";
        }

        $href = __URL__ . "{$kernelFile}{$getparams}";

        return $href;
    }
}

/**
 * This class handles the creation of links which carry the information the URL
 * itself seperated with slashes instead of GET parameters.
 */
class SlashedLinkHrefTag extends DefaultLinkHrefTag {
    /**
     * Returns the link with the parameters as data in the url seperated with
     * slashes.
     * @param string $location
     * @param array(mixed) $parameter
     * @param string $kernelFile
     * @return string
     */
    protected function  GetHrefForPage($location, $parameter, $kernelFile) {
        $location = str_replace(array('/'), array('.'), $location);
        $kernelFile = substr($kernelFile, 0, strlen($kernelFile) - 4);
        $params = "/{$kernelFile}/location/{$location}";

        foreach($parameter as $key => $value) {
            $params = $params . "{$key}/{$value}/";
        }

        $href = __URL__ . $params;
        return $href;
    }
}
?>
