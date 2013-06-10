<?php
/*
Plugin Name: Media Manager Plus
Plugin URI: http://dev7studios.com/uber-media
Description: Upgrade the WordPress Media Manager and add support for Flickr, Instagram, 500px etc.
Version: 1.1
Author: Dev7studios
Author URI: http://dev7studios.com
*/

if ( !session_id() ) session_start();

$uber_media = new uber_media();
class uber_media {

	private $p1ugin_folder;
	private $plugin_path;
	private $plugin_version;
	private $plugin_l10n;
	
	private $callback;
	
	function __construct() {	
       	$this->plugin_folder = basename(plugin_dir_path(__FILE__));
       	$this->plugin_path = plugin_dir_path( __FILE__ );
       	$this->plugin_version = '1.0';
       	$this->plugin_l10n = 'uber-media';
       	$this->callback = get_admin_url() .'upload.php?page=uber-media';
       	
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_menu', array($this, 'uber_media_page' ));

        add_action('admin_init', array($this, 'image_sources_header'));
         
        add_action('print_media_templates', array($this, 'print_media_templates'));
        add_filter('media_view_strings', array($this,'custom_media_string'), 10, 2);
        
        add_action('wp_ajax_uber_disconnect', array($this, 'disconnect_source'));
        add_action('wp_ajax_uber_check', array($this, 'connect_check'));
        add_action('wp_ajax_uber_load_images', array(&$this, 'load_images'));
                       
        load_plugin_textdomain( $this->plugin_l10n, false, dirname( plugin_basename( __FILE__ ) ) .'/lang/' );
        
		require_once( $this->plugin_path .'includes/wp-settings-framework.php' );
		$this->wpsf = new ubermediaWordPressSettingsFramework( $this->plugin_path .'includes/uber-media-settings.php', '');
		add_filter( $this->wpsf->get_option_group() .'_settings_validate', array( $this, 'validate_settings' ) );
		$this->settings = wpsf_get_settings( $this->plugin_path .'includes/uber-media-settings.php' );
        
	}
    
    function admin_enqueue_scripts() {
		wp_register_script( 'uber-media-js', plugins_url('assets/js/uber-media.js' , __FILE__ ), array('media-views'), $this->plugin_version );
        wp_enqueue_script( 'uber-media-js' );
        wp_localize_script( 'uber-media-js', 'uber_media', array(  'nonce' => wp_create_nonce('uber_media') ));
        
        wp_register_style( 'uber-media-css', plugins_url('assets/css/uber-media.css' , __FILE__ ), array(), $this->plugin_version);
		wp_enqueue_style('uber-media-css');
    }
    
    function uber_media_page() {
		add_media_page('Media Manager Plus', 'Media Manager Plus', 'read', 'uber-media', array($this, 'settings_page' ));
	}
	
	public function settings_page() {
		if (!current_user_can('manage_options')) {
		    wp_die('You do not have sufficient permissions to access this page.');
		} 
		global $wpsf_ubermedia_settings;
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'sources'; 
?>
		<div class="wrap">
		  <div id="icon-upload" class="icon32"></div>
		  <h2><?php _e('Media Manager Plus', $this->plugin_l10n); ?></h2>
		  <h2 class="nav-tab-wrapper">
			  <?php foreach( $wpsf_ubermedia_settings as $tab ){ ?>
				<a href="?page=<?php echo $_GET['page']; ?>&tab=<?php echo $tab['section_id']; ?>" class="nav-tab<?php echo ($active_tab == $tab['section_id'] ? ' nav-tab-active' : ''); ?>"><?php echo $tab['section_title']; ?></a>
				<?php } ?>
			  </h2>
		  <form action="options.php" method="post">
				<?php settings_fields( $this->wpsf->get_option_group() ); ?>
				<?php $this->do_settings_sections( $this->wpsf->get_option_group() ); ?>
		  </form>
		</div>
<?php
	}
	
	function do_settings_sections($page) {
        global $wp_settings_sections, $wp_settings_fields;
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'sources'; 
        if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
            return;
        foreach ( (array) $wp_settings_sections[$page] as $section ) {
            echo '<div id="section-'. $section['id'] .'"class="ubermedia-section'. ($active_tab == $section['id'] ? ' ubermedia-section-active' : '') .'">';
            
             if ($section['id'] == 'sources') {
	         	$this->setting_image_sources();   	 
             } else {
	            call_user_func($section['callback'], $section);
	            if ( !isset($wp_settings_fields) || !isset($wp_settings_fields[$page]) || !isset($wp_settings_fields[$page][$section['id']]) )
	                    continue;
	            echo '<table class="form-table">';
	            echo '<input type="hidden" name="ubermediasettings_settings[sources]" value="sources">';
	            do_settings_fields($page, $section['id']);
	            echo '</table>';
	            if ($section['id'] != 'support') submit_button();
	        }
            echo '</div>';
        }
    }
    
    function validate_settings( $input ) { 	
    	if (isset($input['sources'])) {
    		$sources = $this->default_val($this->settings, 'ubermediasettings_sources_available', array());
    		$input['ubermediasettings_sources_available'] = $sources;
    		unset($input['sources']);
    	}
    	return $input;   
    }

	function default_val( $options, $value, $default = '' ){
        if( !isset($options[$value]) ) return $default;
        else return $options[$value];
    }
	
	function setting_image_sources() {
		$sources = $this->get_sources();
		$html = '';
		if ($sources) {
			$html .= '<div id="uber-media-settings">';
			$html .= '<iframe id="logoutframe" src="https://instagram.com/accounts/logout/" width="0" height="0"></iframe>';
			$html .= '<ul>';
			foreach($sources as $source => $source_data) {
				$class = ($source_data['url'] == '#') ? 'disconnect' : 'connect';
				$btnclass = ($source_data['url'] == '#') ? '' : ' button-primary';
				$width = (isset($source_data['w'])) ? 'data-w="'. $source_data['w'] .'" ' : '';
				$height = (isset($source_data['h'])) ? 'data-h="'. $source_data['h'] .'" ' : '';
				$html .= '<li>';
				$html .= '<img src="'. plugins_url('assets/img/'. $source .'.png' , __FILE__ ) .'" alt="'. $source_data['name'] . ' logo">';
				$html .= '<a data-source="'. $source .'" '. $width . $height .'class="button uber-connect '. $class . $btnclass .'" title="'. ucfirst($class) . ' '. $source_data['name'] .'" href="'. $source_data['url'] . '">'. ucfirst($class) .'</a></li>';
			}
			$html .= '</ul>';
			$html .= '</div>';
		} 	
		if($html == '') $html = __('No available sources', $this->plugin_l10n);
		echo $html;
	}
	
	function get_sources($popup = false) {
		$options = $this->default_val($this->settings, 'ubermediasettings_sources_available', array());
		$callback = $this->callback;
		$source_dir = glob(dirname(__FILE__) .'/includes/sources/*');
		$sources = array();	    
		$show_connected = $this->default_val($this->settings, 'ubermediasettings_general_show-connected', 0);
		if ($source_dir) {
		    foreach($source_dir as $dir) {
			    $source = basename($dir, ".php");
			    $source_data['url'] = '#';
			    $source_data['name'] = ucfirst($source);
			    include_once($dir);
				$var = 'uber_media_source_'. $source;
				$obj = new $var();
				$source_data['settings'] = $obj->show_details();
			   
			    if (!array_key_exists($source. '-settings', $options) && (!$popup || ($popup && $show_connected == 0)) ) {
				   	$source_data['url'] = $obj->get_authorise_url($callback, $source);
					$source_data['w'] = $obj->get_popup_width();
					$source_data['h'] = $obj->get_popup_height();
				} 
				
				if (!$popup || 
						($popup && $show_connected == 0) ||
							($popup && $show_connected == 1 && array_key_exists($source. '-settings', $options))) 
				$sources[$source] = $source_data;
			}
		}
		return $sources;
	}

	function disconnect_source() {
        if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'uber_media' ))
            return 0;
        if ( !isset($_POST['source']))
            return 0; 
        $response['error'] = false;
        $response['message'] = '';  
        $source = $_POST['source'];
        $options = $this->default_val($this->settings, 'ubermediasettings_sources_available', array());
		if (isset($options[$source .'-settings'])) {
			unset($options[$source .'-settings']);
			$save_options = $this->settings;
			$save_options['ubermediasettings_sources_available'] = $options;
			update_option('ubermediasettings_settings', $save_options);
			$response['message'] = 'success';
		}    
        echo json_encode($response);
        die;
    }
    
    function connect_check() {
        if ( !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'uber_media' ))
            return 0;
        if ( !isset($_POST['source']))
            return 0; 
        $response['error'] = false;
        $response['message'] = '';  
        $source = $_POST['source'];
        $options = $this->default_val($this->settings, 'ubermediasettings_sources_available', array());
		if (isset($options[$source .'-settings'])) {
			$response['message'] = 'success';
		}    
        echo json_encode($response);
        die;
    }
    
    function image_sources_header() {
		if(isset($_GET['page']) && $_GET['page'] == 'uber-media' && isset($_GET['type'])) {
		   	$options = $this->default_val($this->settings, 'ubermediasettings_sources_available', array());
		    $source = $_GET['type'];
		    $request_code = $_GET['oauth_verifier'];

		    if (isset($_SESSION[$source .'_oauth_token']) && isset($_SESSION[$source .'_oauth_token_secret'])) {
				$auth_token = $_SESSION[$source .'_oauth_token'];
			    $auth_token_secret = $_SESSION[$source .'_oauth_token_secret'];

			    $callback = $this->callback;
			    $source_dir = dirname(__FILE__) .'/includes/sources/'; 
				include_once($source_dir . $source .'.php');
				
				$var = 'uber_media_source_'. $source;
				$obj = new $var($auth_token, $auth_token_secret);
			    $token = $obj->getAccessToken($request_code, $callback);
			 
			    if( isset($token['oauth_token']) && isset($token['oauth_token_secret']) ) {
				    $options[$source .'-settings'] = array('access-token' => $token);

					$save_options = $this->settings;
					$save_options['ubermediasettings_sources_available'] = $options;
					update_option('ubermediasettings_settings', $save_options);
					
					if (isset($_SESSION[$source .'_oauth_token'])) unset($_SESSION[$source .'_oauth_token']);
					if (isset($_SESSION[$source .'_oauth_token_secret'])) unset($_SESSION[$source .'_oauth_token_secret']);    
				}
			}
			?>
			<script>
				window.close();
			</script>
			<?php
		}   
	}
	
	function load_images(){
        if ( !isset($_POST['param'])  || !isset($_POST['method'])  || !isset($_POST['source']))
            return 0;   
        $response['error'] = false;
        $response['message'] = '';
        $response['images'] = array();
        $images = array();
 
        $image_source =  $_POST['source'];
    	$options = $this->default_val($this->settings, 'ubermediasettings_sources_available', array());
    	if(isset($options[$image_source .'-settings'])) {
	    	$source_settings = $options[$image_source .'-settings'];
	    	$access_token = $source_settings['access-token'];
	    	$source_dir = dirname(__FILE__) .'/includes/sources/'; 
			include_once($source_dir . $image_source .'.php');
			$var = 'uber_media_source_'. $image_source;
			$obj = new $var($access_token['oauth_token'], $access_token['oauth_token_secret']);
			$method = $_POST['method'];
			$count = 50;
			$params = array();
			if (isset($_POST['param']) && $_POST['param'] != '') $params[] = $_POST['param'];
			if ($count != '') $params['count'] = $count;
			$safemode = $this->default_val($this->settings, 'ubermediasettings_general_safe-mode', 1);
			$params['safemode'] = $safemode;
			if (isset($_POST['page']) && $_POST['page'] != '') $params['page'] = $_POST['page'];
			if (isset($_POST['altpage']) && $_POST['altpage'] != '') $params['altpage'] = $_POST['altpage'];
			$return = call_user_func_array(array($obj, $method), $params);
			if ($return['images']) {
				foreach( $return['images'] as $image) $images[] = $image;
				if(isset($return['pagin'])) $response['pagin'] = 'end';
				if(isset($return['altpage'])) $response['altpage'] = $return['altpage'];
			} else {
				$response['error'] = true;
	        	$response['message'] = 'Failed to get '. ucfirst($image_source) .' images'. ((isset($_POST['param']) && $_POST['param'] != '') ? ' for '. $_POST['param'] : '') ;
			}
	    }	
		$response['images'] = $images;
        
        echo json_encode($response);
        die;
    }
    
    function custom_media_string($strings, $post){
		$hier = $post && is_post_type_hierarchical( $post->post_type );
		$strings['ubermedia'] = $this->get_sources(true);
		$strings['ubermediaButton'] = $hier ? __( 'Insert into page', $this->plugin_l10n ) : __( 'Insert into post', $this->plugin_l10n );
		return $strings;
	}
	
	function print_media_templates() {
	?>
		<script type="text/html" id="tmpl-uberimage">
			<img id="{{ data.id }}" src="{{ data.thumbnail }}" width="120" alt="{{ data.caption }}" title="{{ data.caption }}" data-full="{{ data.full }}" data-link="{{ data.link }}" />
			<a class="check" href="#" title="Deselect"><div id="check-{{ data.id }}" class="media-modal-icon"></div></a>
		</script>
		<script type="text/html" id="tmpl-uberimage-settings">
			<div class="attachment-info">
				<h3>{{{ data.custom_data.title }}}</h3>
				<span id="uberload" class="spinner" style="display: block"></span>
				<input id="full-uber" type="hidden" value="{{ data.custom_data.dataset.full }}" />
				<div class="thumbnail">
				</div>
			</div>
			<?php if ( ! apply_filters( 'disable_captions', '' ) ) : ?>
				<label class="setting caption">
					<span><?php _e('Caption', $this->plugin_l10n); ?></span>
					<textarea id="caption-uber" data-setting="caption"></textarea>
				</label>
			<?php endif; ?>
			
			<label class="setting alt-text">
				<span><?php _e('Title', $this->plugin_l10n); ?></span>
				<input id="title-uber" type="text" data-setting="title" value="{{{ data.custom_data.title }}}" />
			</label>
			
			<label class="setting alt-text">
				<span><?php _e('Alt Text', $this->plugin_l10n); ?></span>
				<input id="alt-uber" type="text" data-setting="alt" value="{{{ data.custom_data.title }}}" />
			</label>
	
			<div class="setting align">
				<span><?php _e('Align', $this->plugin_l10n); ?></span>
				<div id="align-button" class="button-group button-large" data-setting="align">
					<button class="button" value="left">
						<?php esc_attr_e('Left'); ?>
					</button>
					<button class="button" value="center">
						<?php esc_attr_e('Center'); ?>
					</button>
					<button class="button" value="right">
						<?php esc_attr_e('Right'); ?>
					</button>
					<button class="button active" value="none">
						<?php esc_attr_e('None'); ?>
					</button>
				</div>
			</div>
	
			<div class="setting link-to">
				<span><?php _e('Link To', $this->plugin_l10n); ?></span>
				<div id="link-button" class="button-group button-large" data-setting="link">
					<button class="button" value="{{ data.custom_data.dataset.full }}">
						<?php esc_attr_e('Image URL'); ?>
					</button>
					<button class="button" value="{{ data.custom_data.dataset.link }}">
						<?php esc_attr_e('Page URL'); ?>
					</button>
					<button class="button active" value="none">
						<?php esc_attr_e('None'); ?>
					</button>
				</div>
			</div>
		</script>
	<?php
	}

	
}


?>