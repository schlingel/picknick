<?php

$this->WriteElement('form', 'start', array('method' => 'post', 'action' => 'login.php'));
$this->WriteElement('form', 'input', array('title' => "Submit", 'value' => "Submit", 'type' => 'submit'));
$this->WriteElement('form', 'end', array());

?>