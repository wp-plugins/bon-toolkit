<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * BON_Toolkit_Quiz
 *
 * @package BON_Toolkit
 * @author Hermanto Lim
 * @since 1.0.0
 *
 */
class BON_Toolkit_Quiz {

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var string
	 */
	public $shortcode_tag = 'bt-quiz';

	function __construct() {

		add_action('init', array($this, 'init'));
		
		// pre process shortcode to make sure there is no extra p tag on the shortcode
		add_filter('the_content', array( &$this, 'process_shortcode'), 7);
		
	}

	function init() {
		
		$prefix = bon_toolkit_get_prefix();
		$this->prefix = $prefix;
		add_shortcode($this->shortcode_tag, array( $this, 'shortcode' ) );

		add_action('wp_ajax_'.$this->shortcode_tag, array( &$this, 'do_ajax') );
		add_action('wp_ajax_nopriv_'.$this->shortcode_tag, array( &$this, 'do_ajax') );
	}

	

	function do_ajax($post_id) {

		if ( function_exists( 'check_ajax_referer' ) ) {
				
			check_ajax_referer( 'quiz-submit', 'nonce' );
		
		} 

		 $post_id = str_replace('quiz_', '', $_POST['post_id']);

	 if(!is_numeric($post_id)) return false;

	    $quiz_meta = array();
	    $quiz = array();
	    $pattern = "#\[|\]\s+|[\]]#s";
	    $quiz_metas = get_post_meta($post_id, $this->prefix . 'quiz_settings', true);

	    $encode = null;

	        $i = 0;
	        foreach ($quiz_metas as $quiz_meta) {
	            if(empty($quiz_meta['question']) || empty($quiz_meta['options']) || empty($quiz_meta['answer'])) {
	               return false;
	            }
	            else {
	                $quiz[$i]['question'] = $quiz_meta['question'];
	                $quiz[$i]['opts'] = preg_split($pattern, $quiz_meta['options'], -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
	                $quiz[$i]['answer'] = $quiz_meta['answer'];
	                $i++;
	            }

	        }

	    $content = get_post($post_id);

	    global $bontoolkit;
	    $options = get_option($bontoolkit->option_name);

			$quiz_comment['perfect'] = ( isset($options['quiz_100']) ? $options['quiz_100'] : 'Perfect!' );
			$quiz_comment['excellent'] = ( isset($options['quiz_90']) ? $options['quiz_90'] : 'Excellent!' );
			$quiz_comment['good'] = ( isset($options['quiz_80']) ? $options['quiz_80'] : 'Good!' );
			$quiz_comment['average'] = ( isset($options['quiz_60']) ? $options['quiz_60'] : 'Average!' );
			$quiz_comment['bad'] = ( isset($options['quiz_40']) ? $options['quiz_40'] : 'Bad!' );
			$quiz_comment['poor'] = ( isset($options['quiz_30']) ? $options['quiz_30'] : 'Poor!' );
			$quiz_comment['worst'] = ( isset($options['quiz_0']) ? $options['quiz_0'] : 'Worst!' );

	    $return['quiz'] = $quiz;
	    $return['quiz_title'] = $content->post_title;
	    $return['quiz_content'] = $content->post_content;
	    $return['quiz_comment'] = $quiz_comment;

	    wp_send_json($return);
	    
	}

	function load_scripts() {

		if(!is_admin()) {

				wp_register_script( 'bon-toolkit-quiz' , trailingslashit(BON_TOOLKIT_JS) . 'quiz.js', array('jquery'), '1.0.0', true );
		    	wp_enqueue_script( 'bon-toolkit-quiz' );
		    	
		    if ( BON_TOOLKIT_USE_CSS ) {	
				wp_register_style( 'bon_toolkit_font_style', trailingslashit( BON_TOOLKIT_CSS ) . 'bt-social.css', false, '1.0.0' );
				wp_enqueue_style( 'bon_toolkit_font_style' );

				wp_register_style( 'bon-toolkit-quiz' , trailingslashit(BON_TOOLKIT_CSS) . 'quiz.css', false, '1.0.0' );
		    	wp_enqueue_style( 'bon-toolkit-quiz' );
		    }
		}
	    
	}

	function build($post_id) {
		global $bontoolkit;
		$output = '';

			if( is_singular('quiz') ) {
				$this->load_scripts();
			}
			$options = get_option($bontoolkit->option_name);
			//$content = get_post($post_id);
			$attachment_id = get_post_thumbnail_id( $post_id );
			$image_src = wp_get_attachment_image_src( $attachment_id, 'full');
			$image_src = $image_src[0];

			$output .= '<div class="bon-toolkit-quiz-container">';
			$output .= wp_nonce_field( 'quiz-submit','_quiz_nonce' . $post_id, true, false );
			$output .= '<div id="quiz_'.$post_id.'" class="quiz-wrapper bon-toolkit-quiz" data-thumbnail="'.$image_src.'"></div></div>';


		return $output;
	}

	function shortcode( $atts ) {
		$this->load_scripts();
	    extract( shortcode_atts( array(
	    	'id' => '',
	    ), $atts ) );

	    if($id) {
	    	return $this->build($id);
	    }
	    else {
	    	return __('Invalid Quiz ID','bon-toolkit');
	    }

	}

	function process_shortcode($content) {
	    global $shortcode_tags;

	    // Backup current registered shortcodes and clear them all out
	    $orig_shortcode_tags = $shortcode_tags;
	    remove_all_shortcodes();

	    add_shortcode($this->shortcode_tag, array($this, 'shortcode') );

	    // Do the shortcode (only the one above is registered)
	    $content = do_shortcode($content);

	    // Put the original shortcodes back
	    $shortcode_tags = $orig_shortcode_tags;

	    return $content;
	}


} // END class

new BON_Toolkit_Quiz(); 
?>