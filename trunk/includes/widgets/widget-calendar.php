<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Calendar
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

add_action( 'widgets_init', 'load_bon_toolkit_calendar_widget' );

function load_bon_toolkit_calendar_widget() {
	register_widget( 'Bon_Toolkit_Widget_Calendar' );
}

class Bon_Toolkit_Widget_Calendar extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'calendar',
			'description' => esc_html__( 'An advanced widget that gives you total control over the output of your calendar.', 'bon-toolkit' )
		);

		/* Set up the widget control options. */
		$control_options = array(
			'width'  => 200,
			'height' => 350
		);

		/* Create the widget. */
		$this->WP_Widget(
			'bon-toolkit-calendar',               // $this->id_base
			BON_TOOLKIT_PREFIX . ' ' . __( 'Calendar', 'bon-toolkit' ), // $this->name
			$widget_options,                 // $this->widget_options
			$control_options                 // $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 0.6.0
	 */
	function widget( $sidebar, $instance ) {
		extract( $sidebar );

		/* Get the $initial argument. */
		$initial = !empty( $instance['initial'] ) ? true : false;

		/* Output the theme's widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		/* Display the calendar. */
		echo '<div class="calendar-wrap">';
			echo str_replace( array( "\r", "\n", "\t" ), '', get_calendar( $initial, false ) );
		echo '</div><!-- .calendar-wrap -->';

		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['initial'] = ( isset( $new_instance['initial'] ) ? 1 : 0 );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 0.6.0
	 */
	function form( $instance ) {

		/* Set up the default form values. */
		$defaults = array(
			'title'   => esc_attr__( 'Calendar', 'bon-toolkit' ),
			'initial' => false
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<div class="bon-toolkit-widget-controls columns-1">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bon-toolkit' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['initial'], true ); ?> id="<?php echo $this->get_field_id( 'initial' ); ?>" name="<?php echo $this->get_field_name( 'initial' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'initial' ); ?>"><?php _e( 'One-letter abbreviation?', 'bon-toolkit' ); ?> <code>initial</code></label>
		</p>
		</div>
	<?php
	}
}

?>