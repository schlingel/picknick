<?php

require_once 'main.inc.php';


/**
 * A simple interface for objects which returns data at the initializing process
 * of the kernel object. Due to this objects it is possible to hand data from
 * one to page to one another.
 * E.g. if you have the session id in the URL it is automatically added to every
 * link you create with the GetLink method of the kernel.
 */
interface IDataProvider {
    /**
     * Returns the data of this IDataProvider as associative array.
     */
    public function GetData();

    /**
     * Removes the value with the given name from this data provider.
     */
    public function UnsetValue($name);
}

/*
 * In difference to the IDataProvider a IDataSink is the last point of data.
 * data sinks take care to remove data from the DataAccessor. This objects are
 * needed to give them the oppurtunity to act on differenct conditions which are
 * able to change.
 *
 * (E.g. connection to the DB are possible.) The default data sink removes every
 * POST-parameter which name begins with "form." and every GET-parameter which
 * begins with "tmp."
 */
interface IDataSink {
    /**
     * Returns the name of the sink object.
     */
    public function GetName();

    /**
     * Checks if the given name matches the condition to remove the name value
     * pair from the data providers.
     */
    public function IsTemporary($name);
}

class DataAccessor {
    protected $DataProviders;

    protected $DataSinks;

    protected $TemporaryData;

    public function __construct() {
        $this->DataProviders = array();
        $this->DataProviders = array();
        $this->TemporaryData = array();
    }

    /*
     * Checks for every value in every data provider if there is any data sink
     * which condition fires on the given value.
     */
    public function Initialize() {
        foreach($this->DataProviders as $dataProvider) {
            $data = $dataProvider->GetData();

            foreach($data as $key => $value) {
                foreach($this->DataSinks as $dataSink) {
                    $data = $dataProvider->GetData();

                    if($dataSink->IsTemporary($key)) {
                        if(isset($this->TemporaryData[$dataSink->GetName()]))
                            $this->TemporaryData[$dataSink->GetName()][$key] = $value;
                        else
                            $this->TemporaryData[$dataSink->GetName()] = array($key => $name);
                    }
                }
            }
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
    public function GetValue($name) {
        foreach($this->DataProviders as $provider) {
            $assoc_array = $provider->GetData();

            if(isset($assoc_array[$name]))
                return $assoc_array[$name];
        }

        return null;
    }

    /**
     * Unsets the value with the given name in every data provider.
     */
    public function UnsetValue($name) {
        foreach($this->DataProviders as $provider) {
            $provider->UnsetValue($name);
        }
    }

    /**
     * Generates one assoc array out of all data provider datasets.
     *
     * return array(mixed)
     */
    public function GetAssocArray() {
        $array = array();

        foreach($this->DataProviders as $provider) {
            foreach($provider->GetData() as $key => $value) {
                $array[$key] = $value;
            }
        }

        return $array;
    }
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
    public function GetPathOfLink($location);

    /**
     * Checks if the given location does exist.
     */
    public function IsLinkValid($location);
}

/**
 * The actuall kernel object. 
 */
class Kernel implements IKernel {
    
    protected $Logger;

    protected $DataAccessor;

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
    }

    /**
     * Parses the ../etc/logger directory and tries to create objects from the
     * file names. The pattern is FILE_NAME.php and creates a call like this:
     * bla = new FILE_NAME(); and adds this objects to the logger module.
     */
    private function GetLoggerFromEtc() {
        $path = dirname(__FILE__) . '../etc/logger';
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
        $path = dirname(__FILE__) . '../etc/data.provider';
        $filenames = $this->GetFileNamesFrom($path);

        $accessor = $this->DataAccessor;

        foreach($filenames as $filename) {
            $pureName = substr($filename, 0, strlen($filename) - 4);
            eval("\$accessor->AddDataProvider(new {$pureName}());");
        }
    }

    private function GetDataSinkFromEtc() {
        $path = dirname(__FILE__) . '../etc/data.sink';
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
        $dir = new DirectoryIterator(dirname(__FILE__));
        
        foreach ($dir as $fileinfo) {
            $name = $fileinfo->getFilename();
            
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

    public function IsLinkValid($location) {
        $path = $this->GetPathOfLink($location);
        return file_exists($path);
    }

    /**
     * Generates the possible path to the given location. This method returns
     * allways a path, wether the file exists or not.
     */
    public function GetPathOfLink($location) {
        return dirname(__FILE__) . "../page/{$location}.php";
    }

    /**
     * Returns the logging publisher object.
     * @return LoggingPublisher
     */
    public function GetLogger() { return $this->Logger; }
}

?>
