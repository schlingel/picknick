<?php

require_once dirname(__FILE__) . '/../../core/main.inc.php';

/**
 * This class the data providing by the URL itself. So it works by a simple
 * schema: URL/kernel/key1/value1/key2/value2/ ...
 *
 * This way it is possible to create SE friendly URLs. To enable this link style
 * you have to enable the SlashedLinkHelper class by removing the .unused from the
 * SlashedLinkHelper.php.unused in the ./etc/html.helper directory.
 */
class UrlDataSink implements IDataSink {
    /**
     * Contains the data which is returned by the GetData method
     * @var array(mixed)
     */
    protected $TmpData;

    /**
     * Initializes the data sink with the given request uri and extracts the
     * data.
     */
    public function  Initialize() {
        $this->TmpData = array();
        $this->InitializeData();
    }

    /**
     * Extracts the data from the request uri. Throws an exception if the url
     * is not correct formatted. If the URL doesn't contain any slash seperated
     * information it just returns.
     */
    // Due to this two case exist strategy it is possible to keep this class
    // loaded all the time.
    private function InitializeData() {
        $dataString = $this->GetDataString();
        if($dataString == "")
            return;
        
        $parts = explode('/', $dataString);

        if((count($parts) % 2) != 0)
            throw new InvalidArgsException("The URL parameters have no value for every key!");
        
        for($i = 0; $i < count($parts);) {
            $key = $parts[$i];
            $value = $parts[$i + 1];
            $i += 2;

            $this->TmpData[$key] = $value;
        }
    }

    /**
     * Extracts the data containing string from the request uri.
     * @return string
     */
    private function GetDataString() {
        $wholeString = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];

        // wholeString starts with the kernel name, but without the .php. This
        // gets removed.
        $dataString = substr($wholeString, 0, strlen($scriptName) - 4);
        return $dataString;
    }

    /**
     * Returns the extracted data.
     * @return array(mixed)
     */
    public function  GetData() { return $this->TmpData; }
}

?>