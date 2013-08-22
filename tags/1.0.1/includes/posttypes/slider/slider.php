<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * ======================================================================================================
 *
 * Creating Custom Post Type for Slider
 * Post type crated using global $bon superobject $bon->cpt Custom Post Type Instance
 * @since 1.0
 * @return void
 *
 * ======================================================================================================
 */

if( !function_exists('bon_toolkit_setup_slider_post_type') ) {
	
	function bon_toolkit_setup_slider_post_type() {

		global $bon;

		$prefix = bon_toolkit_get_prefix();

		$cpt = $bon->cpt();

		$cpt->create('Slider', array('supports' => array('editor','title', 'page-attributes'), 'menu_position' => 4 ));

		$meta_fields = array(

			array( 
				'label'	=> __('Slider Image','bon-toolkit'),
				'id'	=> $prefix.'slider_image', 
				'type'	=> 'image',
				'desc'	=> __('The Slider Image.', 'bon-toolkit')					
			),
			
			array( 
				'label'	=> __('Sub Title / Secondary Title','bon-toolkit'),
				'id'	=> $prefix.'slider_subtitle', 
				'type'	=> 'text',
				'desc'	=> __('The Secondary title below the Primary Title.', 'bon-toolkit')					
			),

			array(
				'label'	=> __('Url / Link to','bon-toolkit'),
				'id'	=> $prefix.'slider_linkto', 
				'type'	=> 'text',
				'desc'	=> __('Read more target', 'bon-toolkit')					
			),

			array(
				'label'	=> __('Caption Position'),
				'id'	=> $prefix.'slider_position', 
				'type'	=> 'select',
				'options' => array(
					'' => 'Left',
					'caption-right' => 'Right'
				),
				'desc'	=> __('The position of the titles and the contents', 'bon-toolkit')					
			),

		);

		$meta_fields = apply_filters( 'bon_toolkit_filter_slider_meta', $meta_fields );

		if(is_array($meta_fields) && !empty($meta_fields)) {
			$cpt->add_meta_box(   
			    'slider-options',
			    'Slider Options',
			    $meta_fields  
			);
		}

	}

	add_action('init','bon_toolkit_setup_slider_post_type', 1);
	
}

/*
 * Adding Slider Icon to WordPress Menu
 */
if( !function_exists('bon_toolkit_setup_slider_icon') ) {
	
	function bon_toolkit_setup_slider_icon($input) {
	
		$input['slider'] = trailingslashit( BON_TOOLKIT_IMAGES ) . 'icon-slide.png'; 

		return $input;
	}

	add_filter('bon_toolkit_filter_post_type_icon', 'bon_toolkit_setup_slider_icon');

}

?>