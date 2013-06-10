<?php

/*
 * General_Settings_Localizer
 * Manages pseudo metaboxes to store language versions of posts
 * Substitude category site title, tagline, email (?)
 * Usage: Everywhere
 */

class Bloginfo_Localizer {

	private $langs;
	private $empty_field_notices;
	
	function __construct() {
		add_action('stella_parameters', array( $this, 'start' ), 10, 4);
	}

	function start($langs, $use_hosts, $use_default_lang_values, $empty_field_notices) {
		$this->use_default_lang_values = $use_default_lang_values;
		$this->empty_field_notices = $empty_field_notices;
		$this->langs = $langs;
		//changes blog name, description
		if ( !is_admin() ){
			add_filter( 'option_blogname', array( $this, 'localize_blogname' ) );
			add_filter( 'option_blogdescription', array( $this, 'localize_blogdescription' ) );
		}
		// adding styles and scripts
		add_action('admin_init', array($this, 'add_styles'));
		add_action('admin_enqueue_scripts', array($this, 'add_scripts'));

		// add fields to general settings 
		add_filter('admin_init', array( $this, 'add_settings_fields' ));

		//notice messages
		if( $this->empty_field_notices )
			add_action('admin_notices',array($this, 'fields_checker'));
	}
	function fields_checker(){

		// is general settings page 
		if( false != strpos($_SERVER['REQUEST_URI'],"options-general.php") && false == strpos($_SERVER['REQUEST_URI'],"?") ){ 
			// default lang field
			$notices = array();
			$default_notices = array();
			if( is_multisite() ){
				if( '' == get_blog_option( get_current_blog_id(), 'blogname' ) )
					$default_notices[] = __('Site Title');

				if( '' == get_blog_option( get_current_blog_id(), 'blogdescription' ) )
					$default_notices[] = __('Tagline');
			}else{
				if( '' == get_option( 'blogname' ) )
					$default_notices[] = __('Site Title');

				if( '' == get_option( 'blogdescription' ) )
					$default_notices[] = __('Tagline');
			}

			if( 0 != count($default_notices) ) $notices[ $this->langs['default']['name'] ] = $default_notices;
			
			// other languages fields
			foreach($this->langs['others'] as $prefix=>$lang){
				$others_notices = array();
				if( is_multisite() ){
					if( '' == get_blog_option( get_current_blog_id(), 'blogname-'.$lang['prefix'] ) )
						$others_notices[] = __('Site Title');			
					
					if( '' == get_blog_option( get_current_blog_id(), 'blogdescription-'.$lang['prefix'] ) )
						$others_notices[] = __('Tagline');	
				}else{				
					if( '' == get_option( 'blogname-'.$lang['prefix'] ) )
						$others_notices[] = __('Site Title');			
					
					if( '' == get_option( 'blogdescription-'.$lang['prefix'] ) )
						$others_notices[] = __('Tagline');				
				}
				
				if( 0 != count($others_notices) ) $notices[ $lang['name'] ] = $others_notices;
			}
			// generate message
			$notices_string = '';
			if( 0 != count($notices) ){
				foreach( $notices as $lang_name=>$note ){
					$notices_string .= '<p><b>'.$lang_name.'</b>: <i>';
					for( $i = 0; $i < count($note); $i++){
						if( count($note) - 1 == $i) $notices_string .= $note[$i].'</i>';
						else $notices_string .= $note[$i].', ';
					}
					$notices_string .= '</p>';
				}
				echo '<div class="updated"><p>'.__('Some fields are empty:','stella-plugin').'</p>'.$notices_string.'</div>';
			}

		}
	}
	function localize_blogname($value){

		if (STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG){

			$value_new = ( is_multisite() ) ? get_blog_option( get_current_blog_id(), 'blogname-'.STELLA_CURRENT_LANG ) : get_option( 'blogname-'.STELLA_CURRENT_LANG );

			if ('' != $value_new || !$this->use_default_lang_values)
				return $value_new;
		}
		return $value;
	}
	function localize_blogdescription($value){

		if (STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG){

			$value_new =( is_multisite() ) ? get_blog_option( get_current_blog_id(), 'blogdescription-'.STELLA_CURRENT_LANG ) : get_option( 'blogdescription-'.STELLA_CURRENT_LANG );

			if ('' != $value_new || !$this->use_default_lang_values)
				return $value_new;
		}
		return $value;
	}
	function add_settings_fields(){
		foreach( $this->langs['others'] as $prefix => $lang ){
			register_setting('general', 'blogname-'.$lang['prefix'], 'esc_attr');
			register_setting('general', 'blogdescription-'.$lang['prefix'], 'esc_attr');
			add_settings_field('blogname-'.$lang['prefix'], '<label for="blogname-'.$lang['prefix'].'">'.__('Site Title').'</label>' , array($this,'blogname_field_html'), 'general', 'default', array('prefix' => $lang['prefix']));
			add_settings_field('blogdescription-'.$lang['prefix'], '<label for="blogdescription-'.$lang['prefix'].'">'.__('Tagline').'</label>' , array($this,'blogdescription_field_html'), 'general', 'default', array('prefix' => $lang['prefix']));
		}
	}
	function blogname_field_html($args){

		$prefix = $args['prefix'];
		$value = ( is_multisite() ) ? get_blog_option( get_current_blog_id(), 'blogname-'.$prefix ) : get_option( 'blogname-'.$prefix );
		echo '<input name="blogname-'.$prefix.'" type="text" id="blogname-'.$prefix.'" value="' . $value . '" class="regular-text"/>';
	}
	function blogdescription_field_html($args){

		$prefix = $args['prefix'];
		$value = ( is_multisite() ) ? get_blog_option( get_current_blog_id(), 'blogdescription-'.$prefix ) : get_option( 'blogdescription-'.$prefix );
		echo '<input name="blogdescription-'.$prefix.'" type="text" id="blogdescription-'.$prefix.'" value="' . $value . '" class="regular-text"/>';
		echo '<span class="description"> '.__('In a few words, explain what this site is about.').'</span>';
	}
	function get_indexed_language_list() {

		$result['0'] = array('0' => $this->langs['default']['prefix'], '1' => $this->langs['default']['name']);
		$i = 1;
		foreach ($this->langs['others'] as $prefix => $lang) {
			$result[$i] = array('0' => $lang['prefix'], '1' => $lang['name']);
			$i++;
		}
		return $result;
	}
	function add_styles() {
		wp_enqueue_style('stella_styles', stella_plugin_url() . 'css/tabs.css');
	}

	function add_scripts() {
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('stella_bloginfo_tabs', stella_plugin_url() . 'js/bloginfo-tabs.js');
		
		( false == strpos($_SERVER['REQUEST_URI'],"site-settings.php") ) ? $mu_settings_page = false : $mu_settings_page = true;
		
		wp_localize_script('stella_bloginfo_tabs', 'bloginfo_langs', json_encode(array('langs' => $this->get_indexed_language_list(), 'default_str' => __('Default','stella-plugin'), 'mu_settings_page' => $mu_settings_page)));
	}

}

new Bloginfo_Localizer();

?>
