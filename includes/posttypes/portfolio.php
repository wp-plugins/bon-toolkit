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

		$name = __('Portfolio', 'bon-toolkit');
		$plural = __('Portfolios', 'bon-toolkit');

		$cpt->create('Portfolio', array( 'has_archive' => true, 'menu_icon' => 'dashicons-portfolio', 'supports' => array('editor','title', 'page-attributes', 'thumbnail','excerpt','comments','custom-fields'), 'menu_position' => 4 ), array(), $name, $plural );
	    
		$cpt->add_taxonomy("Portfolio Category", array('hierarchical' => true, 'label' => __('Portfolio Categories','bon-toolkit') , 'labels' => array('menu_name' => __('Categories', 'bon-toolkit') ) ) );

		$cpt->add_taxonomy("Portfolio Tag", array('hierarchical' => false, 'label' =>  __('Portfolio Tags','bon-toolkit'), 'labels' => array('menu_name' => __('Tags','bon-toolkit') ) ) );

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

?>