<?php

$this->WriteElement('form', 'start', array('method' => 'post', 'action' => $this->GetElement('link', 'href', array('location' => 'standard'))));
$this->WriteElement('form', 'input', array('title' => "Submit", 'value' => "Submit", 'type' => 'submit'));
$this->WriteElement('form', 'end', array());

?>