<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * BON_Toolkit_Poll
 *
 * @package BON_Toolkit
 * @author Hermanto Lim
 * @since 1.0.0
 *
 */
class BON_Toolkit_Poll {

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var string
	 */
	public $shortcode_tag = 'bt-poll';

	/**
	 * @var string
	 */
	public $slug = 'poll';

	/*
	 * Class Constructor
	 */
	function __construct() {

		add_action('init', array($this, 'init'));		
		add_filter('the_content', array( &$this, 'process_shortcode'), 7);
	}

	function init() {
		global $bon, $bontoolkit;

		$this->prefix = bon_toolkit_get_prefix();

		add_shortcode( $this->shortcode_tag, array( &$this, 'shortcode') );
			
		if(!is_admin() && is_singular('poll') ) {
			add_action('wp_enqueue_scripts', array( &$this, 'load_scripts') );
		} else {
			
			add_action('new_to_publish_poll', array( $this, 'setup') );		
			add_action('draft_to_publish_poll', array( $this, 'setup') );		
			add_action('pending_to_publish_poll', array( $this, 'setup') );

			
			add_action('wp_ajax_'.$this->shortcode_tag, array( &$this, 'do_ajax') );
			add_action('wp_ajax_nopriv_'.$this->shortcode_tag, array( &$this, 'do_ajax') );
		}
	}

	function load_scripts() {

		if ( BON_TOOLKIT_USE_CSS ) {
	    	wp_register_style( 'bon-toolkit-poll' , trailingslashit(BON_TOOLKIT_CSS) . 'poll.css', '', '1.0.0' );
	    	wp_enqueue_style( 'bon-toolkit-poll' );
		}
		
	}

	function setup($post_id) {

		global $post;

		$_POST += array("{$this->slug}_edit_nonce" => '');
	    if ( $this->slug != $_POST['post_type'] ) {
	        return;
	    }

	    if ( !current_user_can( 'edit_post', $post_id ) ) {
	        return;
	    }

	     if ( !wp_verify_nonce( $_POST["{$this->slug}_edit_nonce"],  plugin_basename( __FILE__ ) ) ) {
	        return;
	    }

	    if(!is_numeric($post_id)) return;

	    $options = $_REQUEST[ $this->prefix . 'poll_options'];
	    $votes = $_REQUEST[ $this->prefix . 'poll_votes'];
	    if($options) {
	    	if(!$votes) {
		    	$votes = array();
		    	foreach($options as $option) {
	    			$votes[$option['vote_options']] = 0;
		    	}
		    	add_post_meta($post_id, $this->prefix. 'poll_votes', $votes);
		    } else {
		    	foreach($options as $option) {
	    			$votes[$option['vote_options']] = 0;
		    	}
		    	update_post_meta($post_id, $this->prefix. 'poll_votes', $votes);
		    }
	    }
	}

	function do_ajax($post_id) {

		if ( function_exists( 'check_ajax_referer' ) ) {
				
			check_ajax_referer( 'poll-submit', 'nonce' );
		
		} 

	    if( isset($_POST['post_id']) && isset($_POST['option_id']) ) {
	        
	        $post_id = str_replace('poll_', '', $_POST['post_id'] );
	        $opt_id = stripslashes( $_POST['option_id'] );

	        echo $this->create_poll($post_id, $opt_id, 'update');
	       
	    } else {

	        $post_id = str_replace('poll_', '', $_POST['post_id']);

	        echo $this->create_poll($post_id, '', 'get');

	    }
	    exit;
	}

	function get_vote_option($post_id) {
		
		$return = array();

		$options = get_post_meta( $post_id, $this->prefix . 'poll_options', true);
		
		if(!$options) {
			return false;
		} else {
			foreach($options as $option) {
				$return[] = $option['vote_option'];
			}
		}
		return $return;
	}

	function load($post_id) {
    	    
		if(!is_numeric($post_id)) return;

			$output = '';
			$output .= '<div class="poll-container">';

			if( !isset($_COOKIE['entry_poll_'. $post_id]) ) {
				$output .= $this->get_form($post_id);
			}

			else {
				$output .= $this->get_result($post_id);
			}

			$output .= '</div>';

			return $output;
	}

	function create_poll($post_id, $opt_id = '', $action = 'get') {

	    if(!is_numeric($post_id)) return;
	    	
        	$options = $this->get_vote_option($post_id);

			
            switch($action) {
	         	
	         	case 'get':

	         		$votes = get_post_meta($post_id, $this->prefix . 'poll_votes', true);

	         		if($options) {
		            	if(!$votes) {
		            		foreach($options as $option) {

		            			$votes[$option] = 0;
		            		}

		            		add_post_meta($post_id,  $this->prefix . 'poll_votes', $votes);
		            	}
		            	
		            	return $this->get_form($post_id);
		            }

	         	break;  

	         	case 'update':

	         		if($options) {

	         			foreach($options as $option) {

		         			if( isset($_COOKIE['entry_poll_'. $post_id]) ) return $votes;

		         			if($opt_id) {
		         				$new_votes = array();
		         				$votes = get_post_meta($post_id, $this->prefix . 'poll_votes', true);
		         				if($votes) {
			         				foreach($votes as $key => $value) {
			         					
			         					if($key != $opt_id) {
			         						$new_votes[$key] = $value;
			         						if(!isset($votes[$opt_id])) {
			         							$new_votes[$opt_id] = 1;
			         						}
			         					}
			         					else {
			         						$value++;
			         						$new_votes[$key] = $value;
			         					}

			         				}
			         			}
			         			else {
							    	$new_votes = array();
							    	foreach($options as $option) {
						    			$new_votes[$option] = 0;
							    	}

							    	foreach($new_votes as $key => $value) {

			         					if($key != $opt_id) {
			         						$new_votes[$key] = $value;
			         					}
			         					else {
			         						$value++;
			         						$new_votes[$key] = $value;
			         					}
			         				}

			         			}
		         			}

			            	update_post_meta($post_id, $this->prefix . 'poll_votes', $new_votes);

			            	//setcookie('entry_poll_'. $post_id, $post_id, time()*20, '/');
			            	
			            	return $this->get_result($post_id);
	         			}
		            }

	         	break;
	        }
	          
	}

	function get_form($post_id) {

		$output = '';

			$votes = get_post_meta($post_id, $this->prefix . 'poll_votes', true);



			$output .= '<h4>'.get_the_title($post_id).'</h4>';
			$output .= '<div class="poll-content">';
			$output .= wpautop(wptexturize(get_post_field('post_content', $post_id)));
			$output .= '</div><hr/>';


		$options = $this->get_vote_option($post_id);
		if($options) {
			$output .= '<form id="poll_'.$post_id.'" class="bon-toolkit-poll" method="post" action="poll-submit">';
			foreach ($options as $option) {
				$output .= '<div class="options"><label><input type="radio" name="poll_option_'.$post_id.'" value="'.$option.'" />'.$option.'</label></div>';
			}
			$output .= wp_nonce_field( 'poll-submit','_poll_nonce' . $post_id, true, false );
			$output .= '<hr/><div class="options-submit"><button type="submit" class="poll-button">'.__('Vote','bon-toolkit').'</button></div>';
			$output .= '</form>';
		}

		return $output;
	}

	function get_result($post_id) {

		$output = '';
		$options = $this->get_vote_option($post_id);
		$votes = get_post_meta($post_id, $this->prefix .'poll_votes', true);


			
		$output .= '<h4>'.get_the_title($post_id).'</h4>';
		$output .= '<div class="poll-content">';
		$output .= wpautop(wptexturize(get_post_field('post_content', $post_id)));
		$output .= '</div><hr/>';
		


		if($options && $votes) {
			$output .= '<div class="poll-result">';
			$count = $this->count_votes($votes, $options);
			
			$i = 1;
			foreach($options as $option) {
				$output .= '<div class="poll-option poll-result-'.$i.' '.(isset($votes[$option]) && $votes[$option] > 0 ? '' : 'no-vote').'">';
				$output .= '<div class="poll-details">';
				$output .= '<span class="option-label">'.$option.'</span>';
				$output .= '</div>';
				$output .= '<div class="poll-progress"><div class="bar" style="width: '.$count['percents'][$option].'"><span>'.$count['percents'][$option].'</span></div></div>';
				$output .= '</div>';

				$i++;

				if($i >= 6 ) { $i = 1; }
			}
			$output .= '<hr/><div class="votes-amount">';
			$output .= '<span>'. sprintf(_n('Total Vote: 1','Total Vote: %d', $count['total'], 'bon-toolkit' ), $count['total']).'</span>';
			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}

	function shortcode( $atts ) {

		$this->load_scripts();
		
	    extract( shortcode_atts( array(
	    	'id' => '',
	    ), $atts ) );

	    if($id) {
	    	return $this->load($id);
	    }
	    else {
	    	return '<p>'.__('Invalid Poll ID','bon-toolkit').'</p>';
	    }

	}

	function count_votes($votes = array(), $options = array()) {
		
		$total = 0;
		$result = array();
		foreach($votes as $vote) {
			$total += $vote;
		}

		foreach($options as $option) {
			if( isset($votes[$option]) && $votes[$option] != 0 ) {

				$percent = ($votes[$option] / $total) * 100;
				$result['percents'][$option] = str_replace(".0", "", number_format ($percent, 1, ".", "")) . '%';
			}
			else {
				$percent = '0%';
				$result['percents'][$option] = $percent;
			}
			
		}
		$result['total'] = $total;
		return $result;
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

new BON_Toolkit_Poll();
?>