<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * ======================================================================================================
 *
 * Creating Custom Post Type for Portfolio
 * Post type crated using global $bon superobject $bon->cpt Custom Post Type Instance
 * @since 1.0
 * @return void
 *
 * ======================================================================================================
 */

if( !function_exists('bon_toolkit_setup_portfolio_post_type') ) {

	function bon_toolkit_setup_portfolio_post_type() {

		global $bon;

		
		$prefix = bon_toolkit_get_prefix();


		$cpt = $bon->cpt();


		$cpt->create('Portfolio', array('supports' => array('editor','title', 'page-attributes', 'thumbnail','excerpt','comments','custom-fields'), 'menu_position' => 4 ));
	    
		$cpt->add_taxonomy("Portfolio Category", array('hierarchical' => true, 'label' => 'Categories', 'labels' => array('menu_name' => 'Categories') ) );

		$cpt->add_taxonomy("Portfolio Tag", array('hierarchical' => false, 'label' => 'Tags', 'labels' => array('menu_name' => 'Tags') ) );

		$meta_fields = array(

			array(
				'label' => __('Date', 'framework'),
				'desc' => __('When is the project created', 'framework'),
				'type' => 'date',
				'id' => $prefix . 'portfolio_date',
			),

			array(
				'label' => __('Url', 'framework'),
				'desc' => __('Url to the implemented final project', 'framework'),
				'type' => 'text',
				'id' => $prefix . 'portfolio_url',
			),

			array(
				'label' => __('Client', 'framework'),
				'desc' => __('Who is your client (eq. Jonh doe or Organization Name)', 'framework'),
				'type' => 'text',
				'id' => $prefix . 'portfolio_client',
			),

			array(
				'label' => __('Gallery', 'framework'),
				'desc' => __('Project Images Gallery', 'framework'),
				'type' => 'gallery',
				'id' => $prefix . 'portfolio_gallery',
			),
			
		);

		$meta_fields = apply_filters( 'bon_toolkit_filter_portfolio_meta', $meta_fields );

		if(is_array($meta_fields) && !empty($meta_fields)) {
			$cpt->add_meta_box(   
			    'portfolio-options',
			    'Portfolio Options',
			    $meta_fields  
			);
		}

	}

	add_action('init','bon_toolkit_setup_portfolio_post_type', 1);

}

/*
 * Adding Portfolio Icon to WordPress Menu
 */
if( !function_exists('bon_toolkit_setup_portfolio_icon') ) {
	
	function bon_toolkit_setup_portfolio_icon($input) {
		
		$input['portfolio'] = trailingslashit( BON_TOOLKIT_IMAGES ) . 'icon-portfolio.png'; 

		return $input;
	}
	
	add_filter('bon_toolkit_filter_post_type_icon', 'bon_toolkit_setup_portfolio_icon');

}
?>