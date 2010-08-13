<?php

require_once 'main.inc.php';


/**
 * Data providers take care about storing data for the whole session.
 */
interface IDataProvider {
    /**
     * Returns the data of this IDataProvider as associative array.
     */
    public function GetData();

    /**
     * Gets the associative array which is constructed by the data sinks and
     * filters for data which should be stored.
     */
    public function Initialize($sinkData);
}

/*
 * A data sinkt takes care about getting data. Default data sinks in this project
 * are there for HTTP GET and HTTP POST data. This objects only collect data and
 * returns them in a associative array.
 */
interface IDataSink {
    /**
     * Initializes the specific data sink object.
     */
    public function Initialize();

    /**
     * Returns the data of this data sink as associative array.
     */
    public function GetData();
}

/**
 */
class DataAccessor {
    /**
     * Contains the list of the data providers.
     * var array(IDataProvider)
     */
    protected $DataProviders;

    /**
     * Contains the list of the data sinks.
     * VAR array(IDataSink)
     */
    protected $DataSinks;

    /*
     * Stores the data which gets lost the next time the user sends a POST or
     * GET request.
     */
    protected $TemporaryData;

    /**
     * Stores the data which is consistent for the whole session.
     */
    protected $StoredData;

    public function __construct() {
        $this->DataProviders = array();
        $this->DataSinks = array();
        $this->TemporaryData = array();
    }

    /*
     * Checks for every value in every data provider if there is any data sink
     * which condition fires on the given value.
     */
    public function Initialize() {
        $this->StoredData = array();
        $this->TemporaryData = array();

        foreach($this->DataSinks as $dataSink) {
            $dataSink->Initialize();
            $this->TemporaryData = array_merge($this->TemporaryData, $dataSink->GetData());
        }

        foreach($this->DataProviders as $dataProvider) {
            $dataProvider->Initialize($this->TemporaryData);
            $this->StoredData = array_merge($this->StoredData, $dataProvider->GetData());
        }
    }

    /**
     * Adds a data provider to the provider list.
     * @param IDataProvider $dataProvider
     */
    public function AddDataProvider($dataProvider) {
        if(!($dataProvider instanceof IDataProvider))
            throw new WrongTypeException ("The given object wasn't a IDataProvider type.");

        $this->DataProviders[count($this->DataProviders)] = $dataProvider;
    }

    /**
     * Removes the given data provider from the list.
     * @param IDataProvider $dataProvider
     */
    public function RemoveDataProvider($dataProvider) {
        $index = array_search($dataProvider, $this->DataProviders);

        if($index)
            unset($this->DataProviders[$index]);
    }

    /*
     * Adds a data sink to the data sink list.
     */
    public function AddDataSink($dataSink) {
        if(!($dataSink instanceof IDataSink))
            throw new WrongTypeException ("The given data sink object is not a IDataSink object!");

        $this->DataSinks[count($this->DataSinks)] = $dataSink;
    }

    /**
     * Removes the given data sink from the data sink list.
     * @param <type> $dataSink 
     */
    public function RemoveDataSink($dataSink) {
        $index = array_search($dataSink);

        if($index)
            unset($this->DataSinks[$index]);
    }

    /**
     * Gets the value from the first data provider which contains a value with
     * the given name. If no provider contain such a value with that name null
     * is returned.
     *
     * @param string $name
     * @return mixed
     */
    public function GetValue($name) { return $this->StoredData[$name]; }

    public function GetTmpValue() { return $this->TemporaryData; }

    /**
     * Unsets the value with the given name in every data provider.
     */
    public function UnsetValue($name) { unset($this->StoredData[$name]); }

    /**
     * Generates one assoc array out of all data provider datasets.
     *
     * return array(mixed)
     */
    public function GetAssocArray() { return $this->StoredData; }

    /**
     * Returns the temporary data.
     */
    public function GetTmpData() { return $this->TemporaryData; }
}

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
