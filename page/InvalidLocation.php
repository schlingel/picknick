<?php

require_once dirname(__FILE__) . '/../core/main.inc.php';

class InvalidLocation extends Page {

    public function GetTitle() { return "Invalid Link - Got trolled ..."; }

    public function ShowBody() { $this->GetTemplate('InvalidLocation'); }

    public function Initialize() {}
}

?>
