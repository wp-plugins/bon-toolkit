<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Post
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

add_action( 'widgets_init', 'load_bon_toolkit_post_widget' );

function load_bon_toolkit_post_widget() {
	register_widget( 'Bon_Toolkit_Widget_Posts' );
}


/**
 * Archives widget class.
 *
 * @since 1.0
 */
class Bon_Toolkit_Widget_Posts extends WP_Widget {

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 *
	 * @since 1.2.0
	 */
	function __construct() {

		/* Set up the widget options. */
		$widget_options = array(
			'classname'   => 'bon-toolkit-posts-widget',
			'description' => esc_html__( 'An advanced widget that gives you query your latest posts from various post types and terms.', 'bon-toolkit' )
		);

		/* Set up the widget control options. */
		$control_options = array(

		);

		/* Create the widget. */
		$this->WP_Widget(
			'bon-toolkit-posts',            // $this->id_base
			BON_TOOLKIT_PREFIX . ' ' . __( 'Posts Widget', 'bon-toolkit' ), // $this->name
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
		$pattern = "#\[(.*?)\]#s";
		$new_term = preg_replace($pattern, '', strip_tags($instance['term']));
		$instance['term'] = $new_term;
		/* Set the $args for wp_get_archives() to the $instance array. */
		$args = $instance;

		/* Overwrite the $echo argument and set it to false. */
		$args['echo'] = false;

		/* Output the theme's $before_widget wrapper. */
		echo $before_widget;

		/* If a title was input by the user, display it. */
		if ( !empty( $instance['title'] ) )
			echo $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

		$query_args = array(
			'post_status' => 'publish',
			'post_type' => $args['post_type'],
			'posts_per_page' => $args['limit'],
			'order' =>	$args['order'],
			'orderby' => $args['orderby'],
			'ignore_sticky_posts' => true,
		);
		$tax_query = array();

		if(!empty($args['term']) && !empty($args['taxonomy'])) {
			$tax_query[] = array(
					'taxonomy' => $args['taxonomy'],
		            'field' => 'slug',
		            'terms' => $args['term']
				);
		}

		$query_args['tax_query'] = $tax_query;

		$post_query = new WP_Query($query_args);

		if($post_query->have_posts()) : while($post_query->have_posts()): $post_query->the_post();

		?>

		<div class="item clear">
			<?php if ( current_theme_supports( 'get-the-image' ) && $args['display_thumb'] == 'yes' ) get_the_image( array( 'image_scan' => true, 'before' => '<div class="featured-image">', 'after' => '</div>' ) ); ?>
			<div class="item-content">
				<h3 class="item-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute( array('before' => __('Permalink to ', 'bon-toolkit')) ); ?>"><?php the_title(); ?></a></h3>
				<?php 
					if($args['include_meta'] == 'yes') {
						echo apply_atomic_shortcode('post_widget_meta', __('[entry-published format="M, d Y" text="Published on"] [entry-author text="by"]','bon-toolkit'));
					}
				?>
			</div>
		</div>
		<?php 

		endwhile; 
		else :
			echo '<p>' . __('No post found.','bon-toolkit') . '</p>';
		endif; wp_reset_postdata();
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
		$instance['limit']  = strip_tags( $new_instance['limit'] );
		$instance['post_type']  = strip_tags( $new_instance['post_type'] );
		$instance['order']  = strip_tags( $new_instance['order'] );
		$instance['orderby']  = strip_tags( $new_instance['orderby'] );
		$instance['include_meta']  = strip_tags( $new_instance['include_meta'] );
		$instance['display_thumb']  = strip_tags( $new_instance['display_thumb'] );
		
		$instance['term']  = strip_tags($new_instance['term']);

		if(isset($new_instance['term'])) {
			$pattern = "#\[(.*?)\]#s";
			preg_match_all($pattern, $new_instance['term'], $matches);
			$instance['taxonomy']  = $matches[1][0];
		} else {
			$instance['taxonomy']  = '';
		}
		


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
			'title'           => esc_attr__( 'Latest Posts', 'bon-toolkit' ),
			'limit'           => 5,
			'post_type'		  => 'post',
			'taxonomy'        => '',
			'term'			  => '',
			'orderby'		  => 'date',
			'order'			  => 'DESC',
			'include_meta'	  => 'yes',
			'display_thumb'   => 'yes',
		);

		/* Create an array of order options. */
		$order = array(
			'ASC'  => esc_attr__( 'Ascending', 'bon-toolkit' ),
			'DESC' => esc_attr__( 'Descending', 'bon-toolkit' )
		);

		$opts = array(
			'yes'  => esc_attr__( 'Yes', 'bon-toolkit' ),
			'no' => esc_attr__( 'No', 'bon-toolkit' )
		);

		$orderby = array(
			'ID' => esc_attr__( 'ID', 'bon-toolkit' ),
			'title' => esc_attr__( 'Title', 'bon-toolkit' ),
			'name' => esc_attr__( 'Name', 'bon-toolkit' ),
			'date' => esc_attr__( 'Date', 'bon-toolkit' ),
			'rand' => esc_attr__( 'Random', 'bon-toolkit' ),
			'comment_count' => esc_attr__( 'Comment Count', 'bon-toolkit' ),
		);

		/* Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );
		$taxonomies = array();
		$terms = array();
		
		?>

		<div class="bon-toolkit-widget-controls">
		<p>
			<code>
				<?php _e('<strong>Note:</strong> Post type and term value have to match each taxonomy belong to post type for the query to work.','bon-toolkit'); ?>
			</code>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'bon-toolkit' ); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><code><?php _e('Limit','bon-toolkit'); ?></code></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'display_thumb' ); ?>"><code><?php _e('Display Thumbnail','bon-toolkit'); ?></code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'display_thumb' ); ?>">
				<?php foreach ( $opts as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['display_thumb'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><code><?php _e('Order','bon-toolkit'); ?></code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>">
				<?php foreach ( $order as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['order'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><code><?php _e('Order by','bon-toolkit'); ?></code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>">
				<?php foreach ( $orderby as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['orderby'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'include_meta' ); ?>"><code><?php _e('Include Published Meta','bon-toolkit'); ?></code></label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'include_meta' ); ?>" name="<?php echo $this->get_field_name( 'include_meta' ); ?>">
				<?php foreach ( $opts as $option_value => $option_label ) { ?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['include_meta'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><code><?php _e('Post Type','bon-toolkit'); ?></code></label>
			<select class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>">
				<option value="" <?php selected( $instance['post_type'], '' ); ?>><?php _e('Select One','bon-toolkit'); ?></option> 
				<?php 
				$post_type_args = get_post_types(array('public' => true, 'show_ui' => true, 'show_in_nav_menus' => true, 'publicly_queryable' => true));

				foreach ( $post_type_args as $option_value => $option_label ) { 
					$temp_taxonomies = get_object_taxonomies($option_value, 'objects');

					if(!empty($temp_taxonomies)) {

						foreach($temp_taxonomies as $temp_key => $temp_val) {
							
							if($temp_val->show_ui === true) {
								$taxonomies[$option_value][] = $temp_key ;
							}
						}
					}

					?>
					<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $instance['post_type'], $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</p>
		<?php
		if(is_array($taxonomies) && !empty($taxonomies)) {
		?>
		<p>

		<label for="<?php echo $this->get_field_id( 'term' ); ?>"><code><?php _e('Term','bon-toolkit'); ?></code></label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'term' ); ?>" name="<?php echo $this->get_field_name( 'term' ); ?>">
		<option value=""><?php _e('Select One', 'bon-toolkit'); ?>
		<?php
			foreach($taxonomies as $tax_key => $tax_vals) {
				
				foreach($tax_vals as $tax_val) {
					if(strpos($tax_val,'tag') === false) { ?>
						<optgroup label="<?php echo ucwords(str_replace('_',' ', str_replace('-', ' ', esc_html( $tax_val )))); ?>">

					<?php $temp_terms = get_terms($tax_val);
						foreach($temp_terms as $temp_term) { ?>
						<option value="[<?php echo $tax_val; ?>]<?php echo esc_attr( $temp_term->slug ); ?>" <?php selected( $instance['term'], '['. $tax_val . ']' . $temp_term->slug ); ?>><?php echo esc_html( $temp_term->name ); ?></option>'
					<?php } ?>
						</optgroup>
						<?php
					}
				}

			}
		?>
		</select>

		</p>
		<?php
		}
		?>
		</div>
		<div style="clear:both;">&nbsp;</div>
	<?php
	}

}

?>