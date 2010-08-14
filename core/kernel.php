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
}

/**
 * The actuall kernel object. 
 */
class Kernel implements IKernel {
    
    protected $Logger;

    protected $DataAccessor;

    protected $CurrentPage;

    public function  __construct() {
        $this->Logger = new LoggingPublisher();
        $this->DataAccessor = new DataAccessor();

        $this->Initialize();
    }

    /**
     * Initializes the parts of the Kernel which are used in every call.
     */
    private function Initialize() {
        $this->GetLoggerFromEtc();
        $this->GetDataProviderFromEtc();
        $this->GetDataSinkFromEtc();

        $this->DataAccessor->Initialize();

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
     * Parses the etc/data.provider/ directory and adds the the containing
     * data provider to the kernel.
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
     * returns array(string) 
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
     */
    private function IsPHP($filename) {
        $end = substr($filename, strlen($filename) - 4, 4);

        return ($end === '.php');
    }

    public function GetPathOfLocation($location) {
        return dirname(__FILE__) . "/../page/{$location}.php";
    }
    
    public function IsLinkValid($location) {
        $path = $this->GetPathOfLocation($location);
        return file_exists($path);
    }

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
}

?>
