<?php
/**
 * @package Absolute
 */
?>
<?php
/**
 * Defining the layout measurements. Change this values to increase/decrease width/height of layouts
 * On changing this values, make sure also change the thumbnail images set below
 * And then run the "Regenerate Thumbnail" plugin to reset the thumbnail sizes
 */
// Full content width
define('ABSOLUTE_WRAPPER_WIDTH',900);
// Sidebar width
define('ABSOLUTE_SIDEBAR_WIDTH',250);
// Post content width
define('ABSOLUTE_CONTENT_WIDTH',(ABSOLUTE_WRAPPER_WIDTH - ABSOLUTE_SIDEBAR_WIDTH));
// Post Slideshow height
define('ABSOLUTE_SLIDER_HEIGHT',380);
// Header height
define('ABSOLUTE_HEADER_HEIGHT',140);

/**
 * Setting the content width based on theme's layout
 */
if (!isset($content_width))
    $content_width = ABSOLUTE_CONTENT_WIDTH;

/**
 * Calling the theme options template and retrieving the theme options
 */
require(get_template_directory().'/admin/theme-options.php');
$absolute_options = absolute_get_options();

/**
 * Telling wordpress to run absolute_setup whenever "after_setup_theme" hook is run
 */
add_action('after_setup_theme', 'absolute_setup');
if (!function_exists('absolute_setup')):
function absolute_setup() {

    /* Make Absolute available for translation.
      * Translations can be added to the /languages/ directory.
      * If you're building a theme based on Absolute, use a find and replace
      * to change 'absolute' to the name of your theme in all the template files.
    */
    load_theme_textdomain('absolute', get_template_directory().'/languages' );
    add_editor_style();
    add_theme_support('automatic-feed-links');
    add_theme_support('post-thumbnails');

    /**
     * This sets up the image size for the slideshow.
     * If you change the width/height of your content and the slider,
     * you will have to modify the width and height mentioned below as well
     * Default width for the slide is set as: WRAPPER_WIDTH - 90px
     * Default height for slide is set as SLIDER_HEIGHT - 80px
     */
    add_image_size('absolute-slide-image',760,300,true);

    /**
     * This sets up the image size for the thumbnail in the magazine style layout.
     * If you want to change the width/height of the thumbnail,
     * you will have to modify the width and height mentioned below
     */
    add_image_size('absolute-featured-image',200,200,true);

    /**
     * This sets up the image size for the thumbnail in the portfolio.
     * If you want to change the width/height of the thumbnail,
     * you will have to modify the width and height mentioned below
     */
    add_image_size('absolute-portfolio-image',200,160,true);

    register_nav_menu('primary', __('Primary Menu', 'absolute'));
    register_nav_menu('bottom', __('Bottom Menu', 'absolute'));
    add_theme_support('post-formats', array('link', 'gallery', 'status', 'quote', 'image', 'video'));
    if(absolute_is_wp_version('3.4')) {
	add_theme_support('custom-background');
    } else {
	add_custom_background();
    }
    if(absolute_is_wp_version('3.4')) {
        $defaults = array(
            // default header image
            'default_image' => '',  //%s/images/headers/header1.jpg
            // random header image rotation
            'random-default' => true,
            'width' => ABSOLUTE_WRAPPER_WIDTH,
            'height' => ABSOLUTE_HEADER_HEIGHT,
            'flex-height' => false,
            'flex-width' => false,
            // default header title text color
            'default-text-color' => 'ffffff',
            'header-text' => true,
            'uploads' => true,
            'wp-head-callback' => 'absolute_header_style',
            'admin-head-callback' => 'absolute_admin_header_style'
        );
        add_theme_support('custom-header',$defaults);
    } else {
        // default header title text color
        define('HEADER_TEXTCOLOR','ffffff');
        // default header image
        define('HEADER_IMAGE',''); //%s/images/headers/header1.jpg
        define('HEADER_IMAGE_WIDTH',ABSOLUTE_WRAPPER_WIDTH);
        define('HEADER_IMAGE_HEIGHT',ABSOLUTE_HEADER_HEIGHT);
        add_theme_support('custom-header',array('random-default' => true));
        add_custom_image_header('absolute_header_style','absolute_admin_header_style');
    }
}
endif;

/**
 * This removes the default gallery styling applied by wordpress
 */
add_filter('use_default_gallery_style', '__return_false');

/**
 * Default menu to use if custom menu is not used
 */
function absolute_page_menu_args($args) {
    $args['show_home'] = false;
    $args['menu_class'] = 'main-menu clearfix';
    return $args;
}
add_filter('wp_page_menu_args', 'absolute_page_menu_args');

/**
 * Styling for custom header
 */
function absolute_header_style() {
    global $absolute_options;
    ?>
    <style type="text/css">
        <?php if(trim(get_header_image()) != ''): ?>
	#header { background-image: url(<?php header_image(); ?>); background-repeat: repeat; }
	<?php endif; ?>
        #header .site-title, #header .site-title a, #header .site-desc { color: #<?php echo get_header_textcolor(); ?>; }
        <?php if('blank' == get_header_textcolor()): ?>
        #header .site-title, #header .site-desc { display: none; }
        <?php endif; ?>

    </style>
    <?php
}

/**
 * Styling for custom header in admin
 */
function absolute_admin_header_style() {
    ?>
    <style type="text/css">
        #headimg { height: <?php echo HEADER_IMAGE_HEIGHT; ?>px; background-repeat: repeat; background-color: #2f2822; }
        #headimg h1 { line-height: 100% !important; font-family: Helvetica, Arial, sans-serif; font-size: 30px; font-weight: 700; text-shadow: none; padding: 0 !important; margin: 0 !important; margin-left: 20px !important; position: relative; top: 25%; padding: 0; line-height: 1; }
        #headimg h1 a { text-decoration: none; }
        #headimg #desc { font-family: "HelveticaNeue", "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 13px; text-shadow: none; position: relative; top: 20%; padding: 0 !important; margin-top: 20px !important; margin-left: 20px !important; }
    </style>
    <?php
}

/**
 * Enqueue javascript files required by theme
 */
function absolute_enqueue_head_scripts() {
    global $absolute_options;
    if(is_admin())
	return;
    if(is_singular() && get_option('thread_comments'))
        wp_enqueue_script('comment-reply');
    wp_enqueue_script('jquery');
    if($absolute_options['enable_slideshow'] and is_front_page()) {
	wp_register_script('absolute-slider-js', get_template_directory_uri().'/inc/slider/slider.js','jquery');
	wp_enqueue_script('absolute-slider-js');
    }
    wp_register_script('absolute-dropdown-js', get_template_directory_uri().'/js/dropdown.js','jquery');
    wp_register_script('absolute-theme-js', get_template_directory_uri().'/js/absolute.js','jquery');
    wp_enqueue_script('absolute-dropdown-js');
    wp_enqueue_script('absolute-theme-js');
}
add_action('wp_enqueue_scripts', 'absolute_enqueue_head_scripts');

/**
 * Loading javascripts into the footer
 */
function absolute_load_foot_scripts() {
    global $absolute_options;
    $scripts = '';
    if($absolute_options['enable_slideshow'] and is_front_page()) {
	$scripts .= '<script type="text/javascript">jQuery(document).ready(function() { jQuery("#coin-slider").coinslider({ width: '.(ABSOLUTE_WRAPPER_WIDTH - 96).', height: '.(ABSOLUTE_SLIDER_HEIGHT - 82).', delay: '.($absolute_options['slideshow_interval'] * 1000).' }); });</script>'."\n";
    }
    echo $scripts;
}
add_action('wp_footer','absolute_load_foot_scripts');

/**
 * Add dynamic script & styling statements to the <head>
 */
function absolute_enqueue_styles() {
    global $absolute_options;
    $style = "";
	if(trim($absolute_options['custom_favicon']) != "") $style .= '<link rel="shortcut icon" href="'.$absolute_options['custom_favicon'].'"/>'."\n";
    $style .= '<!--[if IE]> <link rel="stylesheet" type="text/css" media="all" href="'.get_template_directory_uri().'/ie.css" /> <![endif]-->'."\n";
    if($absolute_options['enable_slideshow'] and is_front_page())
	$style .= '<link rel="stylesheet" type="text/css" media="all" href="'.get_template_directory_uri().'/inc/slider/slider.css" />'."\n";
//    $style .= '<link rel="stylesheet" type="text/css" media="all" href="'.get_template_directory_uri().'/css/bootstrap.min.css" />'."\n";
    $style .= '<!--[if lt IE 9]><script src="'.get_template_directory_uri().'/js/html5.js" type="text/javascript"></script><![endif]-->'."\n";
    $style .= '<style type="text/css">'."\n";
    $style .= "\t".'#wrapper { width: '.ABSOLUTE_WRAPPER_WIDTH.'px; }'."\n";
    $style .= "\t".'#header { height: '.ABSOLUTE_HEADER_HEIGHT.'px; }'."\n";
    $style .= "\t".'#header .tablayout { height: '.ABSOLUTE_HEADER_HEIGHT.'px; }'."\n";
    $style .= "\t".'.container { width: '.(ABSOLUTE_CONTENT_WIDTH - 51).'px; }'."\n"; //Subtracting 51 for the padding + border
    $style .= "\t".'.full-container { width: '.(ABSOLUTE_WRAPPER_WIDTH - 51).'px; }'."\n"; //Subtracting 51 for the padding + border
    $style .= "\t".'.widget-area { width: '.(ABSOLUTE_SIDEBAR_WIDTH - 51).'px; }'."\n"; //Subtracting 51 for the padding + border
    $style .= absolute_background_style();
    $style .= absolute_typography_style();
//    if($absolute_options['sidebar_layout'] == 'one-left-sidebar' and !is_404() and !is_page_template('fullwidth-template.php') and !is_page_template('portfolio-template.php')) {
//        $style .= "\t".'#content { background-image: url('.get_template_directory_uri().'/images/wrapper_left_bg.png); background-repeat: repeat-y; background-position: -'.(500 - ABSOLUTE_SIDEBAR_WIDTH).'px 0; }'."\n";
//    } else if(!is_404() and !is_page_template('fullwidth-template.php') and !is_page_template('portfolio-template.php')) {
//        $style .= "\t".'#content { background-image: url('.get_template_directory_uri().'/images/wrapper_right_bg.png); background-repeat: repeat-y; background-position: -'.(1500 - ABSOLUTE_CONTENT_WIDTH).'px 0; }'."\n";
//    }
    $style .= "\t".'.entry-magazine { width: '.(ABSOLUTE_CONTENT_WIDTH - 51 - 244).'px; }'."\n";
    $style .= '</style>'."\n";
    echo $style;
}
add_action('wp_head','absolute_enqueue_styles');

/**
 * Adjusting border colors and shadows based on the custom background color
 */
function absolute_background_style() {
    global $absolute_options;
    $bgcolor = get_theme_mod('background_color');
    $bgimage = get_theme_mod('background_image');
    $style = '';
    if((trim($bgcolor) != '' and trim($bgimage) != '') or (trim($bgcolor) != '' and trim($bgimage) == '')) {
        $style .= "\t".'#wrapper { -webkit-box-shadow:0px 0px 3px '.absolute_rgb_color(absolute_color_darken($bgcolor,40),0.4).'; -moz-box-shadow:0px 0px 3px '.absolute_rgb_color(absolute_color_darken($bgcolor,40),0.4).'; box-shadow:0px 0px 3px '.absolute_rgb_color(absolute_color_darken($bgcolor,40),0.4).'; }'."\n";
        $style .= "\t".'#content, #slider_container, .main-menu { border-left: 1px #'.absolute_color_darken($bgcolor,40).' solid; border-right: 1px #'.absolute_color_darken($bgcolor,40).' solid; }'."\n";
        $style .= "\t".'#header { border-left: 1px #'.absolute_color_darken($bgcolor,40).' solid; border-right: 1px #'.absolute_color_darken($bgcolor,40).' solid; border-top: 1px #'.absolute_color_darken($bgcolor,40).' solid; }'."\n";
    } else if(trim($bgimage) != '' and trim($bgcolor) == '') {
        $style .= "\t".'#wrapper { -webkit-box-shadow:0px 0px 3px '.absolute_rgb_color(absolute_color_darken("#111111",40),0.4).'; -moz-box-shadow:0px 0px 3px '.absolute_rgb_color(absolute_color_darken("#111111",40),0.4).'; box-shadow:0px 0px 3px '.absolute_rgb_color(absolute_color_darken("#111111",40),0.4).'; }'."\n";
        $style .= "\t".'#content, #slider_container, .main-menu { border-left: 1px #'.absolute_color_darken("#111111",40).' solid; border-right: 1px #'.absolute_color_darken("#111111",40).' solid; }'."\n";
        $style .= "\t".'#header { border-left: 1px #'.absolute_color_darken("#111111",40).' solid; border-right: 1px #'.absolute_color_darken("#111111",40).' solid; border-top: 1px #'.absolute_color_darken("#111111",40).' solid; }'."\n";
    }
    return $style;
}

/**
 * Custom font & colors for theme
 */
function absolute_typography_style() {
    global $absolute_options;
    $style = '';
    $style .= "\t".'body { font-family: '.$absolute_options['content_font'].'; }'."\n";
    $style .= "\t".'body { color: #'.$absolute_options['font_color'].'; }'."\n";
    $style .= "\t".'.widget a, .entry-meta a, .entry-content a, .entry-utility a, #author-info a, .comment a, .pingback a, #respond a { color: #'.$absolute_options['link_color'].'; }'."\n";
    $style .= "\t".'#searchsubmit, .more-link:hover, .page-link a:hover span, .page-link span, .entry-content input[type="submit"], .entry-content input[type="reset"], .entry-content input[type="button"], .navigation a:hover, .navigation .current, .single-navigation span a:hover, #commentform input[type="submit"], #commentform input[type="reset"], #commentform input[type="button"] {
        background-color: #'.$absolute_options['button_color'].'; text-shadow: #'.absolute_color_darken($absolute_options['button_color'],40).' -1px -1px !important; border-color: #'.absolute_color_darken($absolute_options['button_color'],40).';
    }'."\n";
    $style .= "\t".'#searchsubmit:hover, .entry-content input[type="submit"]:hover, .entry-content input[type="reset"]:hover, .entry-content input[type="button"]:hover, #commentform input[type="submit"]:hover, #commentform input[type="reset"]:hover, #commentform input[type="button"]:hover {
        background-color: #'.absolute_color_darken($absolute_options['button_color'],40).'; text-shadow: #'.absolute_color_darken($absolute_options['button_color'],70).' -1px -1px !important; border-color: #'.absolute_color_darken($absolute_options['button_color'],70).';
    }'."\n";
    $style .= "\t".'.widget, .entry-content, .entry-utility, #author-info, .entry-share, .comment-content, .pingback-body, .nocomments, .nopassword, #respond { font-size: '.$absolute_options['font_size'].'%; }'."\n";
    return $style;
}

/**
 * Portfolio column class
 */
function absolute_portfolio_class() {
    global $absolute_options;
    if($absolute_options['portfolio_columns'] == 2)
	echo 'gallery gallery-columns-2';
    elseif($absolute_options['portfolio_columns'] == 4)
	echo 'gallery gallery-columns-4';
    else
	echo 'gallery gallery-columns-3';
}

/**
 * Register sidebars and widgetized areas
 */
function absolute_widgets_init() {
    register_sidebar(array(
        'name' => __('Main Sidebar', 'absolute'),
        'id' => 'sidebar-1',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Footer Widget Area 1', 'absolute'),
        'id' => 'sidebar-2',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Footer Widget Area 2', 'absolute'),
        'id' => 'sidebar-3',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Footer Widget Area 3', 'absolute'),
        'id' => 'sidebar-4',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => __('Header Area', 'absolute'),
        'id' => 'sidebar-5',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => "</div>",
//        'before_title' => '<h3 class="widget-title">',
//        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'absolute_widgets_init');

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function absolute_footer_sidebar_class() {
    $count = 0;
    if(is_active_sidebar('sidebar-2'))
        $count++;
    if(is_active_sidebar('sidebar-3'))
        $count++;
    if(is_active_sidebar('sidebar-4'))
        $count++;
    $class = '';
    switch($count) {
        case '1':
            $class = 'one';
            break;
        case '2':
            $class = 'two';
            break;
        case '3':
            $class = 'three';
            break;
    }
    if($class)
        echo 'class="'.$class.'"';
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own absolute_posted_on to override in a child theme
 */
if (!function_exists('absolute_posted_on')):
function absolute_posted_on() {
	printf(__('<span class="entry-date"><a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span><span class="entry-author"><a href="%4$s" title="%5$s" rel="author">%6$s</a></span>', 'absolute' ),
		esc_url(get_permalink()),
		esc_attr(get_the_time()),
		esc_html(get_the_date()),
		esc_url(get_author_posts_url(get_the_author_meta('ID'))),
		esc_attr(sprintf(__('View all posts by %s', 'absolute'), get_the_author())),
		get_the_author()
	);
}
endif;

/**
 * Filters Title for the Site
 */
function absolute_filter_wp_title($title) {
    $site_name = get_bloginfo('name');
    if(trim($title) != '') {
	$title = str_replace('&raquo;','',$title);
	$filtered_title = $title.' | '.$site_name;
    } else
	$filtered_title = $site_name;
    if (is_front_page()) {
        $site_description = get_bloginfo('description');
        $filtered_title .= ' | '.$site_description;
    }
    return $filtered_title;
}
add_filter('wp_title', 'absolute_filter_wp_title');

/**
 * Prints HTML with post category and tags.
 */
function absolute_utility() {
    global $absolute_options;
    $utility_text = "";
    $categories_list = get_the_category_list(__(', ', 'absolute'));
    $tag_list = get_the_tag_list('', __(', ', 'absolute'));
    if($categories_list != "")
        $utility_text .= '<p><span class="utility-title">'.__('Posted under: ','absolute').'</span>'.$categories_list.'</p>';
    if($tag_list != "")
        $utility_text .= '<p><span class="utility-title">'.__('Tagged as: ','absolute').'</span>'.$tag_list.'</p>';

    if($utility_text != "" and $absolute_options['show_single_utility']) {
        echo '<div class="entry-utility">';
        echo $utility_text;
        echo '</div>';
    }
}

/**
 * Displays Author's description on Single post templates
 */
function absolute_post_author_info() {
    global $absolute_options;
    // If a user has filled out their description and this is a multi-author blog, show a bio on their entries
    if(get_the_author_meta('description') && (!function_exists('is_multi_author') || is_multi_author())) {
        ?>
        <div id="author-info" class="clearfix">
            <div id="author-avatar">
                <?php echo get_avatar(get_the_author_meta('user_email'), apply_filters('absolute_author_bio_avatar_size', 48)); ?>
            </div>
            <div id="author-description">
                <h3><?php printf(__('About %s', 'absolute'), get_the_author()); ?></h3>
                <p><?php the_author_meta('description'); ?></p>
                <p>
                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>" rel="author"><?php printf(__('View all posts by %s <span class="meta-nav">&rarr;</span>', 'absolute'), get_the_author()); ?></a>
                </p>
            </div>
        </div>
        <?php
    }
}

/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function absolute_excerpt_length($length) {
    return 40;
}
add_filter('excerpt_length', 'absolute_excerpt_length');

/**
 * Returns a "Continue Reading" link for excerpts
 */
function absolute_continue_reading_link() {
    return '<p><a class="more-link" href="'.esc_url(get_permalink()).'">'.__('Continue reading', 'absolute').'</a></p>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with a absolute_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function absolute_auto_excerpt_more($more) {
    return absolute_continue_reading_link();
}
add_filter('excerpt_more', 'absolute_auto_excerpt_more');

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function absolute_custom_excerpt_more( $output ) {
    if(has_excerpt() && !is_attachment()) {
        $output .= absolute_continue_reading_link();
    }
    return $output;
}
add_filter('get_the_excerpt', 'absolute_custom_excerpt_more');

function absolute_post_bookmark() {
    global $absolute_options;
    $bookmark_list = array(
        'facebook' => array('value' => 'facebook', 'title' => __('Facebook','absolute'), 'url' => 'http://www.facebook.com/sharer.php?u='.get_permalink().'&amp;t='.get_the_title()),
        'twitter' => array('value' => 'twitter', 'title' => __('Twitter','absolute'), 'url' => 'http://twitter.com/intent/tweet?text='.get_the_title().'%20-%20'.get_permalink()),
        'delicious' => array('value' => 'delicious', 'title' => __('Delicious','absolute'), 'url' => 'http://del.icio.us/post?url='.get_permalink().'&amp;amp;title='.get_the_title()),
        'digg' => array('value' => 'digg', 'title' => __('Digg','absolute'), 'url' => 'http://digg.com/submit?url='.get_permalink()),
        'tumblr' => array('value' => 'tumblr', 'title' => __('Tumblr','absolute'), 'url' => 'http://www.tumblr.com/share?v=3&amp;u='.get_permalink().'&amp;t='.get_the_title())
    );
    $output = "";
    if($absolute_options['show_bookmark_buttons']) {
        $output .= '<div class="entry-share">'."\n";
        $output .= '<span class="entry-share-title">'.__('Share this: ','absolute').'</span>'."\n";
        foreach($bookmark_list as $entry) {
            $output .= '<a class="entry-share-item" href="'.$entry['url'].'"><img src="'.get_template_directory_uri().'/images/'.$entry['value'].'_share_icon.png"/></a>'."\n";
        }
        $output .= '</div>'."\n";
    }
    echo $output;
}

/**
 * Creating the slideshow items
 */
function absolute_slideshow() {
    global $absolute_options;
    $slides = absolute_get_posts($absolute_options['slideshow_count'],100,'post');
    $output = '';
    $slideshow = '';
    foreach($slides as $entry) {
        $slide_text = '<h3 class="slide-title">'.$entry['title'].'</h3>';
        if(trim($entry['excerpt']) != '')
            $slide_text .= '<p class="slide-desc">'.$entry['excerpt'].'</p>';
	if(has_post_thumbnail($entry['id'])) {
            $img = get_the_post_thumbnail($entry['id'],'absolute-slide-image');
        } else $img = '<img class="absolute-default-slide" src="'.get_template_directory_uri().'/images/default_slide_image.png"/>';
	$output .= '<a href="'.$entry['permalink'].'">'.$img.'<span>'.$slide_text.'</span></a>'."\n";
    }
    if(trim($output) != '') {
	$slideshow .= '<div id="coin-slider">'."\n";
        $slideshow .= $output;
        $slideshow .= '</div>'."\n";
    }
    echo $slideshow;
}

/**
 * Create pagination link for posts
 */
function absolute_get_pagination($range = 4){
    global $paged, $wp_query;
    $max_page = 0;
    if (!$max_page) {
        $max_page = $wp_query->max_num_pages;
    }
    if($max_page > 1){
        echo '<div class="navigation clearfix">'."\n";
        if(!$paged){
            $paged = 1;
        }
        if($paged != 1){
            echo "<a href=".get_pagenum_link(1).">".__('First','absolute')."</a>";
        }
        previous_posts_link(' &laquo; ');
        if($max_page > $range){
            if($paged < $range){
                for($i = 1; $i <= ($range + 1); $i++){
                    echo "<a href='".get_pagenum_link($i) ."'";
                    if($i==$paged) echo " class='current'";
                    echo ">".number_format_i18n($i)."</a>";
                }
            }
            elseif($paged >= ($max_page - ceil(($range/2)))){
                for($i = $max_page - $range; $i <= $max_page; $i++){
                    echo "<a href='".get_pagenum_link($i) ."'";
                    if($i==$paged) echo " class='current'";
                    echo ">".number_format_i18n($i)."</a>";
                }
            }
            elseif($paged >= $range && $paged < ($max_page - ceil(($range/2)))){
                for($i = ($paged - ceil($range/2)); $i <= ($paged + ceil(($range/2))); $i++){
                    echo "<a href='".get_pagenum_link($i) ."'";
                    if($i==$paged) echo " class='current'";
                    echo ">".number_format_i18n($i)."</a>";
                }
            }
        }
        else{
            for($i = 1; $i <= $max_page; $i++){
                echo "<a href='".get_pagenum_link($i) ."'";
                if($i==$paged) echo " class='current'";
                echo ">".number_format_i18n($i)."</a>";
            }
        }
        next_posts_link(' &raquo; ');
        if($paged != $max_page){
            echo " <a href=".get_pagenum_link($max_page).">".__('Last','absolute')."</a>";
        }
        echo '</div>'."\n";
    }
}

/**
 * Function retrieve post information outside of the loop
 */
function absolute_get_posts($number=5, $excerpt_length=50, $post_type='') {
    $args = 'numberposts='.$number;
    $output = array();
    if($post_type != '')
        $args .= '&post_type='.$post_type;
    $entries = get_posts($args);
    foreach($entries as $entry) {
        setup_postdata($entry);
        $postid = $entry->ID;
        $posttitle = $entry->post_title;
        $postdate = $entry->post_date;
        $postlink = get_permalink($entry->ID);
        $postexcerpt = absolute_trim_words(get_the_excerpt(),$excerpt_length);
        $postcontent = $entry->post_content;
        $postcategory = get_the_category($postid);
	$postcategory = $postcategory[0]->cat_name;
        if(!comments_open($entry->ID)) {
    	    $comment_count = '';
	    $comment_link_text = __('Comments off','absolute');
	    $comment_link = '';
	} else {
	    $comment_count = $entry->comment_count;
	    if($comment_count == 1) {
		$comment_link_text = __(' comment','absolute');
		$comment_link = get_comments_link($entry->ID);
	    } else if($comment_count == 0) {
		$comment_link_text = __(' comments','absolute');
		$comment_link = $postlink.'#respond';
	    } else {
		$comment_link_text = __(' comments','absolute');
		$comment_link = get_comments_link($entry->ID);
	    }
	}
        array_push($output, array('id' => $postid, 'title' => $posttitle, 'date' => $postdate, 'permalink' => $postlink, 'excerpt' => $postexcerpt, 'content' => $postcontent, 'category' => $postcategory, 'comment_count' => $comment_count, 'comment_link' => $comment_link, 'comment_link_text' => $comment_link_text));
    }
    return $output;
}

/**
 * Function to trim words in content/excerpt
 */
function absolute_trim_words($str, $n, $delim='...') {
    $len = strlen($str);
    if ($len > $n) {
        preg_match('/(.{'.$n.'}.*?)\b/', $str, $matches);
        return rtrim($matches[1]).$delim;
    } else {
        return $str;
    }
}

/**
 * Function to return darker color shade
 */
function absolute_color_darken($color, $dif=80){
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb = '';
    for ($x=0;$x<3;$x++){
        $c = hexdec(substr($color,(2*$x),2)) - $dif;
        $c = ($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
    return $rgb;
}

/**
 * Checks the version of WP
 */
function absolute_is_wp_version($is_ver) {
    $wp_ver = explode('.', get_bloginfo('version'));
    $is_ver = explode('.', $is_ver);
    for($i=0; $i<=count($is_ver); $i++)
        if(!isset($wp_ver[$i])) array_push($wp_ver, 0);
    foreach($is_ver as $i => $is_val)
        if($wp_ver[$i] < $is_val) return false;
    return true;
}

/**
 * Converts Hex Color to RGB Color
 */
function absolute_rgb_color($hex,$opacity) {
    $hex = ereg_replace("#", "", $hex);
    $color = array();
    if(strlen($hex) == 3) {
        $color['r'] = hexdec(substr($hex, 0, 1).$r);
        $color['g'] = hexdec(substr($hex, 1, 1).$g);
        $color['b'] = hexdec(substr($hex, 2, 1).$b);
    }
    else if(strlen($hex) == 6) {
        $color['r'] = hexdec(substr($hex, 0, 2));
        $color['g'] = hexdec(substr($hex, 2, 2));
        $color['b'] = hexdec(substr($hex, 4, 2));
    }
    $rgbcolor = 'rgba('.$color['r'].','.$color['g'].','.$color['b'].','.$opacity.')';
    return $rgbcolor;
}

if(!function_exists('absolute_comment')):
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own absolute_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 * 
 */
function absolute_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    switch($comment->comment_type):
        case 'pingback':
	case 'trackback':
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
            <article id="comment-<?php comment_ID(); ?>" class="pingback">
                <div class="pingback-body">
                    <?php _e('Pingback:', 'absolute'); ?> <?php comment_author_link(); ?><?php edit_comment_link(__('Edit', 'absolute'), '<span class="pingback-edit-link">', '</span>'); ?>
                </div>
            </article>
        <?php
        break;
        default:
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment">
            <div class="comment-vcard">
                <?php echo get_avatar($comment, '32'); ?>
                <?php comment_reply_link(array_merge($args, array('reply_text' => __('Reply', 'absolute'), 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                <?php edit_comment_link( __('Edit', 'absolute'), '<span class="edit-link">', '</span>'); ?>
            </div>
            <div class="comment-body">
                <p class="comment-author"><?php printf(__('<strong>%1$s</strong> on %2$s <span class="says">said:</span>', 'absolute'), sprintf('<span class="fn">%s</span>', get_comment_author_link()), sprintf('<a href="%1$s">%2$s</a>', esc_url(get_comment_link($comment->comment_ID)), sprintf(__('%1$s at %2$s', 'absolute'), get_comment_date(), get_comment_time()))); ?></p>
                <?php if($comment->comment_approved == '0'): ?>
                    <p class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.', 'absolute'); ?></p>
                <?php endif; ?>
                <div class="comment-content"><?php comment_text(); ?></div>
            </div>
        </article>
<?php
        break;
    endswitch;
}
endif;
?>