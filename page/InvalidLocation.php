<?php

require_once dirname(__FILE__) . '/../core/main.inc.php';

class InvalidLocation extends Page {

    public function GetTitle() { return "Default Page"; }

    public function ShowBody() {
        echo "BÖÖÖÖÖÖÖÖÖÖÖSE! >:-(";
    }

    public function Initialize() {
        ;
    }
}

?>
