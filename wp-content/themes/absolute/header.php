<?php
/**
 * @package Absolute
 */
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <title>
        <?php
            global $page, $paged, $absolute_options;
            wp_title();
        ?>
    </title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="header">
    <div id="header-block">
        <div id="header_right">
            <div id="header_contact">
                <div class="text">
                    <?php $cur_lang = stella_get_current_lang(); ?>
                    <?php if ($cur_lang == 'ru'): ?> ГАРЯЧАЯ ЛИНИЯ <?php endif; ?>
                    <?php if ($cur_lang == 'uk'): ?> ГАРЯЧА ЛІНІЯ <?php endif; ?>
                    <br />
                    <div class="phone">0 800 <span>32 23 566</span></div>
                </div>
            </div>
            <?php // if($absolute_options['show_search']): ?>
                <?php // get_search_form(); ?>
            <?php // endif; ?>
            
            
            <?php if(!dynamic_sidebar('sidebar-5')): ?>
            <?php endif; ?>
        </div>
        <div class="site-title">
            <a href="<?php echo home_url(); ?>">&nbsp;</a>
        </div>
        <?php // if(trim(get_bloginfo('description')) != ''): ?>
        <!--<p class="site-desc"><?php // bloginfo('description'); ?></p>-->
        <?php // endif; ?>

        
    </div>
</div>
<?php wp_nav_menu(array('theme_location' => 'primary', 'container_class' => 'main-menu clearfix', 'depth' => 1)); ?>    
<div id="wrapper">
    <?php if($absolute_options['enable_slideshow'] and is_front_page()): ?>
    <div id="slider_container">
        <div class="slideshow">
            <?php absolute_slideshow(); ?>
        </div>
    </div>
    <?php endif; ?>
    <div id="content"> <!-- Start of content -->