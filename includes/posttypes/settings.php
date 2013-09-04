<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * =====================================================================================================
 *
 * Styling for the custom post type icon
 * Not using Custom Post Type 'menu_icon' args due unsupported event state
 * @since 1.0
 * @return string
 *
 * ======================================================================================================
 */
add_action( 'admin_head', 'bon_toolkit_setup_post_type_icons' ); // custom icon for post type
function bon_toolkit_setup_post_type_icons() {


	$post_type_menu = array();

	$post_type_menu = apply_filters( 'bon_toolkit_filter_post_type_icon', $post_type_menu );

	$output = '';
	

	if(is_array($post_type_menu) && !empty($post_type_menu)) {
		$output .= '<style type="text/css" media="screen">';
		foreach($post_type_menu as $key => $value) {
			$output .= "#menu-posts-$key .wp-menu-image { background: url($value) no-repeat 6px -18px !important; }";
			$output .= "#menu-posts-$key:hover .wp-menu-image, 
						#menu-posts-$key.wp-has-current-submenu .wp-menu-image 
						{ background-position: 6px 6px !important; }";
		}
		$output .= '</style>';
	}
	

	echo $output;

} // end shandora_slider_icons
?>