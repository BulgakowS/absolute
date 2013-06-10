<?php
/**
 * Create localized menu labels. Their content obtaining happens in Post_Localizer.
 * Usage: Everywhere
 */
class Theme_Menus_Localizer {
	private $langs;
	private $use_default_lang_values;

	function __construct() {
		add_action('stella_parameters', array($this, 'start'), 10, 3);
	}

	function start( $langs, $use_hosts, $use_default_lang_values ) {
		$this->use_default_lang_values = $use_default_lang_values;
		$this->langs = $langs;

		// Save menu item.
		add_action( 'wp_update_nav_menu_item', array( $this, 'update_nav_menu_item' ), 10, 3 );

		// adding styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );

	}


	function admin_enqueue( $hook ) {
		global $current_user;

		if( 'nav-menus.php' != $hook )
			return;

		$nav_menu_selected_id = isset( $_REQUEST['menu'] ) ? (int) $_REQUEST['menu'] : 0;
		$menu = wp_get_nav_menu_object( $nav_menu_selected_id );

		if ( ! is_nav_menu( $menu ) ) {
			$nav_menu_selected_id = get_user_meta( $current_user->ID, 'nav_menu_recently_edited', true );
			$menu = wp_get_nav_menu_object( $nav_menu_selected_id );
		}
		
		$titles = array();
		
		if ( is_nav_menu( $menu ) ) {
			$menu_items = wp_get_nav_menu_items( $menu->term_id, array('post_status' => 'any') );
			
			foreach( $menu_items as $item ) {
				foreach ( $this->langs['others'] as $prefix => $lang ) {
					$title_new = get_post_meta( $item->ID, '_title-' . $lang['prefix'], true );

					if ('' == $title_new && $this->use_default_lang_values)
						$titles[$item->ID][$lang['prefix']] = $item->post_title;
					else
						$titles[$item->ID][$lang['prefix']] = $title_new;

				}
			}

		}


		wp_enqueue_script( 'menu-localizer', stella_plugin_url() . 'js/menu-localizer.js' );
		// Get 'Navigation Label' translate from Wordpress.
		wp_localize_script( 'menu-localizer', 'menu_strings', array('navigation_label' => __('Navigation Label'), 'titles' => $titles  ) );
	}

	function update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0, $menu_item_data = array() ) {
		$item_meta = array();

		// only if ajax
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			// This script should be called on frontend when new item is added to add language fields.
			echo '<script type="text/javascript">jQuery("#edit-menu-item-title-"+'.$menu_item_db_id.').add_language_fields();</script>';
		}
		
		if( isset( $_POST['stella-menu-item-title'] ) ) {
			foreach ( $this->langs['others'] as $prefix => $lang ) {
				$item_meta['_title-' . $lang['prefix']] = $_POST['stella-menu-item-title'][$lang['prefix']][$menu_item_db_id];
			}

			foreach ( $item_meta as $key => $value ) {

				if ( get_post_meta( $menu_item_db_id, $key, FALSE ) ) {
					update_post_meta( $menu_item_db_id, $key, $value );
				} else {
					add_post_meta( $menu_item_db_id, $key, $value );
				}
				if ( ! $value )
					delete_post_meta( $menu_item_db_id, $key );
			}
		}
	}
}
new Theme_Menus_Localizer();
?>
