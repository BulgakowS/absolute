<?php
/*
Plugin Name: WP Super Edit Upgrade Utility
Plugin URI: http://funroe.net/projects/super-edit/
Description: Utility for upgrading or cleanning up WP Super Edit options. This will deactivate any active dependent plugins.
Author: Jess Planck
Version: 2.4.6
Author URI: http://funroe.net

Copyright (c) Jess Planck (http://funroe.net)
WP Super Edit is released under the GNU General Public
License: http://www.gnu.org/licenses/gpl.txt

This is a WordPress plugin (http://wordpress.org). WordPress is
free software; you can redistribute it and/or modify it under the
terms of the GNU General Public License as published by the Free
Software Foundation; either version 2 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

For a copy of the GNU General Public License, write to:

Free Software Foundation, Inc.
59 Temple Place, Suite 330
Boston, MA  02111-1307
USA

You can also view a copy of the HTML version of the GNU General
Public License at http://www.gnu.org/copyleft/gpl.html
*/

/**
* WP Super Edit Upgrade Utility init
* Function checks for WP Super Edit before allowing any activation
* @global object $wp_super_edit 
*/
function wp_super_upgrader_init() {
	global $wp_super_edit;
	
	// Deactivate if WP Super Edit is not active & display notice
	if ( empty( $wp_super_edit ) || !is_object( $wp_super_edit ) ) add_action( 'admin_notices', 'wp_super_upgrader_shutdown' );;
}
add_action( 'init', 'wp_super_upgrader_init' );


/**
* WP Super Edit Upgrade Utility Admin Shutdown Notification
*/
function wp_super_upgrader_shutdown() {	
	$current_plugins = get_settings('active_plugins');
	$current_plugin_basename = plugin_basename( __FILE__ );		
	array_splice( $current_plugins, array_search( $current_plugin_basename, $current_plugins ), 1 ); // Array-function!
	update_option( 'active_plugins', $current_plugins );
    
    echo '<div class="settings-error error" id="setting-error-settings_updated"><p><strong>';
    _e( 'WP Super Edit Plugin Required! Activate WP Super Edit before using. Plugin Deactivated.', 'wp-super-edit' );
    echo '</p></div>';
}
// register_activation_hook(__FILE__,'wp_super_edit_upgrader');

function wp_super_edit_upgrader() {
	global $wp_super_edit;

	//  Unregister Stuff for the Super Classes and Super Emotions plugins by default
	$wp_super_edit->unregister_tinymce_plugin( 'supercssclasses');
	$wp_super_edit->unregister_tinymce_button( 'styleselect' );
	$wp_super_edit->unregister_tinymce_plugin( 'superemotions');
	$wp_super_edit->unregister_tinymce_button( 'superemotions' );
	
	// 07-2011: Fix bad URL for Font Tools.
	$wp_super_edit->unregister_tinymce_plugin( 'fonttools');	
	$wp_super_edit->register_tinymce_plugin( array(
		'name' => 'fonttools', 
		'nicename' => __( 'Font Tools', 'wp-super-edit' ), 
		'description' => __( 'Adds the Font Family and Font Size buttons to the editor.', 'wp-super-edit' ), 
		'provider' => 'tinymce', 
		'status' => 'no', 
		'url' => 'none',		
		'callbacks' => ''
	));	
	
	// 07-2011: compat2x -  DEPRECATE no longer functional
	$wp_super_edit->unregister_tinymce_plugin( 'compat2x');

	// 04-2011: wp-super-class - name mistake
	$wp_super_edit->unregister_tinymce_plugin( 'wp-super-class');
	
}
add_action('wp_super_edit_mode_run', 'wp_super_edit_upgrader', 5);


function wp_super_edit_upgrader_shutdown() {	

    echo '<div class="updated settings-error" id="setting-error-settings_updated"><p><strong>';
    _e( 'WP Super Edit Upgrade Completed!', 'wp-super-edit' );
    echo '</p></div>';

	// Deactivate Super Emotions
	deactivate_plugins( WP_PLUGIN_DIR . '/wp-super-edit/wp-se-emotions.php' );

	// Deactivate Super Clasees
	deactivate_plugins( WP_PLUGIN_DIR . '/wp-super-edit/wp-se-cssclasses.php' );

	// Deactivate once completed
	deactivate_plugins( __FILE__ );
}
add_action('admin_notices', 'wp_super_edit_upgrader_shutdown', 5);
