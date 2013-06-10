<?php
/**
 * @package Absolute
 */
?>
<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
    <input type="text" value="" name="s" id="s" />
    <input type="submit" id="searchsubmit" value="<?php _e('Search','absolute'); ?>" />
</form>