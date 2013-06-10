<?php
/*
 * Url_Changer class
 * Changes homeurl,siteurl
 * Registrates constants - STELLA_CURRENT_LANG, STELLA_DEFAULT_LANG
 * Usage: Everywhere
 */

class Url_Changer {

	private $old_url;
	private $use_hosts;
	private $langs;

	function __construct() {
		//print_r($GLOBALS);die;
		$this->rewrite_url(); // only for subfolder situation
		
		if ( ! is_admin() )
			add_action( 'stella_parameters', array( $this, 'start' ), 1, 2 );	
	}

	function start( $langs, $use_hosts ) {
		
		$this->langs = $langs;
		$this->use_hosts = $use_hosts;
	
		if ( is_multisite() && defined( 'SUNRISE' ) && defined( 'STELLA_MULTISITE_DOMAIN' )  )
			$_SERVER['HTTP_HOST'] = STELLA_MULTISITE_DOMAIN;

		$this->look_up_language();
		
		// Fix host name
		if ( $this->use_hosts ) {
			add_filter( 'option_siteurl', array( $this, 'change_siteurl' ) );
			add_filter( 'option_home', array( $this, 'change_siteurl' ) );
			add_filter( 'content_url', array( $this, 'change_contenturl' ) );
			return;
		}

		// Fix links for /lang/ prefix
		if ( ! is_admin() && is_permalinks_enabled() ) {
			
			if( ! $this->is_subfolder() ){
				add_filter( 'option_home', array( $this, 'add_language_prefix_to_url' ) );
				add_filter( 'content_url', array( $this, 'add_language_prefix_to_url' ) );
			}else{
				add_action('parse_query', array( $this, 'add_language_prefix_after_parse_query') );
				add_filter( 'content_url', array( $this, 'add_language_prefix_to_url' ) );
			}
            return;
		}
	

		// Fix content links for ?lang
		if ( ! is_admin() && ! is_permalinks_enabled() ) {
			add_action('parse_query', array( $this, 'add_language_postfix_after_parse_query') );
			// if theme don't have custom searchform, filter standart form
			add_filter( 'get_search_form', array( $this, 'localize_searchfrom') );
			// trying to filter custom searchform
			add_action( 'wp_enqueue_scripts', array($this, 'add_scripts') );

			return;
		}
	}
	function add_language_prefix_after_parse_query( $args ){
		add_filter( 'home_url', array( $this, 'add_language_prefix_to_url' ));
	}
	function add_language_postfix_after_parse_query( $args ){
		add_filter( 'home_url', array( $this, 'add_language_postfix_to_url' ));
	}
	/*
	 * filter standart searchform
	 */
	function localize_searchfrom( $searchform ){

		if ( STELLA_DEFAULT_LANG != STELLA_CURRENT_LANG ) {
			if( ! $this->use_hosts && ! is_permalinks_enabled() ) {
				$searchform = str_replace('</form>', '<input type="hidden" name="lang" value="'.STELLA_CURRENT_LANG.'"/></form>', $searchform);
			}
		}
		
		return $searchform;
	}
	/*
	 * trying to filter custom searchform
	 */
	function add_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('stella_searchform_filter', stella_plugin_url() . 'js/filter-searchform.js');
		wp_localize_script('stella_searchform_filter', 'hidden_lang_input', '<input type="hidden" name="lang" value="'.STELLA_CURRENT_LANG.'"/>');
	}
	function look_up_language() {


		// Language recognition (STELLA_CURRENT_LANG) for multisite should be in sunrise.php
		if( is_multisite() )
			return;
		
		$use_lang = $this->langs['default']['prefix'];
		
		// Language recognition in rewrite_url function. only for subfolder situation
		if( $this->is_subfolder() && is_permalinks_enabled() ){
			if( ! defined( 'STELLA_CURRENT_LANG' ) ) define('STELLA_CURRENT_LANG', $use_lang);
			return;
		}

		if ( $this->use_hosts  ):
			foreach ( $this->langs['others'] as $prefix => $lang ) {
				if ( $_SERVER['HTTP_HOST'] == $lang['host'] || $_SERVER['HTTP_HOST'] == 'www.' . $lang['host'] ) {
					$use_lang = $lang['prefix'];
					break;
				}
			}
		elseif ( ! is_permalinks_enabled() ) :
			// Non-permalinks variant.
			if ( isset($_GET['lang']) ) :
				foreach ( $this->langs['others'] as $prefix => $lang ) {
					if ( $_GET['lang'] == $lang['prefix'] ) {	
						$use_lang = $lang['prefix'];
						
						break;
					}
				}
			endif;
		else: // Permalinks no-hosts variant.
			if( ! $this->is_subfolder() ){ //wp is not in subfolder
				$uri = $_SERVER['REQUEST_URI'];
				$code = substr( $uri, 0, 4 );
				if ( '/' != substr( $code, -1 ) )
					$code = $code . '/';

				foreach ( $this->langs['others'] as $prefix => $lang ) {
					if ( ! ( false === strpos( $code, '/' . $lang['prefix'] . '/' ) ) ) {
						$use_lang = $lang['prefix'];
						break;
					}
				}
			}

		endif;

		if( ! defined( 'STELLA_CURRENT_LANG' ) ) define('STELLA_CURRENT_LANG', $use_lang);
	}
	
	/**
	 * check is wordpress in subfolder
	 */
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
	/**
	 * Filter for content_url
	 * NO HOSTS
	 * NO PERMALINKS
	 */
	function add_language_postfix_to_url( $value ) {

		$content = strpos($value, '/wp-content/');
		$admin = strpos($value, '/wp-includes/');

		if ( false == $content && false === $admin && STELLA_DEFAULT_LANG != STELLA_CURRENT_LANG) {

			if( ! $this->use_hosts && ! is_permalinks_enabled() ) {

				// If lang is in request uri ( like ?lang ), remove it
				$value = str_replace ( '?lang=' . STELLA_CURRENT_LANG, '', $value );
				$value = str_replace ( '&amp;lang=' . STELLA_CURRENT_LANG, '', $value );
				$value = str_replace ( '&lang=' . STELLA_CURRENT_LANG , '', $value );

				// Add ?lang if need.
				$lang_tmp = '';
					if ( false === strpos( $value, '?' ) )
						$lang_tmp = '?lang=' . STELLA_CURRENT_LANG;
					else
						$lang_tmp = '&amp;lang=' . STELLA_CURRENT_LANG;

				$value .= $lang_tmp;
			}
		}
		return $value;
	}

	/**
	 * Filter for option_home, content_url
	 * NO HOSTS
	 * PERMALINKS
	 * TODO ensure it works perfect
	 */
	function add_language_prefix_to_url( $value ) {
		
		if( defined('STELLA_DEFAULT_LANG') && defined('STELLA_CURRENT_LANG') ){
			$pos = strpos($value, '/wp-content/');
			$admin = strpos($value, '/wp-includes/');

			if ( $pos === false && $admin === false && STELLA_DEFAULT_LANG != STELLA_CURRENT_LANG ) {

				if( is_multisite() && ! is_subdomain_install() ) {

					$site = stella_get_current_blog();
					if ( $site ) {
						$from =  preg_replace('/\/{2,}/','/',  trim( $_SERVER['HTTP_HOST'] . $site->path, '/' ) );
						$to = preg_replace('/\/{2,}/','/',  $_SERVER['HTTP_HOST'] . $site->path . '/' . STELLA_CURRENT_LANG );
						$value = str_replace( $from, $to, $value);
					}

				} else  {
					$siteurl = get_option('siteurl');
					$value = str_replace( $siteurl, $siteurl . '/' . STELLA_CURRENT_LANG , $value);
		
				}
			}
		}
		return $value;
	}

	/**
	 * When use hosts change the siteurl, permalinks or not.
	 */
	function change_siteurl( $value ) {

		if ($this->langs['default']['prefix'] != STELLA_CURRENT_LANG){
			$new_url = $this->langs['others'][STELLA_CURRENT_LANG]['host'];
		}	
		else{
			if( $this->use_hosts ) $new_url = $this->langs['default']['host'];
			else return $value;
		}

		$this->old_url = $value;

		if ( is_ssl() )
			$new_url = 'https://' . $new_url;
		else
			$new_url = 'http://' . $new_url;
		
		return $new_url;
	}

	/**
	 * When use hosts change the contenturl (posts, pages urls), permalinks or not.
	 */
	function change_contenturl( $value ) {

		if ($this->langs['default']['prefix'] != STELLA_CURRENT_LANG)
			$new_url = $this->langs['others'][STELLA_CURRENT_LANG]['host'];
		else
			$new_url = $this->langs['default']['host'];

		$value = str_replace($this->old_url, '', $value);
		$value = str_replace('http://', '', $value);
		$value = str_replace('https://', '', $value);

		if (is_ssl())
			$value = 'https://' . $new_url . $value;
		else
			$value = 'http://' . $new_url . $value;
		return $value;
	}
	/**
	 * Rewrite input url for wordpress use. Rosetta already gathered all information about language,
	 * so we need to remove prefix from url for correct wordpress work.
	 * This method called only when permalinks enabled and not hosts, so no further checks.
	 * NO_HOSTS
	 * PERMALINKS
	 */
	private function rewrite_url() {
		
		if ( is_multisite() || ! $this->is_subfolder() || ! is_permalinks_enabled() )
			return;
		
		$ending_slash = false;
		if( '/' == substr($_SERVER['REQUEST_URI'], -1) )
			$ending_slash = true;
		
		$uri = ( $ending_slash ) ? $_SERVER['REQUEST_URI'] . '/' : $_SERVER['REQUEST_URI'];

		$subfolder_name =  $this->get_subfolder_name();
		$code_start = strrpos( $uri, $subfolder_name ) + strlen( $subfolder_name );

		$code = substr( $uri, $code_start, 4 );
		$code = trim( str_replace('/', '', $code) );

		$options = get_option('stella-options');
		
		foreach ( $options['langs']['others'] as $prefix => $lang ) {
			if ( ! ( false === strpos( $code, $lang['prefix'] ) ) ) {
				$uri = str_replace( $code.'/', '', $uri);
				define('STELLA_CURRENT_LANG', $code);
				break;
			}
		}
		
		// Recreate REQUEST_URI
		$_SERVER['REQUEST_URI'] =  preg_replace('/\/{2,}/','/', $uri );

		if ('' == $_SERVER['REQUEST_URI']) :
			$_SERVER['REQUEST_URI'] = '/';
		endif;
		
		$_SERVER['REDIRECT_URL'] = $_SERVER['REQUEST_URI'];

	}
}
new Url_Changer();
?>
