<?php

require_once dirname(__FILE__) . '/../main.inc.php';

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
?>
