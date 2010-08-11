<?php

require_once 'main.inc.php';

abstract class Page {

    /**
     * The reference to the kernel object.
     * @var IKernel 
     */
    protected $Host;

    /**
     * The name of this page.
     * @var string
     */
    protected $Name;

    public function __construct($host, $name) {
        $this->Host = $host;
        $this->Name = $name;
    }

    /**
     * Gets an associative array of values in the value dispatcher objects.
     */
    public function Initialize($variables);

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

    protected function GetTemplate($name) {
        $path = "/../templates/{$name}.php";

        if(!file_exists($path))
            throw new FileNotFoundException("Couldn't include template for {$name} file {$path} does not exist!");

        include $path;
    }

    /**
     * Uses Getlink and writes the given anchor the html file.
     */
    protected function WriteLink($location, $name='', $alt='', $kernel='/../index.php') {
        echo $this->GetLink($location, $name, $alt, $kernel);
    }

    /**
     * Returns an string containing a link to the given location.
     */
    // Locations are in a special format for addressing pages. E.g. if you
    // want to address the administrator page which lies in the page directory
    // in the subdirectory admin with the name panel the addressing location
    // would be: 'admin/panel'
    // The resulting link would be "SERVER_PATH"/index.php?REGISTERED_VARS&location=admin/panel
    protected function GetLink($location, $name='', $alt='', $kernel='/../index.php') {
        if(!$this->Host->IsLinkValid($location))
            throw new FileNotFoundException("The given link {$location} does not point to a file in the page directory!");
        
        $name = ($name === '') ? $location : $name;
        $href = $this->GetHrefFor($location, $kernel);

        return "<a href=\"{$href}\" alt=\"{$alt}\">{$name}</a>";
    }

    /**
     * Generates an href suitable for the kernel.
     * @param string the intern name of the wanted page
     * @param string the kernel to which this link leads.
     * @return string
     */
    private function GetHrefFor($location, $kernel) {
        $params = $this->GetHttpParams();
        $params = ($params === '') ? "?location={$location}" : "{$params}&location={$location}";
        $path = dirname(__FILE__) . "{$kernel}{$params}";
        
        return $path;
    }

    /**
     * Generates a string which is properly formatted for the HTTP-URL with the
     * name value pairs taken from the data accessor of the kernel.
     */
    private function GetHttpParams() {
        $providers = $this->Host->GetDataAccessor();
        $array = $providers->GetAssocArray();
        $params = '';

        foreach($array as $key => $value) {
            if($params === '')
                $params = "?{$key}={$value}";
            else
                $params = "{$params}&{$key}={$value}";
        }

        return $params;
    }
}

?>
