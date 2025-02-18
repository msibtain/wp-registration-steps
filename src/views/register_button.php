<?php if (!isset($_GET['tsr'])) { // Make sure that user registration is not done yet; ?>
<div class="street_legal_reg" style="clear: both;">
    <?php
    $button_url  = add_query_arg(['ts' => 1], get_permalink( 731 ));
    ?>
    Do you need street legal registration too?
    <a href="<?php echo $button_url; ?>" style="float: none;" class="button custom-button-register"><?php echo __("Yes"); ?></a>
</div>
<?php } ?>