<?php

define('INTERNAL', true);

require_once('form.php');

$form = new form($_REQUEST);
$form->save();

echo "Thank you for your submission!";
