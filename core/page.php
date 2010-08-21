<?php

require_once 'main.inc.php';

/**
 * The base class for pages in the picknick system. Every displayed page must be
 * a child of this class. The name of the page has to be the same like the file
 * name and the path. For example: The path of a class is ./page/users/frank/curriculum.php
 * then the class name is UsersFrankCurriculum.
 */
abstract class Page {

    /**
     * The reference to the kernel object.
     * @var IKernel 
     */
    protected $Host;
    
    public function __construct($host) {
        $this->Host = $host;
    }

    /**
     * Gets an associative array of values in the value dispatcher objects.
     */
    public abstract function Initialize();

    /**
     * Returns the title of the page. This title is entered in the title tags
     * in the header of the html file.
     */
    public abstract function GetTitle();

    /**
     * Writes the error with the given message to all logger objects.
     */
    protected function WriteError($errorLevel, $message) {
        $logger = $this->Host->GetLogger();
        $logger->Write($errorLevel, $message);
    }

    /**
     * Displays the page. You should put the code for the visible
     * parts of the page in this method.
     */
    public abstract function ShowBody();

    /**
     * Checks if the given template name points to an existing template.
     * If the template exists it gets included, otherwhise a FileNotFoundException
     * gets throwen.
     * @param string $name
     */
    protected function GetTemplate($name) {
        $path = dirname(__FILE__) . "/../templates/{$name}.php";

        if(!file_exists($path))
            throw new FileNotFoundException("Couldn't include template for {$name} file {$path} does not exist!");

        include $path;
    }

    /**
     * Uses Getlink and writes the given anchor the html file.
     */
    protected function WriteLink($location, $name='', $alt='', $extraParams=array(), $kernel='index.php') {
        $extraParams['alt'] = $alt;
        $extraParams['location'] = $location;
        $extraParams['text'] = $name;

        $this->Host->WriteHtml('link', 'a', $extraParams);
    }

    /**
     * Returns an string containing a link to the given location.
     */
    // Locations are in a special format for addressing pages. E.g. if you
    // want to address the administrator page which lies in the page directory
    // in the subdirectory admin with the name panel the addressing location
    // would be: 'admin/panel'
    // The resulting link would be "SERVER_PATH"/index.php?REGISTERED_VARS&location=admin/panel
    protected function GetLink($location, $name='', $alt='', $extraParams=null, $kernel='index.php') {
        if(!$this->Host->IsLinkValid($location))
            throw new FileNotFoundException("The given link {$location} does not point to a file in the page directory!");
        
        $name = ($name === '') ? $location : $name;
        $href = $this->GetHrefFor($location, $kernel, $extraParams);

        return "<a href=\"{$href}\" alt=\"{$alt}\">{$name}</a>";
    }

    /**
     * Generates an href suitable for the kernel.
     * @param string the intern name of the wanted page
     * @param string the kernel to which this link leads.
     * @return string
     */
    private function GetHrefFor($location, $kernel, $extraParams=null) {
        $params = $this->GetHttpParams($extraParams);
        $params = ($params === '') ? "?location={$location}" : "{$params}&location={$location}";
        $path = __URL__ . "{$kernel}{$params}";

        return $path;
    }

    /**
     * Generates a string which is properly formatted for the HTTP-URL with the
     * name value pairs taken from the data accessor of the kernel.
     */
    private function GetHttpParams($extraParams = null) {
        $providers = $this->Host->GetDataAccessor();
        $array = $providers->GetAssocArray();
        if($extraParams != null)
            $array = array_merge($array, $extraParams);
        
        $params = '';

        foreach($array as $key => $value) {
            if($params === '')
                $params = "?{$key}={$value}";
            else
                $params = "{$params}&{$key}={$value}";
        }

        return $params;
    }

    /**
     * Gets the data from the data providers. This data is persistent for the whole session.
     * @return array(mixed)
     */
    public function GetDataStore() { return $this->Host->GetDataAccessor()->GetAssocArray(); }

    /**
     * Gets the data from the data sinks which aren't persistent for the whole session.
     * @return array(mixed)
     */
    public function GetTmpData() { return $this->Host->GetDataAccessor()->GetTmpData(); }

    /**
     * Writes the wanted html tag directly to the html page. If there is no html helper object
     * with the given name the method throws an ObjectNotFoundException.
     * @param string $name The name of the html helper object.
     * @param string $tag The name of the wanted tag.
     * @param array(mixed) $params The associative array which contains the attributes.
     * @return void
     */
    public function WriteElement($name, $tag, $params) {
        $this->Host->WriteHtml($name, $tag, $params);
    }

    /**
     * Returns the string of the wanted tag or throws an ObjectNotFoundException.
     * @param string $name The name of the html helper object.
     * @param string $tag The name of the wanted tag.
     * @param array(mixed) $params The associative array which contains the attributes.
     * @return string
     */
    public function GetElement($name, $tag, $params) { return $this->Host->GetHtml($name, $tag, $params); }
}
?>
