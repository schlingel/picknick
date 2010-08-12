<?php

require_once dirname(__FILE__) . '/../../core/main.inc.php';

/**
 * The default sink parsing for form, tmp data and the location field.
 */
class DefaultSink implements IDataSink {

    protected $LastName;

    /**
     * Returns the name of the category of the last temporary key value pair.
     * @return string
     */
    public function GetName() {
        return $this->LastName;
    }

    /**
     * Checks if the given name starts with 'form.', 'tmp.' or if it is 'location'.
     * If it does it sets the name for GetName to form or tmp and returns true,
     * otherwise it just returns false.
     */
    public function IsTemporary($name) {
        if($this->BeginsWith($name, 'form.')) {
            $this->LastName = 'form';
            return true;
        }

        if($this->BeginsWith($name, 'tmp.') || (strcmp($name, 'location') == 0)) {
            $this->LastName = 'tmp';
            return true;
        }

        return false;
    }

    /**
     * Checks if the given name starts with the phrase.
     */
    private function BeginsWith($name, $phrase) {
        $length = strlen($phrase);
        $begin = substr($name, 0, $length);

        return ($begin === $phrase);
    }
}

?>
