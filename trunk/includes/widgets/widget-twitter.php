<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Twitter
 *
 *
 *
 * @author		Hermanto Lim
 * @copyright	Copyright (c) Hermanto Lim
 * @link		http://bonfirelab.com
 * @since		Version 1.0
 * @package 	Bon Toolkit
 * @category 	Widgets
 *
 *
*/

add_action( 'widgets_init', 'load_bon_toolkit_twitter_widget' );

function load_bon_toolkit_twitter_widget() {
	register_widget( 'Bon_Toolkit_Widget_Twitter' );
}


// Widget class.
class Bon_Toolkit_Widget_Twitter extends WP_Widget {

	
	function __construct() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bon-toolkit-twitter-widget', 'description' => __('A widget that displays your latest tweet.', 'bon-toolkit') );

		/* Widget control settings. */
		$control_ops = array(  );

		/* Create the widget. */
		$this->WP_Widget( 'bon-toolkit-twitter', BON_TOOLKIT_PREFIX . __(' Twitter Widget', 'bon-toolkit'), $widget_ops, $control_ops );
	}


    /**
     * Encode single quotes in your tweets
     */
    function encode_tweet($text) {
            $text = mb_convert_encoding( $text, "HTML-ENTITIES", "UTF-8");
            return $text;
    }

    function profile_image($img, $user) {
    	$o = '<img src="'.$img.'" alt="'.$user.'" />';
    	return $o;  
    }

	function widget( $args, $instance ) {

		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		
		$twitter_username = $instance['username'];
		$twitter_postcount = $instance['postcount'];
		$tweettext = $instance['tweettext'];
		
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

			$twitter_args = array(
				'username' => $twitter_username,
				'count' => $twitter_postcount,
				'tweettext' => $tweettext,
			);
		?>

            <div class="twitter-widget clear">
	           <?php bon_toolkit_twitter($twitter_args); ?>
			</div> 
		<?php 
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['postcount'] = strip_tags( $new_instance['postcount'] );
		$instance['tweettext'] = strip_tags( $new_instance['tweettext'] );

		/* No need to strip tags for.. */

		return $instance;
	}
	
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => 'Latest Tweets',
			'username' => 'envato',
			'postcount' => '5',
			'tweettext' => 'Follow on Twitter',
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>


			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'bon-toolkit') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<!-- Username: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e('Twitter Username', 'bon-toolkit') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" />
		</p>
		
		<!-- Postcount: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'postcount' ); ?>"><?php _e('Number of tweets (max 20)', 'bon-toolkit') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'postcount' ); ?>" name="<?php echo $this->get_field_name( 'postcount' ); ?>" value="<?php echo $instance['postcount']; ?>" />
		</p>
		
		<!-- Tweettext: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'tweettext' ); ?>"><?php _e('Follow Text e.g. Follow me on Twitter', 'bon-toolkit') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'tweettext' ); ?>" name="<?php echo $this->get_field_name( 'tweettext' ); ?>" value="<?php echo $instance['tweettext']; ?>" />
		</p>
		
	<?php
	}
}

?>