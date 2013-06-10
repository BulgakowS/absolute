<?php
/*
 * Usage: Everywhere
 */
class Force_Secondary_Hosts_Login {
	function __construct() {

		foreach( $_COOKIE as $key => $value ) {
			if ( ! ( FALSE === strstr( $key, 'wordpress_logged_in_' ) ) ) {

				$this->link_extend = "key=$key&amp;value=$value";
				break;
			}
		}
		//add_action( 'wp_logout', array( $this, 'wp_logout' ) );
		add_action( 'stella_parameters', array( $this, 'get_localize_params' ), null, 3 );
		add_action( 'init', array( $this, 'set_user_cookie' ) );
		add_filter( 'stella_get_permalink', array( $this, 'get_permalink' ), 1, 1 );
		//add_filter( 'stella_get_permalink', array( $this, 'get_permalink' ), 1, 1 );

	}

	function get_localize_params ( $langs, $use_hosts, $use_default_lang_values ) {
		$this->use_hosts = $use_hosts;
	}

	function get_permalink( $href ) {
		// Cross-domain only if hosts in use and user has rights.
		if ( $this->use_hosts && current_user_can('manage_options') ) {
			$delimiter = '&amp;';
			if ( FALSE === strstr( $href, '?' ) )
				$delimiter = '?';
			$href = $href . $delimiter . $this->link_extend;
		}
		return $href;
	}

	function set_user_cookie() {
		if ( $this->use_hosts && isset( $_REQUEST['key'] ) ) {
			if ( ! ( FALSE === strstr( $_REQUEST['key'], 'wordpress_logged_in_' ) ) ) {
				setcookie( $_REQUEST['key'], $_REQUEST['value'], time() + 172800, '/', '', is_ssl(), true);

				$url = str_replace( '?key=' . $_REQUEST['key'], '', $_SERVER['REQUEST_URI'] );
				$url = str_replace( '&key=' . $_REQUEST['key'], '', $url );
				$url = str_replace( '?value=' . $_REQUEST['value'], '', $url );
				$url = str_replace( '&value=' . $_REQUEST['value'], '', $url );

				wp_redirect( $url );
				exit();
			}
		}
	}

	/**
	 *  Session management. Think about it.
	 */
	function wp_logout() {
		if ( current_user_can('manage_options') ) {
			foreach( $_COOKIE as $key => $value ) {
				if ( ! ( FALSE === strstr( $key, 'wordpress_logged_in_' ) ) ) {
					
					if( is_multisite() )
						update_blog_option( get_current_blog_id(), 'lang_wp_session_' . $key, $value );
					else 
						update_option( 'lang_wp_session_' . $key, $value );
					break;
				}
			}
		}
	}
}
new Force_Secondary_Hosts_Login();
?>
