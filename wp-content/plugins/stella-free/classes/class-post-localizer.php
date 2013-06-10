<?php
/*
 * Post_Localizer
 * Manages metaboxes to store language versions of posts
 * Substitude post title,body
 * Usage: Everywhere
 */

class Post_Localizer {

	private $langs;
	private $use_hosts;
	private $use_default_lang_values;
	private $empty_field_notices;
	function __construct() {
		add_action('stella_parameters', array($this, 'start'), 10, 4);
	}

	function start($langs, $use_hosts, $use_default_lang_values, $empty_field_notices) {
		$this->langs = $langs;
		$this->use_hosts = $use_hosts;
		$this->use_default_lang_values = $use_default_lang_values;
		$this->empty_field_notices = $empty_field_notices;
		if ( ! is_admin() ){
			// changes post title and body
			add_filter('the_title', array($this, 'localize_title'), 1, 2);
			if(class_exists('WPSEO_Frontend'))
				add_filter('wpseo_title', array($this,'localize_wpseo_title'), 1, 1);
			add_filter('single_post_title', array($this, 'localize_single_post_title'), 1, 2);
			add_filter('the_content', array($this, 'localize_body'), 1, 1);
			add_filter('get_the_excerpt', array($this, 'localize_excerpt'), 1, 1);
			//add_filter('post_link', array($this, 'localize_slug'), 1, 3);
			//add_action('pre_get_posts', array($this, 'filter_query_for_slug'));
			//add_filter('stella_get_permalink', array($this, 'filter_stella_permalink'), 1, 2);
			// don't show posts without title and body
			add_filter('posts_where_request', array($this, 'filter_get_posts_where'), 1, 1);
			add_filter('posts_join_request', array($this, 'filter_get_posts_join'), 1, 1);
			add_filter('posts_distinct_request', array($this, 'filter_get_posts_distinct'), 1, 1);
			add_filter('posts_search', array($this,'localize_search'), 1, 1);
			//add_filter('posts_request', array($this,'show_request'), 1, 1 );
		}
		// metaboxes
		add_action('add_meta_boxes', array($this, 'add_metaboxes'), 100);
		add_action('save_post', array($this, 'save_metaboxes'), 1, 2);

		// adding styles and scripts
		add_action('admin_init', array($this, 'add_styles'));
		add_action('admin_enqueue_scripts', array($this, 'add_scripts'));
		
		//notice messages
		if( $this->empty_field_notices )
			add_action('admin_notices',array($this, 'fields_checker'));
		
	}
	function fields_checker(){
		global $post;
		// is edit post page 
		if( false != strpos($_SERVER['REQUEST_URI'],"post.php") ){ 
			// default lang field
			$notices = array();
			$default_notices = array();
			
			if( '' == $post->post_title )
				$default_notices[] = __('Title');
			
			if( '' == $post->post_content )
				$default_notices[] = __('Content');
			
			if( !has_post_thumbnail($post->ID) )
				$default_notices[] = __('Featured Image');

			if( 0 != count($default_notices) ) $notices[ $this->langs['default']['name'] ] = $default_notices;
			
			// other languages fields
			foreach($this->langs['others'] as $prefix=>$lang){
				$others_notices = array();
				if( '' == get_post_meta( $post->ID, '_title-' . $prefix, true ) )
					$others_notices[] = __('Title');				
				if( '' == get_post_meta( $post->ID, '_body-' . $prefix, true ) )
					$others_notices[] = __('Content');
				if( class_exists('MultiPostThumbnails') ) {
					if( !MultiPostThumbnails::has_post_thumbnail( get_post_type($post->ID), 'postimagediv-'.$lang['prefix'], $post->ID) )
						$others_notices[] = __('Featured Image');
				}
				if( 0 != count($others_notices) ) $notices[ $lang['name'] ] = $others_notices;
			}
			// generate message
			if( 0 != count($notices) ){
				$notices_string = '';
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
	function filter_stella_permalink( $href, $lang_prefix ){
		/*if ( $lang_prefix != STELLA_DEFAULT_LANG ){	
			$slug_new = get_post_meta( $post->ID, 'slug-' . $lang_prefix, true );
			$href = str_replace( $post->post_name, $slug_new, $href );
		}*/
		return $href;
	}
	function filter_get_posts_distinct( $distinct ){
		global $wpdb;
		if( is_search() && STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG)
			$distinct = " DISTINCT $wpdb->posts.ID, $distinct";
		return $distinct;
	}
	function filter_get_posts_where( $where ){
		global $wpdb;
		$c_lang = STELLA_CURRENT_LANG;
		if( is_search() && STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG ){
				$where = "AND (wpm.post_id = $wpdb->posts.ID)" . $where;
		}else{
			if( STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG && ! $this->use_default_lang_values ){
				$where.=" AND exists ( SELECT * FROM $wpdb->postmeta pm 
					WHERE pm.post_id = $wpdb->posts.ID 
					AND (pm.meta_key = '_title-{$c_lang}' OR pm.meta_key = '_body-{$c_lang}'))";
			}
		}
		return $where;
	}
	function filter_get_posts_join( $join ){
		if( is_search() && STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG ){
				global $wpdb; 
				$join.=", $wpdb->postmeta wpm";
		}
		return $join;
	}
	function localize_search( $search ){
		if( is_search() && STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG ){
			$c_lang = STELLA_CURRENT_LANG;
			global $wp_query;
			global $wpdb;
			$q = &$wp_query->query_vars;
			
			if ( !empty($q['s']) && '' != $search ) {
				$search = '';
				// added slashes screw with quote grouping when done early, so done later
				$q['s'] = stripslashes($q['s']);
				if ( !empty($q['sentence']) ) {
					$q['search_terms'] = array($q['s']);
				} else {
					preg_match_all('/".*?("|$)|((?<=[\r\n\t ",+])|^)[^\r\n\t ",+]+/', $q['s'], $matches);
					$q['search_terms'] = array_map('_search_terms_tidy', $matches[0]);
				}
				$n = !empty($q['exact']) ? '' : '%';
				$searchand = '';
				foreach( (array) $q['search_terms'] as $term ) {
					$term = esc_sql( like_escape( $term ) );
					$search .= "{$searchand}((wpm.meta_key LIKE '_title-$c_lang' OR wpm.meta_key LIKE '_body-$c_lang') AND wpm.meta_value LIKE '{$n}{$term}{$n}')";
					$searchand = ' AND ';
				}

				if ( !empty($search) ) {
					$search = " AND ({$search}) ";
					if ( !is_user_logged_in() )
						$search .= " AND ($wpdb->posts.post_password = '') ";
				}
			}
		}
		return $search;
	}
	function localize_slug( $permalink, $post, $leavename ){
		if( is_multisite() )
			$permalink_structure = get_blog_option( get_current_blog_id(), 'permalink_structure' );
		else
			$permalink_structure =  get_option( 'permalink_structure' );
		
		if ( STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG && false !== strpos( $permalink_structure, '%postname%' ) ){
			$slug_new = get_post_meta($post->ID, '_slug-' . STELLA_CURRENT_LANG, true);

			if ( '' != $slug_new ){
				$permalink = str_replace($post->post_name, $slug_new, $permalink);
			}		
		}
		return $permalink;
	}
	function localize_title( $title, $id ){
		//print_r(STELLA_CURRENT_LANG);die;
		
		if ( STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG ){
			$title_new = get_post_meta( $id, '_title-' . STELLA_CURRENT_LANG, true );
			if ( '' != $title_new || ! $this->use_default_lang_values )
				return $title_new;	
		}
		return $title;
	}
	function localize_wpseo_title( $title ){
		if( is_single() && ( STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG ) ){
			global $post;
			$title_new = get_post_meta( $post->ID, '_title-' . STELLA_CURRENT_LANG, true );
			
			if ( '' != $title_new || ! $this->use_default_lang_values ){
				return str_replace( $post->post_title, $title_new, $title );
			}
			return $title;
		}
		return $title;
	}
	function localize_single_post_title($title, $post){
		return $this->localize_title($title, $post->ID);
	}
	function localize_excerpt($excerpt){
		global $post;
		
		if ( STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG ){
			$excerpt_new = get_post_meta( $post->ID, '_excerpt-' . STELLA_CURRENT_LANG, true );

			if ('' != $excerpt_new || ! $this->use_default_lang_values)
				return $excerpt_new;
		}
		return $excerpt;
	}
	function localize_body($body) {
		global $post;
		
		if ( STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG ){
			$body_new = get_post_meta( $post->ID, '_body-' . STELLA_CURRENT_LANG, true );
			
			$noteaser = ( (false !== strpos($body_new, '<!--noteaser-->') ) ? true : false );
			$extends = get_extended( $body_new );
			$extended = $extends['extended'];
			$body_new = $extends['main'];
			
			if ( ! is_single() ) {
				
				$more_link_text = apply_filters( 'stella_more_link_text', __( '(more...)' ) );
				$more_link = apply_filters( 'the_content_more_link', ' <a href="' . get_permalink() . "#more-{$post->ID}\" class=\"more-link\">$more_link_text</a>", $more_link_text );

				if ( '' != $extended ) {
						$body_new .= $more_link;
				}
			} else {
				if ( $noteaser )
					$body_new = '';

				$body_new .= (0 != strlen( $body_new )) ? '<span id="more-' . $post->ID . '"></span>' . $extended : $extended;
			}

			if (0 != strlen( $body_new ) || ! $this->use_default_lang_values){
				return $body_new;
			}
		}
		return $body;
	}
	// find correct slug name 
	function filter_query_for_slug($array){
		if ( STELLA_CURRENT_LANG != STELLA_DEFAULT_LANG && '' != $array->query_vars['name'] ){
			$current_lang = STELLA_CURRENT_LANG;
			$query_post_name = $array->query_vars['name'];
			global $wpdb;
			$real_post_slug = $wpdb->get_var( $wpdb->prepare("SELECT $wpdb->posts.post_name FROM $wpdb->posts, $wpdb->postmeta 
												WHERE $wpdb->postmeta.meta_key = 'slug-$current_lang' AND $wpdb->postmeta.post_id = $wpdb->posts.ID 
													AND $wpdb->postmeta.meta_value = '$query_post_name'"));
			$array->query_vars['name'] = $real_post_slug;
		}
	}
	function add_metaboxes() {
		// Searching for custom post types.
		$post_types = get_post_types('', 'names');
		
		// Adding metaboxes for every post type.
		foreach ($this->langs['others'] as $prefix => $lang) {
			foreach ($post_types as $post_type) {
				add_meta_box('post-in-' . $lang['prefix'], $lang['prefix'], array($this, 'show_title_body_metabox'), $post_type, 'normal', 'high', array('prefix' => $lang['prefix']));
				add_meta_box('excerpt-' . $lang['prefix'], __('Excerpt') . '-' . $lang['name'], array($this, 'show_excerpt_metabox'), $post_type, 'normal', 'high', array('prefix' => $lang['prefix']));
			}
		}
	}
	function show_excerpt_metabox( $post, $metabox ){
		$prefix = $metabox['args']['prefix'];
		$excerpt = get_post_meta( $post->ID, '_excerpt-' . $prefix, true );
		$help = __('Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="http://codex.wordpress.org/Excerpt" target="_blank">Learn more about manual excerpts.</a>');
		$meta_html = <<<meta_html
			<textarea rows="1" cols="40" name="excerpt-$prefix" id="excerpt-$prefix" class="post-excerpt">$excerpt</textarea>
			<p>$help</p>	
meta_html;
		echo $meta_html;
	}
	function show_title_body_metabox( $post, $metabox ){
		// getting meta data
		
		$prefix = $metabox['args']['prefix'];
		$body = get_post_meta( $post->ID, '_body-' . $prefix, true );
		$title = get_post_meta( $post->ID, '_title-' . $prefix, true );
		
		//$slug = get_post_meta($post->ID, '_slug-' . $prefix, true);
		$post_type = get_post_type($post);
		
		//$slug = ('' == $slug) ? $title : $slug;

		$permalink_html = '<div id="edit-slug-box"><strong></strong><span id="sample-permalink"></span></div>';
		
		if (!post_type_supports($post_type, 'editor'))
			$permalink_html = "";
		$title_html = <<<title_html
		<div class="titlediv">
			<input class="like-default-title" type="text" name="title-$prefix" value="$title"/>	
			$permalink_html
		</div>
title_html;
		echo $title_html;

		if (post_type_supports($post_type, 'editor'))
			wp_editor($body, 'tinymce' . $prefix, array('textarea_name' => 'body-' . $prefix, 'media_buttons' => true, 'tinemce' => true));
	}

	function save_metaboxes($post_id, $post) {
		
		$post_meta = array();
		// saving post metaboxes - title, body 
		foreach ($this->langs['others'] as $prefix => $lang) {
			if ( isset( $_POST['title-' . $lang['prefix']] ) )
				$post_meta['_title-' . $lang['prefix']] = $_POST['title-' . $lang['prefix']];

			if ( isset( $_POST['body-' . $lang['prefix']] ) )
				$post_meta['_body-' . $lang['prefix']] = $_POST['body-' . $lang['prefix']];
			
			if ( isset( $_POST['excerpt-' . $lang['prefix']] ) )
				$post_meta['_excerpt-' . $lang['prefix']] = $_POST['excerpt-' . $lang['prefix']];
			
		}
		foreach ($post_meta as $key => $value) {
			if ($post->post_type == 'revision')
				return;
			if (get_post_meta($post->ID, $key, FALSE)) {
				update_post_meta($post->ID, $key, $value);
			} else {
				add_post_meta($post->ID, $key, $value);
			}
			if (!$value)
				delete_post_meta($post->ID, $key);
		}
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
		global $post;
		$post_type = ( isset($post) ) ? get_post_type( $post->ID ) : 'any';
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('stella_post_tabs', stella_plugin_url() . 'js/post-tabs.js');
		wp_localize_script('stella_post_tabs', 'post_vars', json_encode(array('langs' => $this->get_indexed_language_list(), 'post_type' => $post_type, 'default_str' => __('Default','stella-plugin'))));
	}
}
new Post_Localizer();
?>
