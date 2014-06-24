<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * ======================================================================================================
 *
 * Creating Custom Post Type for Quiz
 * Post type crated using global $bon superobject $bon->cpt Custom Post Type Instance
 * @since 1.0
 * @return void
 *
 * ======================================================================================================
 */

if( !function_exists('bon_toolkit_setup_quiz_post_type') ) {

	function bon_toolkit_setup_quiz_post_type() {

		global $bon;

		$prefix = bon_toolkit_get_prefix();

		$cpt = $bon->cpt();
		
		$name = __('Quiz', 'bon-toolkit');
		$plural = __('Quizzes','bon-toolkit');
		
		$cpt->create('Quiz', array( 'menu_icon' => 'dashicons-clipboard', 'exclude_from_search' => true, 'show_in_nav_menus' => false, 'supports' => array('editor', 'title', 'thumbnail'), 'menu_position' => 20 ), array(), $name, $plural );

		$meta_fields = array(

			array(
					'label' => __('Quiz Settings','bon-toolkit'),
					'id' => $prefix.'quiz_settings',
					'type' => 'repeatable',
					'sanitizer' => array( 
						'question' => 'sanitize_text_field',
						'answer' => 'sanitize_text_field',
						'options' => 'sanitize_textarea',
					),
					'repeatable_fields' => array(
						
						'question' => array(
							'label' => __('Question','bon-toolkit'),
							'id' => 'question',
							'type' => 'text'
						),

						'answer' => array(
							'label' => __('Answer','bon-toolkit'),
							'id' => 'answer',
							'type' => 'text',
							'desc' => __('Define the correct answer by inputting the index of the options. eq: if your answer is in the option number 2 then put 2 inside the textbox.','bon-toolkit')
						),

						'options' => array(
							'label' => __('Option Set','bon-toolkit'),
							'id' => 'options',
							'type' => 'textarea',
							'desc' => __('Separate each option in square bracket. eq: [Option 1] [Option 2] [Option 3]','bon-toolkit')
						),

					)
			),
			
		);

		$meta_fields = apply_filters( 'bon_toolkit_filter_quiz_meta', $meta_fields );

		if(is_array($meta_fields) && !empty($meta_fields)) {
			$cpt->add_meta_box(   
			    'quiz-options',
			    'Quiz Options',
			    $meta_fields  
			);
		}
		

	}

	add_action('init','bon_toolkit_setup_quiz_post_type', 1);
}

/**
 * 
 * The metabox info output
 *
 **/
if( !function_exists('bon_toolkit_quiz_metabox_info') ) {

	function bon_toolkit_quiz_metabox_info() {
		global $post;
		echo '<p><strong>'.__('Shortcode','bon-toolkit').'</strong>:<br />';
		echo '<code>[bt-quiz id="'.$post->ID.'"]</code>';
	}

}

/**
 * 
 * Adding shortcode and function info metabox in the right side of the quiz post editing
 *
 **/
if( !function_exists('bon_toolkit_add_quiz_metabox') ) {

	function bon_toolkit_add_quiz_metabox() {
		add_meta_box('quiz-metabox-display-info', 'Quiz Display Info', 'bon_toolkit_quiz_metabox_info', 'quiz', 'side', 'core');
	}

	add_action('admin_init', 'bon_toolkit_add_quiz_metabox');

}


/**
 * 
 * Defining new table column in All Quizzes View
 * 
 **/
if( !function_exists('bon_toolkit_quiz_custom_columns') ) {

	function bon_toolkit_quiz_custom_columns($columns){  

	        $columns = array(  
	            "cb" => "<input type=\"checkbox\" />",  
	            "title" => __( 'Quiz Title','bon-toolkit' ),
	            "quiz_shortcode" => __( 'Quiz Shortcode','bon-toolkit' ),
	            "date" => __( 'Date','bon-toolkit' )
	        );  
	  
	        return $columns;  
	}   

	add_filter("manage_edit-quiz_columns", "bon_toolkit_quiz_custom_columns");

}

/**
 * 
 * Adding output to new table column in All Quizzes View
 * 
 **/
if( !function_exists('bon_toolkit_quiz_manage_columns') ) {
	
	function bon_toolkit_quiz_manage_columns($name) {
		global $post;
		switch ($name) {
			case 'quiz_shortcode':
				echo '<code>[bt-quiz id="'.$post->ID.'"]</code>';
				break;
		}
	}

	add_action('manage_posts_custom_column',  'bon_toolkit_quiz_manage_columns');

}

?>