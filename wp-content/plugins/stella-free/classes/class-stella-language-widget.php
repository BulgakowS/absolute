<?php

/*
 * Stella_Language_Widget
 * output language menu
 * Usage: Everywhere
 */
if( !class_exists('Stella_Language_Widget') ){
	class Stella_Language_Widget extends WP_Widget{
		private $lang_menu; 
		function __construct() {
			parent::__construct(
				'lang_widget', // Base ID
				__( 'Stella language menu', 'stella-plugin' ), // Name
				array( 'description' => __( 'Shows the language selection menu', 'stella-plugin' ), ) // Args
			);
			$this->lang_menu = stella_get_lang_list();
		}
		function get_language_list_html() {
			$lang_list_html = '<ul id="languages">';
			if(isset($this->lang_menu)){
				foreach ($this->lang_menu as $prefix=>$item){
                                    $lang = strtolower(substr($item['title'], 0, 2));
                                    $href = is_ssl() ? 'https://'.$item['href'] : 'http://'.$item['href'];
                                    $lang_list_html .= '<li><a class="lang_item ';
                                    $lang_list_html .= $lang == stella_get_current_lang() ? 'active' : '';
                                    $lang_list_html .= '" id="lang_'.$item['title'].'" href="'.$href.'"></a></li>';
				}
			}		
			$lang_list_html .= '</ul>';
			return $lang_list_html;
		}
		public function form( $instance ){
			
			if(isset($this->lang_menu)){
				foreach ($this->lang_menu as $prefix=>$item){
					$label = __('Title').' - '.$item['title'];
					$title = ( isset($instance['title-'.$prefix]) ) ? $instance['title-'.$prefix] : '';
					?><p><label for="<?php echo $this->get_field_id( 'title-'.$prefix ); ?>"><?php echo $label; ?></label>
						<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title-'.$prefix ); ?>" name="<?php echo $this->get_field_name( 'title-'.$prefix ); ?>" value="<?php echo $title; ?>"></p>
					<?php
				}
			}
		}
		public function update( $new_instance, $old_instance ){
			$instance = array();
			if(isset($this->lang_menu)){
				foreach ($this->lang_menu as $prefix=>$item){
					$instance['title-'.$prefix] = strip_tags( $new_instance['title-'.$prefix] );
				}
			}
			return $instance;
		}
		public function widget( $args, $instance ) {
			extract( $args );
			$title = apply_filters('stella_language_widget_title', empty( $instance['title-'.STELLA_CURRENT_LANG] ) ? __( 'Language', 'stella-plugin' ) : $instance['title-'.STELLA_CURRENT_LANG], $instance, $this->id_base);
			echo $before_widget;
			if ( ! empty( $title ) ) {
				if ( empty( $before_title ) )
					$before_title = '<h3 class="widget-title">';
				if ( empty( $after_title ) )
					$after_title = '</h3>';
				echo $before_title . $title . $after_title;
			}
			echo $this->get_language_list_html();
			echo $after_widget;
		}
	}
	add_action( 'widgets_init', create_function( '', 'register_widget( "Stella_Language_Widget" );' ));
}

?>
