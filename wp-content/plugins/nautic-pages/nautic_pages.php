<?php
/*
Plugin Name: Nautic Pages
Plugin URI: http://ili.com.ua/
Description: Advanced widjet pages
Author: Yuriy Stepanov (stur)
Version: 1.0.1
Author URI: http://ili.com.ua/
*/


/* use in your template   
   $sortby  = post_title,  menu_order,  ID
if( function_exists( 'nautic_pages_previous' ) ) 
		echo nautic_pages_previous( $sortby );
*/
function nautic_pages_previous($sortby = 'post_title'){
	global $nautic_pages;
	if($nautic_pages === false) return;
	if (!isset($nautic_pages)){
			nuatic_pages_prepare();
	}
	extract( $nautic_pages, EXTR_SKIP );
	if(sizeof($current_path) AND $current_page){
		
		if($sortby == 'post_title') {
			nautic_sortby($pages,'nautic_sort_title');
		}
		elseif ($sortby == 'menu_order') {
			nautic_sortby($pages,'nautic_sort_order');
		}
		else {
			nautic_sortby($pages,'nautic_sort_id');
		}
		
		$page = $pages[$current_page];
		$parent = $page['post_parent'];
		
		$previous = false;
		if($parent){
			reset($pages[$parent]['sub_category']);
			while (list ($ID, $page) = each ($pages[$parent]['sub_category'])) {
	  		 	
					 if($ID == $current_page) {
						 break;
	  		 	}
	  		 	$previous = $ID;
	    }
		} else {
			reset($pages);
			
			$f = false;
			while (list ($ID, $page) = each ($pages)) {
					if ($page['post_parent'] != 0 ) continue;
	  		 	 
					 if($ID == $current_page) {
						 break;
	  		 	}
	  		 	$previous = $ID;
	    }
		}
		if(!$previous) return;
		$page = $pages[$previous];
		$class = "class='page_item page-item-{$previous}'";
		$link = '<a '.$class.' href="' . get_page_link($previous) . '"  title="' . attribute_escape(apply_filters('the_title', $page['post_title'], $page['id'])) . '">' . apply_filters('the_title', $page['post_title'], $page['id']) . '</a>';
		$html = "<div class='nautic_pages_previous'>$link</div>";
		return $html;
	}
	else return;
	
}
/* use in your template   
   $sortby  = post_title,  menu_order,  ID
if( function_exists( 'nautic_pages_next' ) ) 
		echo nautic_pages_next( $sortby );
*/

function nautic_pages_next($sortby = 'post_title'){
	global $nautic_pages;
	if($nautic_pages === false) return;
	if (!isset($nautic_pages)){
			nuatic_pages_prepare();
	}
	extract( $nautic_pages, EXTR_SKIP );
	if(sizeof($current_path) AND $current_page){
		
		if($sortby == 'post_title') {
			nautic_sortby($pages,'nautic_sort_title');
		}
		elseif ($sortby == 'menu_order') {
			nautic_sortby($pages,'nautic_sort_order');
		}
		else {
			nautic_sortby($pages,'nautic_sort_id');
		}
		
		$page = $pages[$current_page];
		$parent = $page['post_parent'];
		
		$next = false;
		if($parent){
			reset($pages[$parent]['sub_category']);
			while (list ($ID, $page) = each ($pages[$parent]['sub_category'])) {
	  		 	if($ID == $current_page) {
	  		 		 $next = key($pages[$parent]['sub_category']);
						 break;
	  		 	}
	    }
		} else {
			reset($pages);
			
			$f = false;
			while (list ($ID, $page) = each ($pages)) {
					if ($page['post_parent'] != 0 ) continue;
	  		 	if($f) {
	  		 		$next = $ID;
  			 		break;
	  		 	}
  	 		 	if($ID == $current_page) $f = true;
	    }
		}
		if(!$next) return;
		$page = $pages[$next];
		$class = "class='page_item page-item-{$next}'";
		$link = '<a '.$class.' href="' . get_page_link($next) . '"  title="' . attribute_escape(apply_filters('the_title', $page['post_title'], $page['id'])) . '">' . apply_filters('the_title', $page['post_title'], $page['id']) . '</a>';
		$html = "<div class='nautic_pages_next'>$link</div>";
		return $html;
	}
	else return;
	
}
/* use in your template 
 if( function_exists( 'nautic_pages_path' ) ) 
		echo nautic_pages_path( array(	'separator' => '>', 'show_latest' => true) );
*/
function nautic_pages_path($args){
	global $nautic_pages;
	if($nautic_pages === false) return;
	if (!isset($nautic_pages)){
			nuatic_pages_prepare();
	}
	extract( $nautic_pages, EXTR_SKIP );	

	if(sizeof($current_path) AND $current_page){
            $defaults = array(
                    'separator' => '>',
                    'show_latest' => true
            );
            $r = wp_parse_args( $args, $defaults );
            extract( $r, EXTR_SKIP );
            $html .= '<div class="nautic_pages_path">';
            foreach ($current_path as $depth=>$ID) {
                    $page = $pages[$ID];
	    if($page['current'] == 'current_page_item' AND $show_latest)
                    $html .=  apply_filters('the_title', $page['post_title'], $page['id']);    	
	    else {
                    $class = "class = 'page_item page-item-{$ID} {$page['current']} depth-$depth'";	    
                    $html .= '<a '.$class.'href="' . get_page_link($ID) . '" title="' . attribute_escape(apply_filters('the_title', $page['post_title'], $page['id'])) . '">' . apply_filters('the_title', $page['post_title'], $page['id']) . '</a>';
                    $html .= $separator;
            }
				
  	}
  	$html .= '</div>';
  	return $html;
	} else return;

}
function nautic_sort_title	($a, $b)	{
		$val = strnatcasecmp ($b['post_title'],$a['post_title']);
		if($val === 0) return nautic_sort_id($a, $b);
		else return $val;
}

function nautic_sort_order	($a, $b)	{
		if ($a['menu_order'] == $b['menu_order']) {
			return nautic_sort_id($a, $b);
		}
		return ($a['menu_order'] < $b['menu_order']) ? -1 : 1;
}

function nautic_sort_id	($a, $b)	{
		if ($a['id'] == $b['id']) return 0;
		return ($a['id'] < $b['id']) ? -1 : 1;
}

function nautic_sortby(&$pages,$sortby){
		uasort($pages,$sortby);
		foreach ($pages as $ID=>$page) {
  			if( sizeof($pages[$ID]['sub_category']) ) {
  					nautic_sortby($pages[$ID]['sub_category'],$sortby);
  			}
  	}
}
function current_page(&$pages,$current_page){

    if(!isset($pages[$current_page])) return false;
    $className = 'current_page_item';
    $current_path = array();
		
    do {
      $pages[$current_page]['current'] = $className;
      $className = ($className == 'current_page_item') ? 
				'current_page_ancestor current_page_parent' : 'current_page_ancestor';
			$current_path[] = $current_page;
    }while($current_page = $pages[$current_page]['post_parent']);
		return array_reverse ($current_path, false);
}

function nautic_walk_pages($pages,$options,$depth=0){
//echo '<pre>'; print_r( $options ); echo '</pre>';
global $post;
    if( $depth ) $html = "<ul class='children'>"; 
    else $html = "<ul class = 'nautic-pages'>";
    
    while ( list ($key, $page ) = each ($pages)) {
        if ( (int)$depth == 0 AND (int)$page['post_parent'] != 0 )
            continue;
    
        $html .= "<li class='page_item page-item-{$key} {$page['current']} depth-$depth'>";  
        
        if ( $page['current'] == 'current_page_item' AND !$options['aslink'] )
            $html .=  apply_filters('the_title', $page['post_title'], $page['id']);    	
        else
            $html .= '<a href="' . get_page_link($key) . '"  '. (($key == $post->ID) ? 'class="active"':'') .' title="' . attribute_escape(apply_filters('the_title', $page['post_title'], $page['id'])) . '">' . apply_filters('the_title', $page['post_title'], $page['id']) . '</a>';

        if($options['count'] && $page['chields'])
                $html .= '('.$page['chields'].')';
        elseif($page['chields'] and ( strstr($page['current'],'current_page')===false ))
                $html .= '+';
        if ( ($page['current'] == 'current_page_ancestor current_page_parent' 
                            || $page['current'] == 'current_page_ancestor'
                            || $page['current'] == 'current_page_item') 
                            and sizeof($page['sub_category'])
                and !$options['onelevel'] ) 
            $html .= nautic_walk_pages($page['sub_category'],$options,$depth+1);
        $html .= '</li>';

    }
    
    $html .= '</ul>';
    
    return $html;
}; 

function nuatic_pages_prepare(){
	global $nautic_pages;
	$data = get_pages();
	
 
  if ( empty($data) ) {
		$nautic_pages = false; 
		return;
	}

	while (list ($key, $val) = each ($data)) {
	   
		 $page['id'] =  $val->ID;
		 $page['post_author'] =  $val->post_author;
		 $page['post_date'] =  $val->post_date;
		 $page['post_title'] =  $val->post_title;
		 $page['post_name'] =  $val->post_name;
		 $page['post_parent'] =  $val->post_parent;
		 $page['menu_order'] =  $val->menu_order;
		 $page['chields'] =  0;
     $pages[$val->ID] = $page;
 	}
 	
 	ksort ($pages); // srted by ID 
 	

  unset($data); 	
  // create tree  	
	reset($pages);
	while (list ($key, $val) = each ($pages)) {
 		if ( $val['post_parent'] != 0 AND isset($pages[$val['post_parent']]) ) {
		 	$pages[$val['post_parent']]['sub_category'][$key] = & $pages[$key];
		 	$pages[$val['post_parent']]['chields'] += 1;
		} 
  }
  // current page
	global $wp_query;
	$current_page = false;
	$current_path = false;
	if ( is_page() || $wp_query->is_posts_page ){
		$current_page = $wp_query->get_queried_object_id();
		if( isset($pages[$current_page]) )
			$current_path = current_page($pages,$current_page);
	}
	$nautic_pages['pages'] = $pages;
	$nautic_pages['current_page'] = $current_page;
	$nautic_pages['current_path'] = $current_path;
}

function nautic_pages_print( $args, $widget_args = 1 ) {

	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
            $widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	// Data should be stored as array:  array( number => data for that instance of the widget, ... )
	$options = get_option('nautic_pages');
	if ( !isset($options[$number]) )
		return;
	
	global $nautic_pages;
	if (!isset($nautic_pages)){
            nuatic_pages_prepare();
	}
	if($nautic_pages === false) return;
	
	extract( $nautic_pages, EXTR_SKIP );	
	

			// exclude category
	if($options[$number]['exclude']) {
            $arr_exclude = explode(',', $options[$number]['exclude']);
            foreach ($arr_exclude as $ID) {
                if( isset($pages[$ID]) ) {
                    if($pages[$ID]['post_parent']) {
                        $parent = $pages[$ID]['post_parent'];
                        unset($pages[$parent]['sub_category'][$ID]);
                    } 
                    unset($pages[$ID]);
                }
            }
        } 

	if($options[$number]['sortby'] == 'post_title') {
		nautic_sortby($pages,'nautic_sort_title');
	}
	elseif ($options[$number]['sortby'] == 'menu_order') {
		nautic_sortby($pages,'nautic_sort_order');
	}
	else {
		nautic_sortby($pages,'nautic_sort_id');
	}


	// root category
	$data = $pages;
        $pages =array();
        $root = ((int)$options[$number]['root']) ? (int)$options[$number]['root'] : 0;
        if($root) {
          $options[$number]['depth'] = '';
                      if( sizeof($data[$root]['sub_category']) )
            $pages =  $data[$root]['sub_category'];
          else 	return __("No pages");

                      reset($pages);
              while (list ($key, $val) = each ($pages)) {
                              $pages[$key]['post_parent'] = 0;
          }
        }	
	else 
            $pages =  $data;
	
	//  depth
	if((int)$options[$number]['depth'] AND $current_page AND $current_path){
		$depth = $options[$number]['depth'];
            if( $pages = $pages[$current_path[$depth-1]]['sub_category'] ){
                while (list ($key, $val) = each ($pages)) {
                    $pages[$key]['post_parent'] = 0;
                }
            } else return;
   
	}
	elseif($options[$number]['depth']) return;

	
	$output = nautic_walk_pages($pages,$options[$number]);	
	$output = apply_filters('wp_list_pages', $output);
	$title = empty($options[$number]['title']) ? __('Pages') : $options[$number]['title'];	
        echo $before_widget;
	echo $before_title . $title . $after_title;
        echo $output;
	echo $after_widget;
}




// Displays form for a particular instance of the widget.  Also updates the data after a POST submit
// $widget_args: number
//    number: which of the several widgets of this type do we mean
function nautic_pages_control( $widget_args = 1 ) {
	global $wp_registered_widgets;
	static $updated = false; // Whether or not we have already updated the data after a POST submit
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );
	


	// Data should be stored as array:  array( number => data for that instance of the widget, ... )
	$options = get_option('nautic_pages');
	if ( !is_array($options) )
		$options = array();

		
	// We need to update the data
	if ( !$updated && !empty($_POST['sidebar']) ) {
		// Tells us what sidebar to put the data in
		$sidebar = (string) $_POST['sidebar'];


		$sidebars_widgets = wp_get_sidebars_widgets();
	
 // echo '<pre>$wp_registered_widgets'; print_r( $wp_registered_widgets ); echo '</pre>';
  
  
  

		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			// Remove all widgets of this type from the sidebar.  We'll add the new data in a second.  This makes sure we don't get any duplicate data
			// since widget ids aren't necessarily persistent across multiple updates
			echo "$_widget_id<br />";
			if ( 'nautic_pages_print' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				
				if ( !in_array( "nauticpage-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed. "many-$widget_number" is "{id_base}-{widget_number}
					unset($options[$widget_number]);

			}
		}
		

		foreach ( (array) $_POST['nautic_pages'] as $widget_number => $widget_nautic ) {
			// compile data from $widget_many_instance
			if ( !isset($widget_nautic['title']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
		
			$title = trim(strip_tags(stripslashes($widget_nautic['title'])));
			$exclude = trim(strip_tags(stripslashes($widget_nautic['exclude'])));
			$sortby = trim(strip_tags(stripslashes($widget_nautic['sortby'])));
			$root = (int) attribute_escape($widget_nautic['root']);
			$depth = (int) attribute_escape($widget_nautic['depth']);		
			$onelevel = (bool)$widget_nautic['onelevel'];
			$count = (bool)$widget_nautic['count'];
			$aslink = (bool)$widget_nautic['aslink'];
			$options[$widget_number] = compact( 'title','exclude','sortby','root','depth','onelevel','count','aslink');
		}

		update_option('nautic_pages', $options);

		$updated = true; // So that we don't go through this more than once
	}


	// Here we echo out the form
	// We echo out a template for a form which can be converted to a specific form later via JS
	if ( -1 == $number ) { 
		$number = '%i%';
		$onelevel = false;
		$aslink = 1;
	} else {
	  $title = attribute_escape( $options[$number]['title'] );
		$exclude = attribute_escape( $options[$number]['exclude'] );
		$sortby = attribute_escape($options[$number]['sortby']);
		$root = (int) attribute_escape($options[$number]['root']);
		$depth = (int) attribute_escape($options[$number]['depth']);		
		$onelevel = (bool)$options[$number]['onelevel'];
		$count = (bool)$options[$number]['count'];
		$aslink = (bool)$options[$number]['aslink'];
	}

	// The form has inputs with names like widget-many[$number][something] so that all data for that instance of
	// the widget are stored in one $_POST variable: $_POST['widget-many'][$number]
?>
			<p>
				<label for="nautic_pages-title-<?php echo $number; ?>">
					<?php _e( 'Title:' ); ?>
					<input class="widefat" id="nautic_pages-title-<?php echo $number; ?>" name="nautic_pages[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
				</label>
			</p>
  		<p>
			<label for="nautic_pages-sortby<?php echo $number; ?>"><?php _e( 'Sort by:' ); ?>
				<select name="nautic_pages[<?php echo $number; ?>][sortby]" id="nautic_pages-sortby<?php echo $number; ?>" class="widefat">
					<option value="post_title"<?php selected( $sortby, 'post_title' ); ?>><?php _e('Page title'); ?></option>
					<option value="menu_order"<?php selected( $sortby, 'menu_order' ); ?>><?php _e('Page order'); ?></option>
					<option value="ID"<?php selected( $sortby, 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
				</select>
			</label>
		</p>	
		<p>
			<label for="nautic_pages-exclude<?php echo $number; ?>"><?php _e( 'Exclude:' ); ?> 
			<input type="text" value="<?php echo $exclude; ?>" name="nautic_pages[<?php echo $number; ?>][exclude]" id="nautic_pages-exclude<?php echo $number; ?>" class="widefat" /></label>
			<br />
			<small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
		</p>
		<p>
			<label for="nautic_pages-root<?php echo $number; ?>"><?php _e( 'Root:' ); ?> 
			<input type="text" value="<?php echo $root; ?>" name="nautic_pages[<?php echo $number; ?>][root]" id="nautic_pages-root<?php echo $number; ?>" class="widefat" /></label>
			<br />
			<small><?php _e( 'Page IDs' ); ?></small>
		</p>
		<p>
			<label for="nautic_pages-depth<?php echo $number; ?>"><?php _e( 'Start depth:' ); ?> 
			<input type="text" value="<?php echo $depth; ?>" name="nautic_pages[<?php echo $number; ?>][depth]" id="nautic_pages-depth<?php echo $number; ?>" class="widefat" /></label>
			<br />
		</p>

			<label for="nautic_pages-onelevel<?php echo $number; ?>">
			<?php _e( 'Show one level' ); ?> 
			<input type="checkbox" <?php checked( $onelevel, true ); ?> name="nautic_pages[<?php echo $number; ?>][onelevel]" id="nautic_pages-onelevel<?php echo $number; ?>" class="checkbox" />
			</label>
			<br />

			<label for="nautic_pages-count<?php echo $number; ?>">
			<?php _e( 'Show post counts' ); ?>
			<input type="checkbox" <?php checked( $count, true ); ?> name="nautic_pages[<?php echo $number; ?>][count]" id="nautic_pages-count<?php echo $number; ?>" class="checkbox" />
			</label>
			<br />
			<label for="nautic_pages-aslink<?php echo $number; ?>">
			<?php _e( 'Show current page as link' ); ?>
			<input type="checkbox" <?php checked( $aslink, true ); ?> name="nautic_pages[<?php echo $number; ?>][aslink]" id="nautic_pages-aslink<?php echo $number; ?>" class="checkbox" />
			</label>

		
			<input type="hidden"  name="nautic_pages[<?php echo $number; ?>][submit]" value="1" />
		</p>
<?php
}

// Registers each instance of our widget on startup
function nautic_pages_register() {
	
	if ( !$options = get_option('nautic_pages') )
		$options = array();

//$options = array();
//update_option('nautic_pages', $options);
	$widget_ops = array('classname' => 'nautic_pages', 'description' => __('nautic PAGES'));
	$control_ops = array('id_base' => 'nauticpage');
	$name = __('Nautic Pages');

	$registered = false;
	foreach ( array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['title']) )
			continue;
		// $id should look like {$id_base}-{$o}
		$id = "nauticpage-$o"; // Never never never translate an id
		$registered = true;
		wp_register_sidebar_widget( $id, $name, 'nautic_pages_print', $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, 'nautic_pages_control', $control_ops, array( 'number' => $o ) );
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$registered ) {
		wp_register_sidebar_widget( 'nauticpage-1', $name, 'nautic_pages_print', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'nauticpage-1', $name, 'nautic_pages_control', $control_ops, array( 'number' => -1 ) );
	}
	


}

function nautic_pages_upgrade() {
	$options = get_option( 'nautic_pages' );

	if ( !isset( $options['title'] ) )
		return $options;

	$newoptions = array( 1 => $options );

	update_option( 'nautic_pages', $newoptions );

	$sidebars_widgets = get_option( 'sidebars_widgets' );
	if ( is_array( $sidebars_widgets ) ) {
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( is_array( $widgets ) ) {
				foreach ( $widgets as $widget )
					$new_widgets[$sidebar][] = ( $widget == 'nauticpage' ) ? 'nauticpage-1' : $widget;
			} else {
				$new_widgets[$sidebar] = $widgets;
			}
		}
		if ( $new_widgets != $sidebars_widgets )
			update_option( 'nautic_pages', $new_widgets );
	}

	return $newoptions;
}
// This is important
add_action( 'widgets_init', 'nautic_pages_register' )



?>