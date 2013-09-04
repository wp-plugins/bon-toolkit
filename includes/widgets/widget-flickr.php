<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Flickr
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

add_action( 'widgets_init', 'load_bon_toolkit_flickr_widget' );

function load_bon_toolkit_flickr_widget() {
	register_widget( 'Bon_Toolkit_Widget_Flickr' );
}

class Bon_Toolkit_Widget_Flickr extends WP_Widget {
	
	function __construct() {
	    $widget_ops = array( 'classname' => 'bon-toolkit-flickr-widget', 'description' => __('Grab your latest Flickr photos', 'bon-toolkit') );
		$control_ops = array();
		$this->WP_Widget('bon-toolkit-flickr', BON_TOOLKIT_PREFIX . ' ' . __('Flickr Widget', 'bon-toolkit'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
 		extract($args);

		$flickr_title = apply_filters('widget_title', $instance['title']);
		$flickr_id = $instance['flickr_id'];
		$flickr_count = $instance['flickr_count'];

		echo $before_widget; ?>
		
			<?php if ( $flickr_title ) echo $before_title . $flickr_title . $after_title; ?>
					
			
		<div class="flickr-widget clear">
			<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=<?php echo $flickr_count; ?>&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user=<?php echo $flickr_id; ?>"></script>
		</div>					
			
			<?php
		echo $after_widget;	
  }

	// Updating the widget
	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title']);
		$instance['flickr_id'] = strip_tags( $new_instance['flickr_id']);
		$instance['flickr_count'] = strip_tags( $new_instance['flickr_count']);

		return $instance;
	}

	function form( $instance ) {
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','bon-toolkit'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if(isset($instance['title'])) echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('flickr_id'); ?>"><?php _e('Your Flickr User ID:','bon-toolkit'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('flickr_id'); ?>" name="<?php echo $this->get_field_name('flickr_id'); ?>" value="<?php if(isset($instance['flickr_id'])) echo $instance['flickr_id']; ?>" />
	 		<small>Don't know your ID? Head on over to <a href="http://idgettr.com">idgettr</a> to find it.</small>
	 	</p>

	 	<p>
			<label for="<?php echo $this->get_field_id('flickr_count'); ?>"><?php _e('No. of Photos:','bon-toolkit'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id('flickr_count'); ?>" name="<?php echo $this->get_field_name('flickr_count'); ?>" value="<?php if(isset($instance['flickr_count'])) echo $instance['flickr_count']; ?>" />
		</p>
		
		<?php
	}
}

?>