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

		$phone = '';
		if( isset( $_POST['phone'] ) ) {
			$phone = sanitize_text_field( $_POST['phone'] );
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
		if( !empty( $phone ) ) {
			$body .= 'Phone :' . $phone . " \n";
		}
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

if( !function_exists('bon_toolkit_get_contact_form') ) {
	function bon_toolkit_get_contact_form($email = '', $color = '') {

		$color = ( $color != '' ) ? $color : 'blue';

		if(empty($email) || !is_email($email)) {
			return __('Failed rendering contact form. Please provide a correct <strong>Email Address</strong>.','bon-toolkit');
		}

		$o = apply_filters('bon_toolkit_get_contact_form', '', $email, $color);

		if($o != '')
			return $o;


		$o .= '<form class="bon-builder-contact-forms"><div class="contact-form-wrapper">';

        $o .= '<div class="contact-form-input">';
        $o .= '<label for="name">'.__('Your Name', 'bon-toolkit').'</label>';
        $o .= '<input type="text" value="" name="name" class="name required" placeholder="'.__('Name', 'bon').'" />';
        $o .= '<div class="contact-form-error">'.__('Please enter your name.','bon-toolkit').'</div>';
        $o .= '</div>';

        $o .= '<div class="contact-form-input">';
        $o .= '<label for="email-address">'.__('Email Address', 'bon-toolkit').'</label>';
        $o .= '<input type="email" value="" name="email" class="email-address required" placeholder="'.__('Email', 'bon').'" />';
        $o .= '<div class="contact-form-error">'.__('Please enter valid email address.','bon-toolkit').'</div>';
        $o .= '</div>';

        $o .= '<div class="contact-form-input">';
        $o .= '<label for="subject">'.__('Subject', 'bon-toolkit').'</label>';
        $o .= '<input type="text" value="" name="subject" class="subject" placeholder="'.__('Subject', 'bon').'" />';
        $o .= '</div>';

        $o .= '<div class="contact-form-input">';
        $o .= '<label for="messages">'.__('Your Messages', 'bon-toolkit').'</label>';
        $o .= '<textarea name="messages" class="messages required" placeholder="'.__('Messages', 'bon').'"></textarea>';
        $o .= '<div class="contact-form-error">'.__('Please enter your messages.','bon-toolkit').'</div>';
        $o .= '</div>';

        $o .= '<input type="hidden" name="receiver" value="'.$email.'" />';

        $o .= '<div class="contact-form-input">';
        $o .= '<button type="submit" class="contact-form-submit bon-toolkit-button round-corner flat '.$color.'" name="submit">'.__('Send Message','bon-toolkit').'</button>';
        $o .= '<span class="contact-form-ajax-loader"><img src="'.trailingslashit( BON_TOOLKIT_IMAGES ).'loader.gif" alt="loading..." /></span>';
        $o .= '</div>';

        $o .= '</div><div class="sending-result"><div class="green bon-toolkit-alert"></div></div></form>';

        return $o;

	}
}

function bon_toolkit_post_custom_bg() {

	if(!current_theme_supports( 'bon-custom-post-bg' )) {
		return;
	}

	$post_types = get_theme_support( 'bon-custom-post-bg' );

	$post_types = $post_types[0];

	if(current_theme_supports( 'bon-custom-post-bg' ) && is_array($post_types) && class_exists('BON_Metabox')) {

		$prefix = bon_toolkit_get_prefix();

		if(is_admin()) {

			$mb = new BON_Metabox();

			$fields = array(
				array(
					'label' => __('Background Image', 'bon-toolkit'),
					'desc' => __('Upload a custom background image for this post. Once image has been uploaded, click Insert into Post.', 'bon-toolkit'),
					'type' => 'image',
					'id' => $prefix . 'custom_bg_image',
				),

				array(
					'label' => __('Background Repeat', 'bon-toolkit'),
					'desc' => __('Select the preferred repeat for the uploaded image.', 'bon-toolkit'),
					'type' => 'select',
					'options' => array(
						'no-repeat' => __('No Repeat', 'bon-toolkit'),
						'repeat-x' => __('Repeat Horizontal', 'bon-toolkit'),
						'repeat-y' => __('Repeat Vertical', 'bon-toolkit'),
						'repeat' => __('Repeat', 'bon-toolkit'),
					),
					'id' => $prefix . 'custom_bg_repeat',
				),

				array(
					'label' => __('Background Position', 'bon-toolkit'),
					'desc' => __('Select the background position for the uploaded image.', 'bon-toolkit'),
					'type' => 'select',
					'options' => array(
						'left' => __('Left', 'bon-toolkit'),
						'right' => __('Right', 'bon-toolkit'),
						'center' => __('Center', 'bon-toolkit'),
					),
					'id' => $prefix . 'custom_bg_position',
				),

				array(
					'label' => __('Background Cover', 'bon-toolkit'),
					'desc' => __('If browser supported, the background image should cover the block.', 'bon-toolkit'),
					'type' => 'select',
					'options' => array(
						'true' => __('Yes', 'bon-toolkit'),
						'false' => __('No', 'bon-toolkit'),
					),
					'id' => $prefix . 'custom_bg_cover',
				),

				array(
					'label' => __('Background Color', 'bon-toolkit'),
					'desc' => __('Select a custom background color for this post.', 'bon-toolkit'),
					'type' => 'color',
					'id' => $prefix . 'custom_bg_color',
				),
			);

			$mb->create_box('bon-custom-background', __('Custom Background','bon'), $fields, $post_types, 'normal', 'high');
		}
	}
}

add_action( 'init', 'bon_toolkit_post_custom_bg', 20 );


function bon_toolkit_filter_custom_bg() {

	if(!current_theme_supports( 'bon-custom-post-bg' )) {
		return;
	}

	$post_types = get_theme_support( 'bon-custom-post-bg' );

	$post_types = $post_types[0];
	if(is_array($post_types) && current_theme_supports('bon-custom-post-bg')) {

		if( is_singular($post_types) ) {

			$prefix = bon_toolkit_get_prefix();

			$bg_image = get_post_meta( get_the_ID(), "{$prefix}custom_bg_image", true );
			$bg_image = wp_get_attachment_url($bg_image);
			$bg_repeat = get_post_meta( get_the_ID(), "{$prefix}custom_bg_repeat", true );
			$bg_color = get_post_meta( get_the_ID(), "{$prefix}custom_bg_color", true );
			$bg_position = get_post_meta( get_the_ID(), "{$prefix}custom_bg_position", true );
			$bg_cover = get_post_meta( get_the_ID(), "{$prefix}custom_bg_cover", true );
			


			if(empty($bg_image) && empty($bg_color)) {
				return;
			}

			$current_post_type = get_post_type();
			$id = get_the_ID();

			$selector = apply_filters( 'bon_toolkit_custom_bg_selector', '.custom-post-background' );

			$bg_attachment = 'scroll';
			$bg_cover_style = '';
			if('true' == $bg_cover) {
				$bg_attachment = 'fixed';
				$bg_cover_style = '-webkit-background-size: cover; -moz-background-size: cover; -o-background-size: cover; background-size: cover;';
			}
			$style = "<style type='text/css' id='custom-post-background-css'> .singular-{$current_post_type}-{$id} {$selector} { background: url({$bg_image}) {$bg_repeat} {$bg_position} {$bg_color} {$bg_attachment}; {$bg_cover_style} }</style>"; 
			
			echo $style;
		}
	}
}

add_action('wp_head', 'bon_toolkit_filter_custom_bg', 1000);

?>