<?php
/*
	Plugin Name: Stella plugin free
	Plugin URI: http://store.theme.fm/plugins/stella/
	Description: Stella plugin for WordPress is designed to give the user a simple and effective way to build a multi-language website. 
	Version: 1.3 build 42
	Author: Frumatic
	Author URI:
	Usage: Everywhere
 
	== Programmer's guide ==

	== How it works? ==

	First of all, plugin looks what is language on site.
	It compare HTTP_HOST or REQUEST_URI (depends on use_hosts option ) with registrated on options page languages.
	After that plugin add STELLA_CURRENT_LANG, STELLA_DEFAULT_LANG constants.
	Plugin add filters to "get_content"-like functions to substitute values.
	If STELLA_CURRENT_LANG don't equal STELLA_DEFAULT_LANG content would be replaced by it corresponding language version.
	To save site links plugin add filters to option_siteurl, option_home, content_url.

	== About storing data ==

	Plugin uses metabox to store language versions of post and single input data (translate_meta).
	Actions:
	- add_meta_boxes (adding fields to post)
	- save_post (to save all added fields)

	To store category's plugin uses "options". So, it register "category_metalangs" option.
	and uses get_option/update_option functions to get/save category's language versions.
	Actions:
	- category_add_form_fields (adding fields to new category/tag form)
	- category_edit_form_fields (adding fields to edit category/tag form)
	- created_category (to save fields when new category/tag was created)
	- edited_category (to save fields when category/tag was edited)

	== Displaying data ==

	Plugin add following filters to substitute content:
	- the_title (post title)
	- the_content (post content)
	- get_the_terms (category/tag names and descriptions)
	- get_terms (category/tag names and descriptions)
	- get_term (category/tag name and description)
	- get_post_metadata ( to translate single input metadata)

	== Options structure ==

	Options |- langs (array)
	        |- use_host (boolean)
			|- use_default_lang_values

	about langs array:

	langs |- default - [ prefix, name, host ]
	      |- others -|- prefix1 - [ prefix, name, host ]
	                 |- prefix2 - [ prefix, name, host ]
	                 |- prefixN - [ prefix, name, host ]
 */
if( ! class_exists('Stella_Plugin') ){
	
	include_once 'api.php';
	if( stella_file_exists( 'updater.php' ) ) include_once 'updater.php';
	
	/*
	 * Localization class
	 * 
	 * manages plugin options
	 * controls other classes 
	 */	
	class Stella_Plugin {

		private $langs;
		private $use_hosts;
		private $use_default_lang_values;
		private $empty_field_notices;
		
		function __construct() {
			load_plugin_textdomain('stella-plugin',false, dirname( plugin_basename( __FILE__ ) ) .'/lang/');
			
			$this->register_options();
			$this->load_options();
				
			if( ! stella_file_exists( 'classes/class-multisite-allow.php' ) && is_multisite() ){
				add_action( 'admin_notices', array( $this, 'multisite_not_allow' ) );
			}else{
				if( stella_file_exists( 'classes/class-url-changer.php' ) ) include_once 'classes/class-url-changer.php';
				if( stella_file_exists( 'classes/class-stella-options-page.php' ) ) include_once 'classes/class-stella-options-page.php';
				if( stella_file_exists( 'classes/class-admin-bar-switcher.php' ) ) include_once 'classes/class-admin-bar-switcher.php';
				if( stella_file_exists( 'classes/class-post-localizer.php' ) ) include_once 'classes/class-post-localizer.php';
				if( stella_file_exists( 'classes/class-category-localizer.php' ) ) include_once 'classes/class-category-localizer.php';
				if( stella_file_exists( 'classes/class-bloginfo-localizer.php' ) ) include_once 'classes/class-bloginfo-localizer.php';
				if( stella_file_exists( 'classes/class-custom-field-localizer.php' ) ) include_once 'classes/class-custom-field-localizer.php';
				if( stella_file_exists( 'classes/class-theme-menus-localizer.php' ) ) include_once 'classes/class-theme-menus-localizer.php';
				if( stella_file_exists( 'classes/class-thumbnail-localizer.php' ) ) include_once 'classes/class-thumbnail-localizer.php';
				if( stella_file_exists( 'classes/class-multi-post-thumbnails.php' ) ) include_once 'classes/class-multi-post-thumbnails.php';
				if( stella_file_exists( 'classes/class-stella-language-widget.php' ) ) include_once 'classes/class-stella-language-widget.php';
				if( stella_file_exists( 'classes/class-force-secondary-hosts-login.php' ) ) include_once 'classes/class-force-secondary-hosts-login.php';
				//if( stella_file_exists( 'classes/class-free-version-limitations.php' ) ) include_once 'classes/class-free-version-limitations.php';
				if( stella_file_exists( 'classes/class-filtered-string-localizer.php' ) ) include_once 'classes/class-filtered-string-localizer.php';
			}
			if ( ! defined('STELLA_DEFAULT_LANG') ) define('STELLA_DEFAULT_LANG', $this->langs['default']['prefix']);

			do_action('stella_init');
			do_action('stella_parameters', $this->langs, $this->use_hosts, $this->use_default_lang_values, $this->empty_field_notices);
			do_action('stella_lang_menu', $this->get_lang_menu());

			add_filter('stella_get_lang_list', array($this, 'get_lang_menu'));

			if ( $current = get_site_transient('update_plugins') ) 
				set_site_transient('update_plugins', $current);

			register_deactivation_hook( __FILE__, array( $this, 'on_deactivate' ) );
			
		}
		function is_permalinks_or_host_mode(){
			if ( ! current_user_can( 'manage_options' ) || is_permalinks_enabled()  )
				return true;

			$stella_page = parse_url( admin_url( '/' ) . 'options-general.php?page=stella-options' );
			if ( isset( $_POST['_wp_http_referer'] ) &&  $stella_page['path'] . '?' . $stella_page['query'] == $_POST['_wp_http_referer'] ) {

				if ( isset( $_POST['use-hosts'] ) && 'on' ==  $_POST['use-hosts'] )
					return true;

			} elseif ( $this->use_hosts ) {
				return true;
			}
		}
		function on_deactivate(){
			if( is_multisite() )
				delete_blog_option( get_current_blog_id(), 'stella-version' );
			else
				delete_option( 'stella-version' );	
			// don't show update notice if plugin deactivated
			if( stella_file_exists( 'updater.php' ) ) set_site_transient('update_plugins', null);
		}
		function register_options() {			
			$default_options = array(
				'langs' => array(
					'default' => array('prefix' => 'en', 'name' => 'English', 'host' => $_SERVER['HTTP_HOST']),
					'others' => array(),
				),
				'use_hosts' => false,
				'use_default_lang_values' => false,
				'empty_field_notices' => true,
			);

			if( is_multisite() ){
				add_blog_option(get_current_blog_id(), 'stella-options', $default_options, '', 'yes');
				if( false == get_blog_option(get_current_blog_id(), 'stella-version') ) 
						$this->update_from_rosetta();
				add_blog_option(get_current_blog_id(), 'stella-version', '1.2.29', '', 'yes');
			}else{
				add_option('stella-options', $default_options, '', 'yes');
				if( false == get_option('stella-version') )
					$this->update_from_rosetta();
				add_option('stella-version', '1.2.29', '', 'yes');
			}
		}
		function enable_permalinks_or_host_mode(){
			echo '<div id="message" class="error">';
			echo '<p><strong>'.__( 'Currently localization is not active. To enable Stella to localize your page, please set hosts mode on', 'stella-plugin' ).' <a href="' . admin_url( 'options-general.php?page=stella-options' ) . '">'.__( 'Stella plugin free settings','stella-plugin' ).'</a> '.__( 'or enable','stella-plugin' ).' <a href="' . admin_url( 'options-permalink.php' ) . '">'.__('Permalinks').'</a>.</strong></p></div>';
		}
		function multisite_not_allow(){
			echo '<div id="message" class="error"><p><strong>'.__( 'Stella-free is not avalable on multisite. Learn more about full version on ', 'stella-plugin' ).'<a href="http://store.theme.fm/plugins/rosetta/">theme.fm</a></strong></p></div>';
		}
		function update_from_rosetta(){ 
			// update options
			$old_options = array();
			if( false != get_option('rosetta-options') ){
				$old_options = get_option('rosetta-options');
				update_option( 'stella-options', $old_options );
				//delete_option( 'rosetta-options' );
			}
			if( is_multisite() && false != get_blog_option(get_current_blog_id(), 'rosetta-options') ){
				$old_options = get_blog_option(get_current_blog_id(), 'rosetta-options');
				update_blog_option( get_current_blog_id(), 'stella-options', $old_options );
				//delete_blog_option( get_current_blog_id(), 'rosetta-options' );
			}
			// update meta keys
			global $wpdb;
			if( file_exists( dirname(__FILE__) . '/lang-codes-list.txt') ){
				$lang_codes = file( dirname(__FILE__) . '/lang-codes-list.txt' );
				foreach( $lang_codes as $code){
					$exploded = explode( '/', $code );
					if( 2 == count( $exploded ) ){
						 $prefix = strtolower(trim($exploded['1']));
						 $wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_body-$prefix' WHERE meta_key = 'body-$prefix'");
						 $wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_title-$prefix' WHERE meta_key = 'title-$prefix'");
						 $wpdb->query("UPDATE $wpdb->postmeta SET meta_key = '_post_postimagediv-{$prefix}_thumbnail_id' WHERE meta_key = 'post_postimagediv-{$prefix}_thumbnail_id'");
					}
				}
			}
		}
		function load_options() {

			$options = array();
			if( is_multisite() ) {
				if( false == strpos($_SERVER['REQUEST_URI'],"site-settings.php") )
					$blog_id = get_current_blog_id();
				else
					$blog_id = $_GET['id'];

				$options = get_blog_option($blog_id, 'stella-options');
			}else{

				$options = get_option('stella-options');
			}
			$default_langs_array = array(
					'default' => array('prefix' => 'en', 'name' => 'English', 'host' => $_SERVER['HTTP_HOST']),
					'others' => array(),
				);
			$this->langs = ( isset( $options['langs'] ) ) ? $options['langs'] : $default_langs_array;
			$this->use_hosts = ( isset( $options['use_hosts'] ) ) ? $options['use_hosts'] : false;
			$this->use_default_lang_values = ( isset( $options['use_default_lang_values'] ) ) ? $options['use_default_lang_values'] : false;
			$this->empty_field_notices = ( isset( $options['empty_field_notices'] ) ) ? $options['empty_field_notices'] : true;
		}
		function get_language_list() {
			$lang_list = array();
			foreach ($this->langs['others'] as $prefix => $lang) {
				$lang_list[$prefix] = $lang['host'];
			}
			$lang_list[$this->langs['default']['prefix']] = $this->langs['default']['host'];
			return $lang_list;
		}
		function get_permalink( $host, $lang_prefix ) {

			$uri = $_SERVER['REQUEST_URI'];

			$path = '';
			// If multisite then remove blogname from $uri
			if ( is_multisite() && ! is_subdomain_install() ) {
				$site = stella_get_current_blog();

				$path = $site->path;
				if ( substr( $uri, 0, strlen( $site->path ) ) == $site->path )
					$uri = '/' . substr( $uri, strlen( $site->path ), strlen( $uri ) - strlen( $site->path ) );
			}

			// Remove lang prefix
			$lang_list = $this->get_language_list();
			$code = '/' . substr( $uri, 0, 4 ) . '/';
			$code = preg_replace('/\/{2,}/','/', $code);
			foreach ( $lang_list as $prefix => $lang ) {
				if ( '/' . $prefix . '/' == $code )
					$uri = substr( $uri, 4, strlen( $uri) - 4);
			}

			// If lang is in request uri ( like ?lang ), remove it
			if( defined('STELLA_CURRENT_LANG') ){
				$uri = str_replace ( '?lang=' . STELLA_CURRENT_LANG, '', $uri );
				$uri = str_replace ( '&amp;lang=' . STELLA_CURRENT_LANG, '', $uri );
				$uri = str_replace ( '&lang=' . STELLA_CURRENT_LANG , '', $uri );
			}
			
			$uri = str_replace ( '?lang=' . $lang_prefix, '', $uri );
			$uri = str_replace ( '&amp;lang=' . $lang_prefix, '', $uri );
			$uri = str_replace ( '&lang=' . $lang_prefix , '', $uri );

			// Add ?lang if need.
			$lang_tmp = '';
			if ( ! is_permalinks_enabled() && ! $this->use_hosts ) {
				if ( false === strpos( $uri, '?' ) )
					$lang_tmp = '?lang=' . $lang_prefix;
				else
					$lang_tmp = '&amp;lang=' . $lang_prefix;
			}

			if ( is_permalinks_enabled() && ! $this->use_hosts )
				$lang_code = $lang_prefix;
			else
				$lang_code = '';
				
			if ( STELLA_DEFAULT_LANG != $lang_prefix ){
				if( $this->is_subfolder() ){ // if single wordpress is in subfolder
					$subfolder_name = $this->get_subfolder_name();
					$uri = str_replace( $subfolder_name, $subfolder_name . '/' . $lang_code, $uri);
					$href = $host . '/' . $path . '/' . $uri . $lang_tmp;
				}else{
					$href = $host . '/' . $path . '/' . $lang_code  . '/' . $uri . $lang_tmp;
				}
			}else{
				$href = $host . '/' . $path . '/' . $uri;
			}
					
			$href = preg_replace('/\/{2,}/','/', $href);
			
			return apply_filters( 'stella_get_permalink', $href, $lang_prefix );
		}
		function get_lang_menu(){
			$lang_menu = array();
			// Set menu item for default language.
			$href = $this->get_permalink( ( $this->use_hosts ) ? $this->langs['default']['host'] : $_SERVER['HTTP_HOST'], $this->langs['default']['prefix'] );
			$lang_menu[ $this->langs['default']['prefix'] ] = array(
					'title' => apply_filters( 'stella_lang_name', $this->langs['default']['name'] ),
					'href' => $href,
			);

			// Set menu items for additional languages
			foreach ($this->langs['others'] as $prefix => $lang) {

				$href = $this->get_permalink( ( $this->use_hosts ) ? $lang['host'] : $_SERVER['HTTP_HOST'], $lang['prefix'] );

				$lang_menu[ $lang['prefix'] ] = array(
					'title' => apply_filters( 'stella_lang_name', $lang['name'] ),
					'href' => $href,
				);
			}
			return $lang_menu; 
		}
		function is_enabled() {
			return true;
		}
		function is_subfolder(){
			if( is_multisite() ) return false;
			$siteurl = get_option('siteurl');
			$siteurl = str_replace('http://', '', $siteurl);
			$siteurl = str_replace('https://', '', $siteurl);
			if( false == strpos($siteurl, '/') )
				return false;
			return true;
		}
		function get_subfolder_name(){
			$siteurl = get_option('siteurl');
			$siteurl = str_replace('http://', '', $siteurl);
			$siteurl = str_replace('https://', '', $siteurl);
			// cut SERVER_NAME to get subfolder
			return str_replace( '/', '', substr( $siteurl, strlen($_SERVER['SERVER_NAME']), strlen($siteurl) - strlen($_SERVER['SERVER_NAME']) ) );
		}
	}
	add_action('after_setup_theme', create_function('', 'new Stella_Plugin();'));
}