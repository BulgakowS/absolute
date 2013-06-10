<?php

/*
 * Advertisment 
 * Adds links to full version
 * Usage: Free
 */

class Free_Version_Limitations{
	public function __construct() {
		add_filter( 'stella_advertisment_html', array( $this, 'get_full_version_sliders_img_html' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
	}
	function get_full_version_sliders_img_html( $html ){
		$html = '<div id="about-rosetta-full">
			<img src="'.stella_plugin_url() . 'images/about-full-1.png'.'"/>
			<img src="'.stella_plugin_url() . 'images/about-full-2.png'.'"/>
		</div>';
		return $html;
	}
	function add_scripts() {
		if( isset($_GET['page']) && $_GET['page'] == 'stella-options' ){
			wp_enqueue_script( 'stella_cycle', stella_plugin_url() . 'js/jquery.cycle.all.min.js' );
			wp_enqueue_script( 'stella_free_version_limitations', stella_plugin_url() . 'js/free-version-limitations.js', array('stella_options_page_script'), false, false );
			wp_localize_script( 'stella_free_version_limitations', 'limitation_message', __( 'Free version supports only one additional language', 'rosetta-plugin' ) );
		}
	}
}
new Free_Version_Limitations();
?>
