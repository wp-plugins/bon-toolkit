<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Contact Form
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

add_action( 'widgets_init', 'load_bon_toolkit_contact_form_widget' );

function load_bon_toolkit_contact_form_widget() {
	register_widget( 'Bon_Toolkit_Widget_Contact_Form' );
}

class Bon_Toolkit_Widget_Contact_Form extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'bon-toolkit-contactform-widget',
			'description' => esc_html__( 'A Widget to show Contact Form.', 'bon-toolkit' )
		);

		/* Set up the widget control options. */
		$control_options = array();

		/* Create the widget. */
		$this->WP_Widget(
			'bon-toolkit-contactform',               // $this->id_base
			BON_TOOLKIT_PREFIX . ' ' . __( 'Contact Form Widget', 'bon-toolkit' ), // $this->name
			$widget_options,                 // $this->widget_options
			$control_options                 // $this->control_options
		);
	}

	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 1.0
	 */
	function widget( $sidebar, $instance ) {
		extract( $sidebar );

		/* Set the $args for wp_get_archives() to the $instance array. */
		$args = $instance;

		/* Overwrite the $echo argument and set it to false. */
		$args['echo'] = false;

		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		$o = apply_filters( 'bon_toolkit_contact_form_widget_filter', '', $args['email_address'], $args['color']);

		if($o != '') {
			echo $o;
		} else {
			$o = bon_toolkit_get_contact_form($args['email_address'], $args['color']);
        	echo $o;
		}

		
		/* Close the theme's widget wrapper. */
		echo $after_widget;
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 0.6.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $new_instance;

		$instance['title']  = strip_tags( $new_instance['title'] );
		$instance['email_address']  = (is_email( $new_instance['email_address'] )) ? $new_instance['email_address'] : '';
		$instance['color']  = strip_tags( $new_instance['color'] );

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
			'title'           => esc_attr__( 'Contact Us', 'bon-toolkit' ),
			'email_address'   => '',
			'color' => '',
		);

		$color = array(
	        'red' => __('Red','bon-toolkit'),
	        'green' => __('Green','bon-toolkit'),
	        'blue' => __('Blue','bon-toolkit'),
	        'orange' => __('Orange','bon-toolkit'),
	        'purple' => __('Purple','bon-toolkit'),
	        'yellow' => __('Yellow', 'bon-toolkit'),
	        'dark' => __('Dark','bon-toolkit')
	    );


		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

		?>

		<div class="bon-toolkit-widget-controls">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><code><?php _e( 'Title:', 'bon-toolkit' ); ?></code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'color' ); ?>"><code>link</code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'color' ); ?>" name="<?php echo $this->get_field_name( 'color' ); ?>">
				<?php foreach ( $color as $option_value => $option_label ) { ?>
					<option value="<?php echo $option_value; ?>" <?php selected( $instance['color'], $option_value ); ?>><?php echo $option_label; ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'email_address' ); ?>"><code><?php _e('Receiver Email Address','bon-toolkit'); ?></code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'email_address' ); ?>" name="<?php echo $this->get_field_name( 'email_address' ); ?>" value="<?php echo esc_attr( $instance['email_address'] ); ?>" />
		</p>
		</div>
	<?php
	}
}

?>