<?php

require_once dirname(__FILE__) . '/../core/main.inc.php';

class Standard extends Page {

    public function GetTitle() { return "Default Page"; }

    public function ShowBody() {
        echo "HALLO WELT ;-)";
        $this->GetTemplate('Standard');
    }

    public function Initialize() {
        ;
    }
}

?>
