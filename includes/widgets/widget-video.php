<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Video
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

add_action( 'widgets_init', 'load_bon_toolkit_video_widget' );

function load_bon_toolkit_video_widget() {
	register_widget( 'Bon_Toolkit_Widget_Video' );
}

/*-----------------------------------------------------------------------------------*/
/*  Widget class
/*-----------------------------------------------------------------------------------*/
class Bon_Toolkit_Widget_Video extends WP_Widget {

	

/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
function __construct() {

	$widget_ops = array( 'classname' => 'bon-toolkit-video-widget', 'description' => __('Show Video', 'bon-toolkit') );
	$control_ops = array();
	$this->WP_Widget('bon-toolkit-video', BON_TOOLKIT_PREFIX . __(' Video Widget', 'bon-toolkit'), $widget_ops, $control_ops);

}



function widget( $args, $instance ) {

	global $bontoolkit;

	$awe = BON_TOOLKIT_FONT_AWESOME;

	extract( $args );

	/* Our variables from the widget settings ---------------------------------------*/
	$title = apply_filters('widget_title', $instance['title'] );
	$embed = isset($instance['embed']) ? $instance['embed'] : '';
	$poster = isset($instance['poster']) ? $instance['poster'] : '';
	$m4v = isset($instance['m4v']) ? $instance['m4v'] : '';
	$ogv = isset($instance['ogv']) ? $instance['ogv'] : '';
	$desc = isset($instance['desc']) ? $instance['desc'] : '';

	/* Display widget ---------------------------------------------------------------*/
	echo $before_widget;

	if ( $title ) { echo $before_title . $title . $after_title; }

	$vid_args = array(
		'embed' => $embed,
		'm4v' => $m4v,
		'ogv' => $ogv,
		'poster' => $poster,
		'id' => $this->id,
		'echo' => true,
		'desc' => $desc,
	);

	bon_toolkit_video($vid_args);	
	
	echo $after_widget;
}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
function update( $new_instance, $old_instance ) {
	$instance = $old_instance;

	/* Strip tags to remove HTML (important for text inputs) ------------------------*/
	$instance['title'] = strip_tags( $new_instance['title'] );

	
	/* Stripslashes for html inputs -------------------------------------------------*/
	$instance['desc'] = stripslashes( $new_instance['desc']);
	$instance['embed'] = stripslashes( $new_instance['embed']);
	$instance['ogv'] = stripslashes( $new_instance['ogv']);
	$instance['m4v'] = stripslashes( $new_instance['m4v']);
	$instance['poster'] = stripslashes( $new_instance['poster']);

	return $instance;
}


/*-----------------------------------------------------------------------------------*/
/*	Widget Settings (Displays the widget settings controls on the widget panel)
/*-----------------------------------------------------------------------------------*/
function form( $instance ) {

	/* Set up some default widget settings ------------------------------------------*/
	$defaults = array(
		'title' => 'Video Widget',
		'embed' => '',
		'ogv' => '',
		'm4v' => '',
		'poster' => '',
		'desc' => '',
	);
	
	$instance = wp_parse_args( (array) $instance, $defaults ); 
	
	/* Build our form ---------------------------------------------------------------*/
	?>

	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><code><?php _e('Title', 'bon-toolkit') ?></code></label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'embed' ); ?>"><code><?php _e('Video Url (Third Party Host)', 'bon-toolkit') ?></code></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'embed' ); ?>" name="<?php echo $this->get_field_name( 'embed' ); ?>"><?php echo esc_url( $instance['embed'] ); ?></textarea>
		<small><?php _e('For List of Supported Providers please see <a href="http://codex.wordpress.org/Embeds" target="blank">http://codex.wordpress.org/Embeds</a>', 'bon-toolkit'); ?></small>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'm4v' ); ?>"><code><?php _e('M4V Video Url (Self Hosted)', 'bon-toolkit') ?></code></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'm4v' ); ?>" name="<?php echo $this->get_field_name( 'm4v' ); ?>"><?php echo esc_url( $instance['m4v'] ); ?></textarea>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'ogv' ); ?>"><code><?php _e('OGV Video Url (Self Hosted)', 'bon-toolkit') ?></code></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'ogv' ); ?>" name="<?php echo $this->get_field_name( 'ogv' ); ?>"><?php echo esc_url( $instance['ogv'] ); ?></textarea>
		
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id( 'poster' ); ?>"><code><?php _e('Poster Url (Self Hosted)', 'bon-toolkit') ?></code></label>
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'poster' ); ?>" name="<?php echo $this->get_field_name( 'poster' ); ?>"><?php echo esc_url( $instance['poster'] ); ?></textarea>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'desc' ); ?>"><code><?php _e('Short Description', 'bon-toolkit') ?></code></label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'desc' ); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>" value="<?php echo stripslashes(htmlspecialchars(( $instance['desc'] ), ENT_QUOTES)); ?>" />
	</p>
		
	<?php
	}
}
?>