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
			$return_data['value'] = 'Cannot send email to destination. No parameter receive form AJAX call.';	
			die ( json_encode($return_data) );
		}

		$name = esc_html( $_POST['name'] );

		if(empty($name)) {
			$return_data['value'] = __('Please enter your name.','bon-toolkit');
			die ( json_encode($return_data) );
		}

		$email = sanitize_email( $_POST['email'] );

		if(empty($email)){
			$return_data['value'] = 'Please enter a valid email address.';
			die ( json_encode($return_data) );		
		}


		$subject = esc_html( $_POST['subject'] );

		$messages = esc_textarea( $_POST['messages'] );

		if(empty($messages)){ 
			$return_data['value'] = 'Please enter your messages.';
			die ( json_encode($return_data) );				
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
?>