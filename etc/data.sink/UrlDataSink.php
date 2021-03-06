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
        if(strcmp($dataString, '') == 0)
            return;
        
        $parts = $this->GetUrlParts($dataString);

        if((count($parts) % 2) != 0)
            throw new InvalidArgsException("The URL parameters have no value for every key!");
        
        for($i = 0; $i < count($parts);) {
            // This happens because the SlashedLinkHelper changes the slashes
            // of values like location links to page/post/34  to page.post.34
            // in this step the values are retranslated.
            $key = str_replace(array('.'), array('/'), $parts[$i]);
            $value = str_replace(array('.'), array('/'), $parts[$i + 1]);
            $i += 2;

            $keyParts = explode('/', $key);

            if(count($keyParts) > 1) {
                $this->TmpData = MergeDataWith($key, $value, $this->TmpData);
            }
            else {
                $this->TmpData[$key] = $value;
            }
        }
    }

    /**
     * Checks if the given url has this form: domain/landingpage/value1/value2/...
     * @param string $url
     * @return boolean
     */
    private function IsDataUrl($url) {
        //return preg_match("/(\/)((a-zA-Z0-9\.\-)+(\/))+/", $url);
        return !strpos($url, "?") && !strpos($url, "&") && !strpos($url, ".php");
    }

    /**
     * Extracts the data containing string from the request uri.
     * @return string
     */
    private function GetDataString() {
        $wholeString = $_SERVER['REQUEST_URI'];
        
        if(!$this->IsDataUrl($wholeString))
            return '';
        
        return $this->RemoveNonDataUrlPart($wholeString);
    }

    /**
     * Removes the not kernel reference and the project dir from the url.
     * @param string the url
     * @return string
     */
    private function RemoveNonDataUrlPart($url) {
        if(strlen(__PROJECT__) > 0) {
            // This way the the string "/__PROJECT__/" gets removes from the
            // url.
            $count = strlen(__PROJECT__) + 1;
            $url = substr($url, $count);
        }

        // The following code removes the "LANDINGPAGE/" string.
        $parts = $this->GetUrlParts($url);
        $count = strlen($parts[0]) + 1;
        $url = substr($url, $count);

        // Removes the last '/' if there is any.
        $count = strlen($url);
        $count -= ($url{$count - 1} === '/') ? 1 : 0;
        $url = substr($url, 0, $count);

	return $url;
    }

    /**
     * Calls explode with '/' as delimiter on $url and removes every empty
     * string in the resulting array and returns this array.
     * @param string $url
     * @return array(string)
     */
    private function GetUrlParts($url) {
        // I had to use this because on a free linux webserver explode didn't
        // work properly and returned empty fields in the array which lead to
        // errors
        $nonFilteredParts = explode('/', $url);
        $filteredParts = array();
        $filteredIndex = 0;

        for($i = 0; $i < count($nonFilteredParts); $i++) {
            $element = $nonFilteredParts[$i];

            if(strlen($element) > 0) {
                $filteredParts[$filteredIndex] = $element;
                $filteredIndex++;
            }
        }

        return $filteredParts;
    }

    /**
     * Returns the extracted data.
     * @return array(mixed)
     */
    public function  GetData() { return $this->TmpData; }
}

?>
