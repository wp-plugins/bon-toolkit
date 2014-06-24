<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * ======================================================================================================
 *
 * Creating Custom Post Type for Polling System
 * Post type crated using global $bon superobject $bon->cpt Custom Post Type Instance
 * @since 1.0
 * @return void
 *
 * ======================================================================================================
 */
if( !function_exists('bon_toolkit_setup_poll_post_type') ) {

	function bon_toolkit_setup_poll_post_type() {

		global $bon;

		
		$prefix = bon_toolkit_get_prefix();
	
		$cpt = $bon->cpt();

		$name = __('Poll', 'bon-toolkit');
		$plural = __('Polls', 'bon-toolkit');

		$cpt->create('Poll', array( 'menu_icon' => 'dashicons-list-view', 'show_in_nav_menus' => false, 'exclude_from_search' => true, 'supports' => array('editor', 'title', 'thumbnail'), 'menu_position' => 20 ), array(), $name, $plural );

		$meta_fields = array(
			array( 
				'label'	=> __('Poll Settings', 'bon-toolkit'),
				'desc'	=> __('Setup the voting options for user.', 'bon-toolkit'), 
				'id'	=> $prefix.'poll_options',
				'type'	=> 'repeatable',
				'sanitizer' => array( 
					'vote_options' => 'sanitize_text_field',
				),
				'repeatable_fields' => array(
					'vote_options' => array(
						'label' => __('Vote Option','bon-toolkit'),
						'id' => 'vote_option',
						'type' => 'text'
					),

				)
			),

		);


		$meta_fields = apply_filters( 'bon_toolkit_filter_poll_meta', $meta_fields );

		if(is_array($meta_fields) && !empty($meta_fields)) {
			$cpt->add_meta_box(   
			    'polling-options',
			    'Polling Options',
			    $meta_fields  
			);
		}

		$shortcode_meta = array(
			'type' => 'code',
			'id' => $prefix . 'poll_display_info',
			'label' => '',
		);


	}

	add_action('init','bon_toolkit_setup_poll_post_type', 1);

}

/**
 * 
 * The metabox info output
 *
 **/
if( !function_exists('bon_toolkit_poll_metabox_info') ) {

	function bon_toolkit_poll_metabox_info() {
		global $post;
		echo '<p><strong>'.__('Shortcode','bon-toolkit').'</strong>:<br />';
		echo '<code>[bt-poll id="'.$post->ID.'"]</code>';
	}

}

/**
 * 
 * Adding shortcode and function info metabox in the right side of the poll post editing
 *
 **/
if( !function_exists('bon_toolkit_add_poll_metabox') ) {

	function bon_toolkit_add_poll_metabox() {
		add_meta_box('poll-metabox-display-info', 'Poll Display Info', 'bon_toolkit_poll_metabox_info', 'poll', 'side', 'core');
	}

	add_action('admin_init', 'bon_toolkit_add_poll_metabox');

}


/**
 * 
 * Defining new table column in All Polls View
 * 
 **/
if( !function_exists('bon_toolkit_poll_custom_columns') ) {

	function bon_toolkit_poll_custom_columns($columns){  

	        $columns = array(  
	            "cb" => "<input type=\"checkbox\" />",  
	            "title" => __( 'Poll Title','bon-toolkit' ),
	            "poll_shortcode" => __( 'Poll Shortcode','bon-toolkit' ),
	            "date" => __( 'Date','bon-toolkit' )
	        );  
	  
	        return $columns;  
	}   

	add_filter("manage_edit-poll_columns", "bon_toolkit_poll_custom_columns");

}

/**
 * 
 * Adding output to new table column in All Polls View
 * 
 **/
if( !function_exists('bon_toolkit_poll_manage_columns') ) {
	
	function bon_toolkit_poll_manage_columns($name) {
		global $post;
		switch ($name) {
			case 'poll_shortcode':
				echo '<code>[bt-poll id="'.$post->ID.'"]</code>';
				break;
		}
	}
	add_action('manage_posts_custom_column',  'bon_toolkit_poll_manage_columns');

}

?>