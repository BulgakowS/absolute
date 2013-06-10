<?php

/*
 * Usage: Everywhere
 */

if ( ! function_exists( 'stella_is_permalinks_enabled' ) ) {
	function stella_is_permalinks_enabled() {
		$permalink_page = parse_url( admin_url('/') . 'options-permalink.php' );
		if ( isset( $_POST['_wp_http_referer'] ) &&  $permalink_page['path'] == $_POST['_wp_http_referer'] && isset( $_POST['permalink_structure'] ) ) {
			if ( '' !=  $_POST['permalink_structure'] )
				return true;
			else
				return false;
		}

		if( is_multisite() )
			return '' != get_blog_option( get_current_blog_id(), 'permalink_structure' );
		else
			return '' !=  get_option( 'permalink_structure' );
	}
}

// Correct plugin path for symlinks case
if ( ! function_exists( 'stella_plugin_url') ) {
	function stella_plugin_url() {
		return trailingslashit( plugins_url( basename( dirname( __FILE__ ) ) ) );
	}
}
	
if ( ! function_exists( 'stella_file_exists' ) ) {
	function stella_file_exists( $path ) {
		return file_exists( dirname(__FILE__) . '/' . $path );
	}
}

if ( ! function_exists( 'is_permalinks_enabled' ) ) {
	function is_permalinks_enabled() {
		return stella_is_permalinks_enabled();
	}
}

if ( ! function_exists( 'stella_get_lang_list' ) ) {
	function stella_get_lang_list() {
		return apply_filters( 'stella_get_lang_list', array() );
	}
}

if ( ! function_exists( 'stella_get_current_lang' ) ) {
	function stella_get_current_lang() {
		return STELLA_CURRENT_LANG;
	}
}

if ( ! function_exists( 'stella_get_default_lang' ) ) {
	function stella_get_default_lang() {
		return STELLA_DEFAULT_LANG;
	}
}

if ( ! function_exists( 'stella_translate_custom_field') ) {
	function stella_translate_custom_field( $id, $field_name, $title, $post_type, $context ) {
		do_action('stella_translate_custom_field', $id, $field_name, $title, $post_type, $context);
	}
}

if ( ! function_exists( 'stella_translate_custom_thumbnail') ) {
	function stella_translate_custom_thumbnail( $label, $id, $post_type ) {
		do_action( 'stella_translate_custom_thumbnail', $label, $id, $post_type );
	}
}

if ( ! function_exists('stella_translate_string') ){
	function stella_translate_string( $filter_name, $original_string, $translations_array ){
		do_action( 'stella_translate_string', $filter_name, $original_string, $translations_array );
	}
}

?>
