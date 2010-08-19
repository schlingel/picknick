<?php

require_once dirname(__FILE__) . '/../../core/main.inc.php';

/**
 * The html helper class which creates links which works with GET parameter.
 */
class DefaultLinkHelper extends HtmlHelper {
    public function GetName() { return 'link'; }

    public function  __construct() {
        $this->StoredData = array(
            new DefaultLinkHrefTag()
        );
    }
}

?>
