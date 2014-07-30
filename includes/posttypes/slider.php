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

		$name = __('Slider', 'bon-toolkit');
		$plural = __('Sliders', 'bon-toolkit');

		$cpt->create('Slider', array( 'menu_icon' => 'dashicons-slides', 'show_in_nav_menus' => false, 'exclude_from_search' => true, 'supports' => array('editor','title', 'page-attributes'), 'public' => false, 'menu_position' => 4 ), array(), $name, $plural );

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


/**
 * 
 * Defining new table column in All Quizzes View
 * 
 **/
if( !function_exists('bon_toolkit_slider_custom_columns') ) {

	function bon_toolkit_slider_custom_columns($columns){  

	        $columns = array(  
	            "cb" => "<input type=\"checkbox\" />",
	            "id" => __('Slide ID', 'bon-toolkit' ),  
	            "title" => __( 'Slide Title','bon-toolkit' ),
	            "date" => __( 'Date','bon-toolkit' )
	        );  
	  
	        return $columns;  
	}   

	add_filter("manage_edit-slider_columns", "bon_toolkit_slider_custom_columns");

}

/**
 * 
 * Adding output to new table column in All Quizzes View
 * 
 **/
if( !function_exists('bon_toolkit_slider_manage_columns') ) {
	
	function bon_toolkit_slider_manage_columns($name) {
		global $post;
		switch ($name) {
			case 'id':
				echo $post->ID;
				break;
		}
	}

	add_action('manage_posts_custom_column',  'bon_toolkit_slider_manage_columns');

}
?>