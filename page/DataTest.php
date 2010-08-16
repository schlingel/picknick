<?php

require_once dirname(__FILE__) . '/../core/main.inc.php';

class DataTest extends Page {

    public function GetTitle() { return "Invalid Link - Got trolled ..."; }

    public function ShowBody() {

        $this->GetTemplate('navigation/links');
        $storedData = $this->GetDataStore();
        $tmpData = $this->GetTmpData();

        var_dump($storedData);
        echo "<br>";
        var_dump($tmpData);

        echo "<br>" . $tmpData['form']['test'];

        $this->GetTemplate('Form');

    }

    public function Initialize() {}
}

?>
