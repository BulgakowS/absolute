<?php

/*
 * Options_Page class
 * Manages options page 
 * Usage: Everywhere
 */

class Stella_Options_Page {
	private $langs;
	// $lang_codes: value, lang_prefix, lang_name
	private $lang_codes = array();
	private $use_hosts;
	private $empty_field_notices;
	function __construct() {
		add_action( 'stella_parameters', array( $this, 'start' ), 10, 4 );
	}
	function add_styles() {
		wp_enqueue_style( 'stella_options_page_style', stella_plugin_url() . 'css/options-page.css' );
	}
	function add_scripts() {
		wp_enqueue_script( 'jquery-color' );
		wp_enqueue_script( 'stella_options_page_script', stella_plugin_url() . 'js/options-page.js' );
		wp_localize_script( 'stella_options_page_script', 'options_page_vars', $this->get_js_vars() );
	}
	function load_lang_codes(){
		if( file_exists( dirname(__FILE__).'/../lang-codes-list.txt') ){
			$lang_codes = file( dirname(__FILE__).'/../lang-codes-list.txt' );
		}
		// explode lines to lang prefix(code) and lang name
		$exploded_lang_codes = array();
		foreach( $lang_codes as $key => $line ){
			$lang = $this->explode_lang_selection( str_replace( '/\n', '', $line ) );
			if( false != $lang )
				$exploded_lang_codes[ strtolower( $lang['prefix'] ) ] = trim($lang['name']);
		}
		return apply_filters( 'stella-lang-codes', $exploded_lang_codes );
	}
	function explode_lang_selection( $s ){
		$exploded = explode( '/', $s );
		if( 2 == count( $exploded ) ){
			return array( 'name'=>trim( $exploded['0'] ), 'prefix'=>trim( $exploded['1'] ) );
		}
		return false; 
	}	
	function lang_selection_html( $selected_prefix, $tag_name ){
		$html = "<select name='$tag_name'>";
		foreach( $this->lang_codes as $prefix => $name ){		
			if( $prefix == $selected_prefix ){
				$prefix = strtoupper( $prefix );
				$html.="<option selected>$name / $prefix </option>";
			}else{
				$prefix = strtoupper( $prefix );
				$html.="<option >$name / $prefix </option>";
			}
		}
		$html.='</select>';
		return $html;
	}
	function start( $langs, $use_hosts, $use_default_values, $empty_field_notices ) {
		$this->langs = $langs;
		$this->use_hosts = $use_hosts;
		$this->empty_field_notices = $empty_field_notices;
		$this->lang_codes = $this->load_lang_codes();
		// option page
		add_action( 'admin_menu', array( $this, 'lang_options_menu' ) );
		// adding styles and scripts
		add_action( 'admin_init', array( $this, 'add_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
	}
	function get_js_vars() {
		$vars = array(
			'btn_delete' => __( 'Delete', 'stella-plugin' ),
			'confirm_msg' => __( 'Are you sure? Check out hosts again, before pressing OK!','stella-plugin' ),
			'host_placeholder' => __( 'Please fill hostname', 'stella-plugin' ),
		);
		return $vars;
	}
	function lang_options_menu() {
		add_options_page( __( 'Stella languages','stella-plugin' ), __( 'Stella languages', 'stella-plugin' ), 'manage_options', 'stella-options', array( $this, 'options_page' ) );
	}
	function switcher_html( $option_name, $checked ){
		$checked_value = ( $checked ) ? 'checked="checked"' : '';
		$html = "<label class='switcher-wrapper' for='$option_name'>
					<div class='slider'></div> 
					<input type='checkbox' class='switchbox' id='$option_name' name='$option_name' $checked_value/> 
					<div class='switcher'>
						<div class='rail'>
							<div class='state1'>OFF</div>
							<div class='state2'>ON</div>
						</div>
					</div>
				</label>";
		return $html;
	}
	function options_page_html(){
		// load options from db 
		$options = ( is_multisite() ) ? get_blog_option( get_current_blog_id(), 'stella-options' ) : get_option( 'stella-options' );
		
		$default_langs_array = array(
				'default' => array('prefix' => 'en', 'name' => 'English', 'host' => $_SERVER['HTTP_HOST']),
				'others' => array(),
			);
		$langs = ( isset( $options['langs'] ) ) ? $options['langs'] : $default_langs_array;
		$use_hosts = ( isset( $options['use_hosts'] ) ) ? $options['use_hosts'] : false;
		$use_default_lang_values = ( isset( $options['use_default_lang_values'] ) ) ? $options['use_default_lang_values'] : false;
		$empty_field_notices = ( isset( $options['empty_field_notices'] ) ) ? $options['empty_field_notices'] : true;
		
		// checking to set default values
		if ( $langs['default']['name'] == '' )
			$langs['default']['name'] = 'English';
		if ( $langs['default']['prefix'] == '' )
			$langs['default']['prefix'] = 'EN';
		if ( $langs['default']['host'] == '' )
			$langs['default']['host'] = $_SERVER['HTTP_HOST'];
		
		// page text
		$p_title = __( 'Stella plugin settings','stella-plugin' );
		$p_langs_section_name = __( 'Languages','stella-plugin' );
		$p_mode_section_name = __( 'Mode','stella-plugin' );
		$p_default = __( 'Default','stella-plugin' );
		$p_additional = __( 'Additional','stella-plugin' );
		$p_help = __( 'LANGUAGE NAME / PREFIX','stella-plugin' );
		$p_host_help = __( 'HOST', 'stella-plugin' );
		$p_add_new = __( 'Add new','stella-plugin' );
		$p_delete = __( 'Delete','stella-plugin' );
		$p_use_host = __( 'Use hosts','stella-plugin' );
		$p_use_default_lang_values = __( 'Transfer default language content','stella-plugin' );
		$p_use_default_lang_values_help = __( 'With this option on default language content will be transferred to empty local content','stella-plugin' );
		$p_display_empty_field_notices = __( 'Display empty field notices','stella-plugin' );
		$p_update = __('Save Changes');
		$host_placeholder = __( 'Please fill hostname', 'stella-plugin' );
		
		// generate default_lang_html
		$is_hidden = ( $use_hosts ) ? "" : " hidden";
		$default_lang_html = '<div class="lang-wrapper"><div class="select">' . $this->lang_selection_html( $langs['default']['prefix'], 'default-lang' ) . '</div><input type="text" class="host-options'.$is_hidden.'" name="default-host" value="' . $langs['default']['host'] . '" placeholder="' . $host_placeholder . '"></div>';

		// generate lang_list_html
		$lang_list_html = '';
		if ( isset( $langs['others'] ) ) {
			foreach ( $langs['others'] as $prefix => $lang ) {
				$lang_list_html.= '<div class="lang-wrapper"><div class="select">' . $this->lang_selection_html(  $lang['prefix'],'lang[]' ) . '</div><input class="host-options'.$is_hidden.'" type="text" name="host[]" value="' . $lang['host'] . '" placeholder="' . $host_placeholder . '"/><input type="button" class="square-button del-button" name="del-host" value="'.$p_delete.'"/></div>';
			}
		}
		// advertisment html 
		$advertisment = apply_filters( 'stella_advertisment_html', '' );
		
		$use_hosts_html = $this->switcher_html( "use-hosts", $use_hosts );
		$use_default_lang_values_html = $this->switcher_html( "use-default-lang-values", $use_default_lang_values );
		$empty_field_notices_html = $this->switcher_html( "empty-field-notices", $empty_field_notices );
		
		if ( is_multisite() ) {

			if ( ! defined( 'SUNRISE' ) ) {
				$use_hosts_html = '<h4 class="message">' . __( "If you are planing to use hosts with multisite, please put <code>define('SUNRISE', true);</code> in your wp-config.php and copy sunrise.php from plugin dir to your wp-content folder.", 'stella-plugin' ) . '</h4>';

			}

			if ( defined( 'SUNRISE' ) && ! file_exists( WP_CONTENT_DIR . '/sunrise.php' ) ) {
				$use_hosts_html = '<h4 class="message">' . __( "If you are planing to use hosts with multisite, please copy sunrise.php from plugin dir to your wp-content folder.", 'stella-plugin') . '</h4>';
			}

		}

		if ( is_multisite() && ! is_subdomain_install() ) {
			$use_hosts_html = '<h4 class="message">'.__('Hosts with multisite subfolders are not supported','stella-plugin').'</h4>';
		}

		$wp_referer_field = wp_referer_field();
		// safely add icons to options page
		$icon_html = '<div id="icon-options-general" class="icon32"><br></div>';
		$plugin_url = stella_plugin_url();
		if( file_exists( dirname(__FILE__).'/../images/icon-stella.png' ) ){
			$icon_html = "<div><img src='$plugin_url/images/icon-stella.png' class='icon32'></img></div>";
		}else{
			if( file_exists( dirname(__FILE__).'/../images/icon-stella-free.png' ) ){
				$icon_html = "<div><img src='$plugin_url/images/icon-stella-free.png' class='icon32'></img></div>";
			}
		}
		
		// generate form html
		$options_html = <<<options_html
		<div id="language-options" class="wrap">
			$icon_html
			<h2>$p_title</h2>

			<form method="post" name="options" target="_self">
			$wp_referer_field
			<h3>$p_langs_section_name</h3>

			<table width="100%" cellpadding="10" class="form-table">				
				<tr valign="top" scope="row">
					<td align="left"><h4>$p_default</h4></td>
				</tr>
				<tr>
					<td align="left" scope="row">
						$default_lang_html
					</td>
				</tr>
				<tr valign="top">
					<td align="left" scope="row"><h4>$p_additional</h4></td>
				</tr>
				<tr>
					<td align="left" scope="row">
						<input id="add-host" type="button" class="button" name="add-host" value="$p_add_new" />
						$lang_list_html
					</td>
				</tr>
			</table>
			
			<h3>$p_mode_section_name</h3>
			
			<table width="100%" cellpadding="10" class="form-table">				
				<tr valign="top">
					<td align="left" scope="row" class="switcher-section">
						<h4>$p_use_host</h4>
					</td>
					<td>
						$use_hosts_html
					</td>
				</tr>				
				<tr valign="top">
					<td align="left" scope="row" class="switcher-section">
						<h4>$p_display_empty_field_notices</h4>							
					</td>
					<td>
						$empty_field_notices_html
					</td>
				</tr>
				<tr valign="top">
					<td align="left" scope="row" class="switcher-section">
						<h4>$p_use_default_lang_values</h4>							
					</td>
					<td>
						$use_default_lang_values_html<span class="description">$p_use_default_lang_values_help</span>
					</td>
				</tr>
			</table>
			$advertisment
			<p class="submit">
				<input id="submit-options" class="button-primary" type="submit" name="Submit" value="$p_update"/>
			</p>
			</form>
		</div>
options_html;
		return $options_html;
	}
	function message_html( $message, $class ){
		return "<div class='$class'><p><strong>$message</p></div>";
	}
	function update_stella_options(){
		$notices_html = '';
		// save new values if form was submitted
		if ( isset( $_POST['Submit'] ) ) {
			$new_options = array();
			$not_valid_hosts = array();
			$used_hosts = array();
			
			$double_lang = false; 
			$double_host = false; 
			
			// use_hosts setting
			if( isset( $_POST['use-hosts']) && 'on' == $_POST['use-hosts'] )
				$new_options['use_hosts'] = true;
			else
				$new_options['use_hosts'] = false;
			
			// use default lang values
			if( isset( $_POST['use-default-lang-values'] ) && 'on' == $_POST['use-default-lang-values'] )
				$new_options['use_default_lang_values'] = true;
			else
				$new_options['use_default_lang_values'] = false;
			
			// display empty gield notices
			if( isset( $_POST['empty-field-notices'] ) && 'on' == $_POST['empty-field-notices'] )
				$new_options['empty_field_notices'] = true;
			else
				$new_options['empty_field_notices'] = false;
			
			
			// getting default_lang
			$new_default_host = $this->trim_h( $_POST['default-host'] );
			$default_lang = $this->explode_lang_selection( $_POST['default-lang'] ) ;
			$new_options['langs']['default']['prefix'] = strtolower( $default_lang['prefix'] );
			$new_options['langs']['default']['name'] = $default_lang['name'];
			// don't save host if it is not valid
			if( $this->validate_host( $new_default_host ) ){		
				$new_options['langs']['default']['host'] = $new_default_host;	
				
				$used_hosts[$new_default_host] = true;
			}else{
				$not_valid_hosts[] = '<b>' . $default_lang['name'] . ': </b><i>' . $new_default_host . '</i>';
				$new_options['langs']['default']['host'] = $_SERVER['HTTP_HOST'];
				
				$used_hosts[$_SERVER['HTTP_HOST']] = true;
			}
			// getting lang_list
			$new_options['langs']['others'] = array();
			if ( isset( $_POST['lang'] ) && isset( $_POST['host'] ) ) {
				$len = count( $_POST['lang'] );
				for ( $i = 0; $i < $len; $i++ ) {
					$new_host = $this->trim_h( $_POST['host'][$i] );	
					$lang = $this->explode_lang_selection( $_POST['lang'][$i] );
					if( $_POST['lang'][$i] != $_POST['default-lang'] && !isset( $new_options['langs']['others'][strtolower( $lang['prefix'] )]['prefix'] ) ){
						if( $new_host != $new_default_host && !isset( $used_hosts[$new_host] ) || $new_host == '' ){
							$new_options['langs']['others'][strtolower( $lang['prefix'] )]['prefix'] = strtolower( $lang['prefix'] );
							$new_options['langs']['others'][strtolower( $lang['prefix'] )]['name'] = $lang['name'];
							if( $this->validate_host( $new_host ) ){
								$new_options['langs']['others'][strtolower( $lang['prefix'] )]['host'] = $new_host;
								$used_hosts[$new_host] = true;
							}else{
								$not_valid_hosts[] = '<b>' . $lang['name'] . ': </b><i>' . $new_host . '</i>';
								$new_options['langs']['others'][strtolower( $lang['prefix'] )]['host'] = '';
							}
						}else{
							$double_host = true;
						}
					}else{
						$double_lang = true;
					}
				}
			}
			
			// update options 
			if( is_multisite() )
				update_blog_option( get_current_blog_id(), 'stella-options', $new_options );
			else 
				update_option( 'stella-options', $new_options );
			
			// genarate notice messages
			$notices_html = $this->message_html( __( 'Options saved.'), 'updated' );
			if( count( $not_valid_hosts ) != 0 ){
				$notices_html .= '<div class="error"><p>'.__( 'Some hosts not valid:', 'stella-plugin' ).'</p>';
				foreach( $not_valid_hosts as $k=>$v ){
					$notices_html .= '<p>'.$v.'</p>';
				}
				$notices_html .= '</div>';
			}
			if( $double_lang ) $notices_html .= $this->message_html( __('Multiple use of language', 'stella-plugin'), 'error' );
			if( $double_host ) $notices_html .= $this->message_html( __('Multiple use of host', 'stella-plugin'), 'error' );
		}
		return $notices_html;
	}
	function options_page() {
		// checking permissions 
		if ( !current_user_can( 'manage_options' ) ) {
			echo $this->error_message_html( __( 'You do not have sufficient permissions to access this page.' ), 'error' );
		}else{
			echo $this->update_stella_options();
			echo $this->options_page_html();
		}	
	}
	function trim_h( $host ) {
		$host = str_replace( 'http://', '', $host);
		$host = str_replace('https://', '', $host);
		return trim( $host );
	}
	function validate_host( $host ) {
		// first and last char can't be '.' or '-'
		if( substr( $host, -1 ) == '-' || substr( $host, 0, 1 ) == '-' || substr( $host, -1 ) == '.' || substr( $host, 0, 1 ) == '.' )
			return false;
		// valid chars
		$valids = '0123456789'.implode( '',range('a','z' ) ).'-./:';
		if ( strspn( $host, $valids ) != strlen( $host ) ) {
			return false;
		}
		return true;
	}
}

new Stella_Options_Page();

?>
