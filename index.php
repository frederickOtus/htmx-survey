<?php

/**
* Features:
* - Form validation
* - Breadname
* - Saving
*/

define('INTERNAL', true);

require_once('form.php');
$form = new form($_REQUEST);
if(!empty($_REQUEST['reset'])) {
    $form::renew_name();
}

?>

<html>
    <head>
      <script src="htmx.min.js"></script>
        <link rel="stylesheet" href="styles.css">
    </head>

    <body>

    <h1>HELLO <span id="breadname"><?php echo $form->get_name(); ?></span></h1>
    <?php if(!form::has_submitted()) { ?>
        <button 
            hx-get="new_bread.php"
            hx-target="#breadname">New bread name please</button>
    <?php } ?>
        
    <br/><br/>

    <div> <?php echo form::number_of_responses() ?> response(s) have been made so far.

    <?php if(form::has_submitted()) {
        
        echo "<div id='form-wrapper'>You appear to have already made a submission. If you need to make a second response with the same device, <a href='/index.php?reset=true' hx-boost='true'>please click here</a></div>";
        } else { ?>
    <div> Instructions: You have <?php echo $form->max_points; ?> to allocate among the organizations. Each org can have a max of <?php echo $form::MAX_SCORE; ?> points.

    <div id="form-wrapper">
        <?php $form->echo_form() ?>
    </div>
        <?php } ?>

    </body>

</html>
