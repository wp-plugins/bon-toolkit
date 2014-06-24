<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * BON_Toolkit_Likes
 *
 * @package BON_Toolkit
 * @author Hermanto Lim
 * @since 1.0.0
 *
 */
class BON_Toolkit_Likes {

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var string
	 */
	public $shortcode_tag = 'bt-likes';

	/*
	 * Class Constructor
	 */
	function __construct() {


		add_action('init', array($this, 'init'));		
		add_filter('the_content', array( &$this, 'process_shortcode'), 7);
	}

	function init() {
		global $bon, $bontoolkit;

		$prefix = bon_toolkit_get_prefix();
		$this->prefix = $prefix;

		add_shortcode( $this->shortcode_tag, array( &$this, 'shortcode') );
		
		add_action( 'publish_post', array( &$this, 'setup') );
		add_action( 'wp_ajax_'.$this->shortcode_tag, array( &$this, 'do_ajax') );
		add_action( 'wp_ajax_nopriv_'.$this->shortcode_tag, array( &$this, 'do_ajax') );
		
	}


	function setup($post_id) {

	    if(!is_numeric($post_id)) return;

    	add_post_meta($post_id, '_bt_likes', '0', true);

	}

	function do_ajax($post_id) {

		if ( function_exists( 'check_ajax_referer' ) ) {				
			check_ajax_referer( 'likes-submit', 'nonce' );
		}
		
	    if( isset($_POST['likes_id']) ) {
        
	        // Click event. Get and Update Count
	        $post_id = str_replace('bt-likes-', '', $_POST['likes_id']);
	        
	        echo $this->create($post_id, 'update');

	    } else {
	        // AJAXing data in. Get Count

	        $post_id = str_replace('bt-likes-', '', $_POST['post_id']);

	        echo $this->create($post_id, 'get');

	    }
	    
	    exit;
	}


	function load() {
    	    
		global $post;
		
	    $output = $this->create($post->ID);

	    $class = 'bon-toolkit-likes';
	    $title = __('Like this', 'bon');
	    if( isset($_COOKIE['bt_likes_'. $post->ID]) ){
	        $class = 'bon-toolkit-likes active';
	        $title = __('You already like this', 'bon-toolkit');
	    }
	    $content = '<div class="bon-toolkit-likes-container">';
	    $content .= wp_nonce_field( 'likes-submit','_likes_nonce' . $post->ID, true, false );
	    $content .= '<a href="#" class="'. $class .'" id="bt-likes-'. $post->ID .'" title="'. $title .'">';
	    $content .= $output;
	    $content .= '</a></div>';

	    return $content;

	}

	function create($post_id, $action = 'get') {

	    if(!is_numeric($post_id)) return;
    	

	    switch($action) {
	    
	        case 'get':
	            $likes = get_post_meta($post_id, '_bt_likes', true);
	            if( !$likes ){

	                $likes = 0;
	                add_post_meta($post_id, '_bt_likes', $likes, true);
	            }

	            return '<i class="bt-icon-heart"></i><span class="bt-likes-count">'. $likes .'</span>';

	        break;
	            
	        case 'update':
	        	

	            $likes = get_post_meta($post_id, '_bt_likes', true);
	            if( isset($_COOKIE['bt_likes_'. $post_id]) ) return $likes;
	            
	            $likes++;
	            update_post_meta($post_id, '_bt_likes', $likes);
	            setcookie('bt_likes_'. $post_id, $post_id, time()*20, '/');
	            
	            return '<i class="bt-icon-heart"></i><span class="bt-likes-count">'. $likes .'</span>';

	        break;
	    
	    }
	          
	}

	function do_likes() {
	   echo $this->load();
	}

	function shortcode( $atts ) {

	     extract( shortcode_atts( array(), $atts ) );
		 return $this->load();

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

}

new BON_Toolkit_Likes();
?>