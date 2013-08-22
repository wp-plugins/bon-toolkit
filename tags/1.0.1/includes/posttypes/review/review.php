<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * ======================================================================================================
 *
 * Creating Custom Post Type for Review
 * Post type crated using global $bon superobject $bon->cpt Custom Post Type Instance
 * @since 1.0
 * @return void
 *
 * ======================================================================================================
 */

if( !function_exists('bon_toolkit_setup_review_post_type') ) {
	function bon_toolkit_setup_review_post_type() {

		global $bon;

		$prefix = bon_toolkit_get_prefix();

		$cpt = $bon->cpt();

		$cpt->create('Review', array('supports' => array('editor','title', 'thumbnail','excerpt','comments','custom-fields', 'post-formats'), 'menu_position' => 4 ));
	    
		$cpt->add_taxonomy("Review Category", array('hierarchical' => true, 'label' => 'Categories', 'labels' => array('menu_name' => 'Categories') ) );

		$cpt->add_taxonomy("Review Tag", array('hierarchical' => false, 'label' => 'Tags', 'labels' => array('menu_name' => 'Tags') ) );

		$meta_fields = array(

			array( 
				'label'	=> __('Review Settings', 'bon-toolkit'),
				'desc'	=> __('Setup the review criteria.', 'bon-toolkit'), 
				'id'	=> $prefix.'review_options',
				'type'	=> 'repeatable',
				'sanitizer' => array( 
					'criteria' => 'sanitize_text_field',
				),
				'repeatable_fields' => array(
					
					'criteria' => array(
						'label' => __('Review Criteria','bon-toolkit'),
						'id' => 'criteria',
						'type' => 'text'
					),

					'rating' => array(
						'label' => __('Review Rating','bon-toolkit'),
						'id' => 'rating',
						'type' => 'slider',
						'min'   => '1',  
						'max'   => '10',  
						'step'  => '1',
					),

				)
			),

			array( 
				'label'	=> __('Review Pros', 'bon-toolkit'),
				'desc'	=> __('The review pros. Tips: You can wrap the item in list. eq <br />&lt;ul&gt;<br />&lt;li&gt;Pros 1&lt;/li&gt;<br />&lt;li&gt;Pros 2&lt;/li&gt; <br /> &lt;/ul&gt;', 'bon-toolkit'), 
				'id'	=> $prefix.'review_pros',
				'type'	=> 'textarea',
				
			),

			array( 
				'label'	=> __('Review Cons', 'bon-toolkit'),
				'desc'	=> __('The review cons.  Tips: You can wrap the item in list. eq <br />&lt;ul&gt;<br />&lt;li&gt;Pros 1&lt;/li&gt;<br />&lt;li&gt;Pros 2&lt;/li&gt; <br /> &lt;/ul&gt;', 'bon-toolkit'), 
				'id'	=> $prefix.'review_cons',
				'type'	=> 'textarea',
				
			),

		);

		$meta_fields = apply_filters( 'bon_toolkit_filter_review_meta', $meta_fields );

		if(is_array($meta_fields) && !empty($meta_fields)) {
			$cpt->add_meta_box(   
			    'review-options',
			    'Review Options',
			    $meta_fields  
			);
		}

	}
	add_action('init','bon_toolkit_setup_review_post_type', 1);
}

/*
 * Adding Review Icon for WordPress Menu
 */
if( !function_exists('bon_toolkit_setup_review_icon') ) {

	function bon_toolkit_setup_review_icon($input) {
		
		$input['review'] = trailingslashit( BON_TOOLKIT_IMAGES ) . 'icon-review.png'; 

		return $input;
	}

	add_filter('bon_toolkit_filter_post_type_icon', 'bon_toolkit_setup_review_icon');

}

?>