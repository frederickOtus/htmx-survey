<?php

if(!defined('INTERNAL')) {
    die();
}

?>
<form id="survey_form">
    <div>You have <?php echo $this->get_remaining_pts(); ?> points to allocate.</div>
    <?php foreach($this->orgs as $shortname => $org) { ?>
    <div id="<?php echo $org['shortname']; ?>-wrapper">
        <span><?php echo $org['name']; ?></span>
        <button name="orgname" <?php if(!$this->can_decrement($org)) { echo "disabled"; } ?>
            value="<?php echo $shortname; ?>"
            hx-post="decrement.php"
            hx-synx="closest form:abort"
            hx-target="#form-wrapper"
        >-</button>
        <input name="<?php echo $org['shortname']; ?>" value="<?php echo $org['score']; ?>"
            hx-post="overflow.php?orgname=<?php echo $shortname; ?>"
            hx-synx="closest form:abort"
            hx-target="#form-wrapper"
            hx-trigger="change"
        ></input>
        <button name="orgname" <?php if(!$this->can_increment($org)) { echo "disabled"; } ?>
            value="<?php echo $shortname; ?>"
            data-foo="bar"
            hx-post="increment.php"
            hx-synx="closest form:abort"
            hx-target="#form-wrapper"
        >+</button>
    </div>
    <?php } ?>
    <button 
        hx-post="save.php" 
        hx-confirm="Are you sure you are ready to submit?"
        hx-target="body"
    >Save</button>
</form>
