<?php

require_once dirname(__FILE__) . '/../../core/main.inc.php';

/**
 * This class ease the creation of forms.
 */
class FormHelper extends HtmlHelper {
    /**
     * Returns the name of this html helper object.
     * @return string
     */
    public function GetName() { return 'form'; }

    /**
     * Writes the specific form element to the page.
     * @param string $name
     * @param array(mixed) $params
     */
    public function  WriteElement($name, $params) {
        if(strcasecmp($name, 'start')) {
            echo $this->GetTagStart('form', $params) . "\n";

            foreach($this->StoredData as $key => $value) {
                echo $this->GetSingleTag('input', array('type' => 'hidden', 'name' => $key, 'value' => $value)) . "\n";
            }
        }
        else if(strcasecmp($name, 'end')) {
            echo $this->GetEndTag('form') . "\n";
        }
        else if(strcasecmp($name, 'input')) {
            echo $this->GetSingleTag('input', $params) . "\n";
        }
    }
    
}

?>
