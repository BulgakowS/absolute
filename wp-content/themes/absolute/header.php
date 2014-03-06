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

	<meta property="og:image" content="<?php _e(bloginfo('template_url'))?>/imgs/absolut-logo116x116.png" />
	<meta property="og:site_name" content="<?php bloginfo('name')?>" />
	<meta property="og:title" content="<?php bloginfo('description')?>" />

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

<!-- FB init -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/ru_RU/all.js#xfbml=1&appId=655446031157437";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
<!-- /FB -->
<!-- VK init -->
	<script type="text/javascript" src="//vk.com/js/api/openapi.js?105"></script>
	<script type="text/javascript"> VK.init({apiId: 4179921, onlyWidgets: true}); </script>
<!-- /VK -->
</head>
<body <?php body_class(); ?>>
<div id="wrap_all">
    <div id="header">
        <div id="header-block">
            <div id="header_left">
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