<?php

require_once 'main.inc.php';

/**
 * The definition of the signature a picknick kernel should have.
 */
interface IKernel {
    /**
     * Returns an array of ILoggerObserver objects.
     * return array(ILoggerObeerver)
     */
    public function GetLogger();

    /**
     * Returns an array of IDataProvder objects.
     * return array(IDataProvider) 
     */
    public function GetDataAccessor();

    /**
     * Creates a path to the given location
     */
    public function GetPathOfLocation($location);

    /**
     * Checks if the given location does exist.
     */
    public function IsLinkValid($location);

    /**
     * Shows the selected page.
     */
    public function ShowPage();

    /**
     * If a html helper with the given name exists, writes the wanted tag to the
     * page.
     * @param string $name The name of the html helper object.
     * @param string $tag The name of the wanted tag.
     * @param array(mixed) $params The associative array which contains the attributes of the tag.
     * @return void
     */
    public function WriteHtml($name, $tag, $params);
    
    /**
     * If a html helper with the given name exists, returns the wanted tag as string.
     * @param string $name The name of the html helper object.
     * @param string $tag The name of the wanted tag.
     * @param array(mixed) $params The associative array which contains the attributes of the tag.
     * @return string
     */
    public function GetHtml($name, $tag, $params);

    /**
     * Sets the current page to the given page.
     * @param Page $page
     * @return void
     */
    public function InvokePage($page);
}

/**
 * The actuall kernel object. 
 */
class Kernel implements IKernel {
    
    protected $Logger;

    protected $DataAccessor;

    protected $CurrentPage;

    protected $HtmlWriter;

    public function  __construct() {
        $this->Logger = new LoggingPublisher();
        $this->DataAccessor = new DataAccessor();
        $this->HtmlWriter = new HtmlWriter();

        $this->Initialize();
    }

    /**
     * Initializes the parts of the Kernel which are used in every call.
     */
    private function Initialize() {
        $this->GetLoggerFromEtc();
        $this->GetDataProviderFromEtc();
        $this->GetDataSinkFromEtc();
        $this->GetHtmlHelperFromEtc();

        $this->DataAccessor->Initialize();
        $this->HtmlWriter->Initialize($this->DataAccessor->GetAssocArray());

        $this->CurrentPage = $this->GetPage($this->GetLocationLink());
        $this->CurrentPage->Initialize();
    }

    /**
     * Returns the given location link from the data providers. If it isn't set
     * 'Standard' is returned.
     */
    private function GetLocationLink() {
        $array = $this->DataAccessor->GetTmpValue();
        $name = $array['location'];

        if($name === null)
            return 'Standard';

        return $name;
    }

    /**
     * Gets the data accessor reference.
     */
    public function  GetDataAccessor() { return $this->DataAccessor; }

    /**
     * Gets the title of the current page.
     */
    public function GetTitle() { return $this->CurrentPage->GetTitle(); }

    /**
     * Parses the ../etc/logger directory and tries to create objects from the
     * file names. The pattern is FILE_NAME.php and creates a call like this:
     * bla = new FILE_NAME(); and adds this objects to the logger module.
     */
    private function GetLoggerFromEtc() {
        $path = dirname(__FILE__) . '/../etc/logger';
        $filenames = $this->GetFileNamesFrom($path);
        $logger = $this->Logger;

        foreach($filenames as $filename) {
            $pureName = substr($filename, 0, strlen($filename) - 4);
            eval("\$logger->AddLogger(new {$pureName}());");
        }
    }

    /**
     * Parses the ../etc/html.helper directory and tries to create objects from
     * the file names. The pattern is FILE_NAME.php and creates a call like this:
     * bla = new FILE_NAME(); and adds this objects to the html writer module.
     */
    private function GetHtmlHelperFromEtc() {
        $path = dirname(__FILE__) . '/../etc/html.helper';
        $filenames = $this->GetFileNamesFrom($path);
        $htmlWriter = $this->HtmlWriter;

        foreach($filenames as $filename) {
            $pureName = substr($filename, 0, strlen($filename) - 4);
            eval("\$htmlWriter->AddHtmlHelper(new {$pureName}());");
        }
        
    }

    /**
     * Parses the etc/data.provider/ directory and adds the the containing
     * data provider to the kernel.
     * @return void
     */
    private function GetDataProviderFromEtc() {
        $path = dirname(__FILE__) . '/../etc/data.provider';
        $filenames = $this->GetFileNamesFrom($path);
        $accessor = $this->DataAccessor;

        foreach($filenames as $filename) {
            $pureName = substr($filename, 0, strlen($filename) - 4);
            eval("\$accessor->AddDataProvider(new {$pureName}());");
        }
    }

    /**
     * Loads every file in the ./etc/data.sink/ directory and invokes the given classes.
     * @return void
     */
    private function GetDataSinkFromEtc() {
        $path = dirname(__FILE__) . '/../etc/data.sink';
        $filenames = $this->GetFileNamesFrom($path);
        $accessor = $this->DataAccessor;

        foreach($filenames as $filename) {
            $pureName = substr($filename, 0, strlen($filename) - 4);
            eval("\$accessor->AddDataSink(new {$pureName}());");
        }
    }

    /**
     * Parses the etc/logger directory and adds returns the names of every php-file
     * in the directory.
     *
     * @returns array(string)
     */
    private function GetFileNamesFrom($path) {
        $array = array();
        $counter = 0;
        $dir = new DirectoryIterator($path);
        
        foreach ($dir as $fileinfo) {
            $name = $fileinfo->getFilename();
            
            if($name == '.' || $name == '..')
                continue;

            if($this->IsPHP($name)) {
                $array[$counter] = $name;
                $counter++;
            }
        }

        return $array;
    }

    /**
     * Checks if the given string ends with '.php'
     * @return boolean
     */
    private function IsPHP($filename) {
        $end = substr($filename, strlen($filename) - 4, 4);

        return ($end === '.php');
    }

    /**
     * Build the physically path of a page by its location name and returns it.
     * @param string $location The location of the page.
     * @return string
     */
    public function GetPathOfLocation($location) {
        return dirname(__FILE__) . "/../page/{$location}.php";
    }

    /**
     * Checks if the given location string contains the name of a existing page
     * class. This check does not check possible added session informations.
     * @param string $location The location of the page.
     * @return boolean
     */
    public function IsLinkValid($location) {
        $path = $this->GetPathOfLocation($location);
        return file_exists($path);
    }

    /**
     * Displays the current set page.
     * @return void
     */
    public function ShowPage() { $this->CurrentPage->ShowBody(); }

    /**
     * Generates code for invoking the given page object and creates it.
     * @param string $location
     * @return Page
     */
    private function GetPage($location) {
        $page = null;
        $className = $this->GetClassNameFrom($location);
        $code = "\$page = new {$className}(\$this);";

        eval($code);
        return $page;
    }

    /**
     * Splits the given location string and build the object name.
     */
    private function GetClassNameFrom($location) {

        if(!$this->IsLinkValid($location))
                return 'InvalidLocation';

        $pieces = explode("/", $location);
        $val = '';

        foreach($pieces as $piece) {
            $piece = ucfirst($piece);
            $val = "{$val}{$piece}";
        }

        return $val;
    }

    /**
     * Returns the logging publisher object.
     * @return LoggingPublisher
     */
    public function GetLogger() { return $this->Logger; }

    /**
     * If a html helper with the given name exists, writes the wanted tag to the
     * page.
     * @param string $name The name of the html helper object.
     * @param string $tag The name of the tag.
     * $param array(mixed) The associative array of the tag attributes.
     */
    public function WriteHtml($name, $tag, $params) {
       $this->HtmlWriter->Write($name, $tag, $params);
    }

    /**
     * If a html helper with the given name exists the method returns the string
     * to display it.
     * @param string $name The name of the html helper object.
     * @param string $tag The name of the tag.
     * $param array(mixed) The associative array of the tag attributes.
     * @return string
     */
    public function GetHtml($name, $tag, $params) { return $this->HtmlWriter->Get($name, $tag, $params); }

    /*
     * Sets the current page to the given page. Throws a WrongTypeException if
     * the given parameter isn't a Page type.
     * @param Page page
     * @return void
     */
    public function InvokePage($page) {
        if(!($page instanceof Page))
            throw new WrongTypeException ("The page parameter must be of the type Page!");

        $this->CurrentPage = $page;
        $this->CurrentPage->Initialize();
    }
}

?>
