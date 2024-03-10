<?php

define('INTERNAL', true);

require_once('form.php');

$form = new form($_REQUEST);
$form->check_overflow($_REQUEST['orgname']);

$form->echo_form();
