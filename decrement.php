<?php

define('INTERNAL', true);

require_once('form.php');

$form = new form($_REQUEST);
$form->decrement($_REQUEST['orgname']);

$form->echo_form();
