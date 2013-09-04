<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * ======================================================================================================
 *
 * Creating Custom Post Type for Testimonial
 * Post type crated using global $bon superobject $bon->cpt Custom Post Type Instance
 * @since 1.0
 * @return void
 *
 * ======================================================================================================
 */

if( !function_exists('bon_toolkit_setup_testimonial_post_type') ) {

	function bon_toolkit_setup_testimonial_post_type() {

		global $bon;

		$prefix = bon_toolkit_get_prefix();

		$cpt = $bon->cpt();

		$cpt->create('Testimonial', array( 'show_in_nav_menus' => false, 'supports' => array('editor','title'), 'menu_position' => 20 ));

		$meta_fields = array(

			array( 
				'label'	=> __('Author Photo','bon-toolkit'),
				'id'	=> $prefix.'testi_image', 
				'type'	=> 'image',
				'desc'	=> __('The image for the author who give the testimonial.', 'bon-toolkit')					
			),
			
			array( 
				'label'	=> __('Author Name','bon-toolkit'),
				'id'	=> $prefix.'testi_name', 
				'type'	=> 'text',
				'desc'	=> __('The testimonial author\'s name', 'bon-toolkit')					
			),

			array(
				'label'	=> __('Author Url','bon-toolkit'),
				'id'	=> $prefix.'testi_link', 
				'type'	=> 'text',
				'desc'	=> __('Link to Author', 'bon-toolkit')					
			),

			array(
				'label'	=> __('Author Position'),
				'id'	=> $prefix.'testi_author_position', 
				'type'	=> 'text',
				'desc'	=> __('The author position. eq. CEO', 'bon-toolkit')					
			),

		);

		$meta_fields = apply_filters( 'bon_toolkit_filter_testimonial_meta', $meta_fields );

		if(is_array($meta_fields) && !empty($meta_fields)) {
			$cpt->add_meta_box(   
			    'testimonial-options',
			    'Testimonial Options',
			    $meta_fields  
			);
		}

	}

	add_action('init','bon_toolkit_setup_testimonial_post_type', 1);

}

/*
 * Setup Testimonial Icon for WordPress Menu
 */
if( !function_exists('bon_toolkit_setup_testimonial_icon') ) {

	function bon_toolkit_setup_testimonial_icon($input) {
	
		$input['testimonial'] = trailingslashit( BON_TOOLKIT_IMAGES ) . 'icon-testi.png'; 

		return $input;
	}

	add_filter('bon_toolkit_filter_post_type_icon', 'bon_toolkit_setup_testimonial_icon');

}

?>