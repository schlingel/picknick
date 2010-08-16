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

    public function  __construct() {
        $this->HtmlHelperTags = array(
            new FormEndTag(),
            new FormInputTag(),
            new FormStartTag()
        );
    }
}

/**
 * The start tag of a form.
 */
class FormStartTag extends HtmlHelperTag {
    public function  __construct() {
        $this->Name = 'start';
    }

    /**
     * Returns the form start tag with the given attributes.
     * @return string
     */
    public function  GetTag($parameter) {
        return $this->GetTagStart('form', $parameter);
    }
}

/**
 * The end tag of the form element.
 */
class FormEndTag extends HtmlHelperTag {
    public function  __construct() {
        $this->Name = 'end';
    }

    public function GetTag($parameter) {
        return $this->GetEndTag('form');
    }
}

/**
 * The input object in the form.
 */
class FormInputTag extends HtmlHelperTag {
    public function __construct() {
        $this->Name = 'input';
    }

    /**
     * Returns the input tag element
     * @return string
     */
    public function GetTag($parameter) {
        return $this->GetTagStart('input', $parameter);
    }
}

?>
