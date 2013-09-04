<?php
/**
 * Get template part (for templates like the loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function bon_toolkit_get_template_part( $slug, $name = '' ) {

	global $bontoolkit;

	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/bon-toolkit/slug-name.php
	if ( $name )
		$template = locate_template( array ( "{$slug}-{$name}.php", "{$bontoolkit->template_url}{$slug}-{$name}.php" ) );

	// Get default slug-name.php
	if ( !$template && $name && file_exists( $bontoolkit->plugin_path() . "/templates/{$slug}-{$name}.php" ) )
		$template = $bontoolkit->plugin_path() . "/templates/{$slug}-{$name}.php";

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/bon-toolkit/slug.php
	if ( !$template )
		$template = locate_template( array ( "{$slug}.php", "{$bontoolkit->template_url}{$slug}.php" ) );

	if ( $template )
		load_template( $template, false );

}

if( !function_exists('bon_toolkit_get_prefix') ) {
	
	function bon_toolkit_get_prefix() {
		global $bon, $bontoolkit;

		if(function_exists('bon_get_prefix')) {
			$prefix = bon_get_prefix();
		} else {
			$prefix = $bontoolkit->prefix;
		}
		
		return $prefix;
	}

}

if( !function_exists('bon_toolkit_get_builder_suffix') ) {

	
	function bon_toolkit_get_builder_suffix() {

		global $bontoolkit;

		return $bontoolkit->suffix;
	}
}
if( !function_exists('bon_toolkit_get_categories') ) {

	/**
	 * @return array
	 * @param string $name
	 * @param string $parent
	 */
	function bon_toolkit_get_categories( $name, $parent = '' ){
		
		if( empty($parent) ){ 
			$get_category = get_categories( array( 'taxonomy' => $name, 'hide_empty' => 0	));
			
			$category_list = array();

			$category_list['all'] = 'All';
			if( !empty($get_category) ){
				foreach( $get_category as $category ){
					$category_list[$category->slug] = $category->name;
				}
			}
				
			return $category_list;

		} else {
			$parent_id = get_term_by('slug', $parent, $category_name);
			$get_category = get_categories( array( 'taxonomy' => $name, 'child_of' => $parent_id->term_id, 'hide_empty' => 0	));
			$category_list = array( '0' => $parent );
			
			if( !empty($get_category) ){
				foreach( $get_category as $category ){
					$category_list[$category->slug] = $category->name;
				}
			}
				
			return $category_list;		
		}
	}
	
}

if( !function_exists('bon_toolkit_get_post_id_lists')) {
	/**
	 * @return array
	 * @param string $post_type
	 */
	function bon_toolkit_get_post_id_lists( $post_type, $numberposts = 100 ){
		
		$posts = get_posts(array('post_type' => $post_type, 'numberposts'=> $numberposts));
		
		$posts_title = array();

		if(!empty($posts)) {

			foreach ($posts as $post) {
				$posts_title[$post->ID] = $post->post_title;
			}

		}

		return $posts_title;
	
	}		
} 

if( !function_exists('bon_toolkit_default_widget_args') ) {

	function bon_toolkit_default_widget_args() {
		if(function_exists('bon_get_default_widget_args') ) {
			return bon_get_default_widget_args();
		} else {

			$defaults = array(
				'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>'
			);

			return $defaults;
		}
	}
}

if( !function_exists('bon_toolkit_process_contact_form') ) {

	add_action('wp_ajax_process_contact_form','bon_toolkit_process_contact_form');
	add_action('wp_ajax_nopriv_process_contact_form','bon_toolkit_process_contact_form');

	function bon_toolkit_process_contact_form() {

		if(!isset($_POST) || empty($_POST)) {
			$return_data['value'] = __('Cannot send email to destination. No parameter receive form AJAX call.','bon-toolkit');	
			die ( json_encode($return_data) );
		}

		$name = esc_html( $_POST['name'] );

		if(empty($name)) {
			$return_data['value'] = __('Please enter your name.','bon-toolkit');
			die ( json_encode($return_data) );
		}

		$email = sanitize_email( $_POST['email'] );

		if(empty($email)){
			$return_data['value'] = __('Please enter a valid email address.','bon-toolkit');
			die ( json_encode($return_data) );		
		}


		$subject = esc_html( $_POST['subject'] );

		$messages = esc_textarea( $_POST['messages'] );

		if(empty($messages)){ 
			$return_data['value'] = 'Please enter your messages.';
			die ( json_encode($return_data) );				
		}

		if(function_exists('akismet_http_post') && trim(get_option('wordpress_api_key')) != '' ) {
			global $akismet_api_host, $akismet_api_port;
			$c['user_ip']			= preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
			$c['blog']			= get_option('home');
			$c['comment_author']	= $name;
			$c['comment_author_email'] = $email;
			$c['comment_content'] 	= $messages;

			$query_string = '';
			foreach ( $c as $key => $data ) {
				if( is_string($data) )
					$query_string .= $key . '=' . urlencode( stripslashes($data) ) . '&';
			}
			
			$response = akismet_http_post($query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
			
			if ( 'true' == $response[1] ) { // Akismet says it's SPAM
				$return_data['value'] = __('Cheatin Huh?!', 'bon-toolkit');
				die( json_encode($return_data) );
			}
		}

		$receiver = $_POST['receiver'];

		$body = "You have received a new contact form message via ".get_bloginfo('name')." \n";
		$body .= 'Name : ' . $name . " \n";
		$body .= 'Email : ' . $email . " \n";
		$body .= 'Subject : ' . $subject . " \n";
		$body .= 'Message : ' . $messages;

		$header = "From: " . $name . " <" . $email . "> \r\n";
		$header .= "Reply-To: " . $email;

		$subject_email = "[".get_bloginfo('name')." Contact Form] ".$subject;

		if( wp_mail($receiver, $subject_email , $body, $header) ){
			$return_data['success'] = '1';
			$return_data['value'] = __('Email was sent successfully.','bon-toolkit');
			die( json_encode($return_data) );
		} else {
			$return_data['value'] = __('There is an error sending email.','bon-toolkit');
			die( json_encode($return_data) );	
		}

	}
}
if( !function_exists('bon_toolkit_filter_builder_class') ) {
	
	function bon_toolkit_filter_builder_class($class) {
		$class = str_replace('span', 'column-span-', $class);
		return 'bon-toolkit-col '.$class;
	}
	add_filter('bon_toolkit_builder_render_column_class', 'bon_toolkit_filter_builder_class', 1);
}

if( !function_exists('bon_toolkit_builder_row_class') ) {
	
	function bon_toolkit_builder_row_class($args) {
		$args[] = 'bon-toolkit-column-grid';
		$args[] = 'column-grid-12';
		return $args;
	}

	add_filter('bon_tookit_builder_render_row_class', 'bon_toolkit_builder_row_class', 1); 
}

if( !function_exists('bon_toolkit_check_options') ) {

	function bon_toolkit_check_options($options) {

		global $bontoolkit;

		$bon_toolkit_options = get_option($bontoolkit->option_name);

		if( isset($bon_toolkit_options[$options]) && $bon_toolkit_options[$options] == true) {
			return true;
		}

		return false;
	}

}

if( !function_exists('bon_toolkit_video') ) {
	function bon_toolkit_video($args) {

		if( isset($GLOBALS['bon_toolkit_video_count']) )
            $GLOBALS['bon_toolkit_video_count']++;
        else
            $GLOBALS['bon_toolkit_video_count'] = 0;

		$defaults = array(
			'embed' => '',
			'm4v' => '',
			'ogv' => '',
			'poster' => '',
			'id' => $GLOBALS['bon_toolkit_video_count'],
			'echo' => true,
			'desc' => '',
		);

		$args = wp_parse_args( $args, $defaults);

		extract($args);
		global $bontoolkit;

		$awe = BON_TOOLKIT_FONT_AWESOME;

		if(!$awe) {
			$awe = 'awe-';
		}

		$o = '<div class="bon-toolkit-video">';
		if(!empty($embed)) {
	    	$o .= '<div class="bon-toolkit-video-embed">';
	    	$embed_code = wp_oembed_get($embed);
	    	$o .= $embed_code;
	    	$o .= '</div>';

	    } else if(!empty ($m4v) && !empty($ogv)) {
	    	$o .= '<div id="jp-"'.$id.' class="bon-toolkit-jplayer jp-jplayer jp-jplayer-video" data-poster="'.$poster.'" data-m4v="'.$m4v.'" data-ogv="'.$ogv.'"></div>';

	    	$o .= '<div class="jp-video-container">
	            <div class="jp-video">
	                <div class="jp-type-single">
	                    <div id="jp-interface-'.$id.'" class="jp-interface">
	                        <div class="jp-controls">
	                            <div class="jp-play" tabindex="1">
	                                <span class="'.$awe.'play icon"></span>
	                            </div>
	                            <div class="jp-pause" tabindex="1">
	                                <span class="'.$awe.'pause icon"></span>
	                            </div>
	                            <div class="jp-progress-container">
	                                <div class="jp-progress">
	                                    <div class="jp-seek-bar">
	                                        <div class="jp-play-bar"></div>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="jp-mute" tabindex="1"><span class="'.$awe.'volume-up icon"></span></div>
	                            <div class="jp-unmute" tabindex="1"><span class="'.$awe.'volume-off icon"></span></div>
	                            <div class="jp-volume-bar-container">
	                                <div class="jp-volume-bar">
	                                    <div class="jp-volume-bar-value"></div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>';
	    } else {
	    	$o .= '<p>'. __('Error Loading Video', 'bon-toolkit') . '</p>';
	    }
	    if($desc != '') {
			$o .= '<div class="bon-toolkit-video-desc">' . $desc . '</div>';
	    }

	    $o .= '</div>';

	    if($echo === true) {
	    	echo $o;
	    } else {
	    	return $o;
	    }
	}
}

/**
 * Return an array of available widgets
 * @since 1.0
 * @uses bon_sort_name_callback();
 * @access public
 * @return array() 
 */

if( !function_exists('bon_toolkit_wp_widget_lists') ) {

	function bon_toolkit_wp_widget_lists() {
		global $wp_registered_widgets;

	    $sort = $wp_registered_widgets;

	    usort( $sort, 'bon_toolkit_sort_name_callback' );

	    $done = array();
	    $return = array();

	    foreach ( $sort as $widget ) {
	        if ( in_array( $widget['callback'], $done, true ) ) // We already showed this multi-widget
	            continue;

	        $done[] = $widget['callback'];
	        $return[get_class($widget['callback'][0])] = $widget['callback'][0]->name;

	    }

	    return $return;
	}
}

/**
 * Callback for sortname
 * @since 1.0
 * @access public
 * @return array() 
 */
if( !function_exists('bon_toolkit_sort_name_callback') ) {
	function bon_toolkit_sort_name_callback( $a, $b ) {
	    return strnatcasecmp( $a['name'], $b['name'] );
	}
}
?>