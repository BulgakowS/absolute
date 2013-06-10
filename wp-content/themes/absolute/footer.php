<?php
/**
 * @package Absolute
 */
global $page, $paged, $absolute_options;
?>
    </div> <!-- End of content -->
    
</div> <!-- End of wrapper -->
<div id="footer">
    <?php wp_nav_menu(array('theme_location' => 'bottom', 'container_class' => 'bottom-menu clearfix', 'depth' => 2)); ?>
    <?php if(!is_404()) get_sidebar('footer'); ?>
    <div id="footer-sosial">
        <?php if(trim($absolute_options['facebook_user']) != ''): ?>
            <a class="social-icons" href="http://www.facebook.com/<?php echo $absolute_options['facebook_user']; ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/facebook_icon.png"/></a>
        <?php endif; ?>
        <?php if(trim($absolute_options['twitter_user']) != ''): ?>
            <a class="social-icons" href="http://www.twitter.com/<?php echo $absolute_options['twitter_user']; ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/twitter_icon.png"/></a>
        <?php endif; ?>
        <?php if($absolute_options['enable_rss']): ?>
            <a class="social-icons" href="<?php bloginfo('rss2_url'); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/rss_icon.png"/></a>
        <?php endif; ?>
    </div>
    <div id="footer-credits">
        <?php _e('&copy; 2007-'.date('Y'),'absolute'); ?> &laquo;Абсолют&raquo;<BR />
        Все права защищены.
    </div>
</div>
<?php wp_footer(); ?>
<script>
    jQuery(document).ready(function(){
        var lis = jQuery('#menu-main > li');
        lis.css({'width': 100/lis.length + '%'});
    });
</script>
</body>
</html>