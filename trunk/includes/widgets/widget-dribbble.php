<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Dribbble
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

add_action( 'widgets_init', 'load_bon_toolkit_dribbble_widget' );

function load_bon_toolkit_dribbble_widget() {
	register_widget( 'Bon_Toolkit_Widget_Dribbble' );
}

class Bon_Toolkit_Widget_Dribbble extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'bon-toolkit-dribbble-widget', 'description' => __('Grab your latest Dribbble shots', 'bon-toolkit') );
		$control_ops = array();
		$this->WP_Widget( 'bon-toolkit-dribbble', BON_TOOLKIT_PREFIX . ' ' . __('Dribbble Widget', 'bon-toolkit'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		
		include_once(ABSPATH . WPINC . '/feed.php');
 
		$playerName = $instance['dribbble_name'];
		$shots = $instance['dribbble_shots'];
	
		if(function_exists('fetch_feed')):
			$rss = fetch_feed("http://dribbble.com/players/$playerName/shots.rss");
			add_filter( 'wp_feed_cache_transient_lifetime', create_function( '$a', 'return 1800;' ) );
			if (!is_wp_error( $rss ) ) : 
				$items = $rss->get_items(0, $rss->get_item_quantity($shots)); 
			endif;
		endif;
		
		extract( $args );
		$dribbble_title = esc_attr( $instance['title'] );
		$dribbble_name = esc_attr( $instance['dribbble_name'] );
		$dribbble_shots = esc_attr( $instance['dribbble_shots'] );
		echo $before_widget;
?>	
	<div class="dribbble-widget">	
		<?php if ( $dribbble_title ) echo $before_title . $dribbble_title . $after_title; ?>
				
		<ul class="dribbbles">
			<?php foreach ( $items as $item ):
				$title = $item->get_title();
				$link = $item->get_permalink();
				$date = $item->get_date('F d, Y');
				$description = $item->get_description();
			
				preg_match("/src=\"(http.*(jpg|jpeg|gif|png))/", $description, $image_url);
				$image = $image_url[1];
				
			?>
			<li class="dribbble-img"> 
				<a href="<?php echo $link; ?>" class="dribbble-link"><img src="<?php echo $image; ?>" alt="<?php echo $title;?>"/></a> 
		 	</li>
		 	<?php endforeach;?>
	 	</ul>
 	</div>

<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['dribbble_name'] = $new_instance['dribbble_name'];
		$instance['dribbble_shots'] = $new_instance['dribbble_shots'];		
		return $instance;
	}

	function form($instance) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'dribbble_name' => '', 'dribbble_shots' => '') );
		
?>
			
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','bon-toolkit'); ?> 
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php if(isset($instance['title'])) echo $instance['title']; ?>" />
				</label>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('dribbble_name'); ?>"><?php _e('Username:','bon-toolkit'); ?> 
					<input class="widefat" id="<?php echo $this->get_field_id('dribbble_name'); ?>" name="<?php echo $this->get_field_name('dribbble_name'); ?>" type="text" value="<?php if(isset($instance['dribbble_name'])) echo $instance['dribbble_name']; ?>" />
				</label>
			</p>
			
			<p>
				<label for="<?php echo $this->get_field_id('dribbble_shots'); ?>"><?php _e('Number of Shots:','bon-toolkit'); ?>
					<input class="widefat" id="<?php echo $this->get_field_id('dribbble_shots'); ?>" name="<?php echo $this->get_field_name('dribbble_shots'); ?>" type="text" value="<?php if(isset($instance['dribbble_shots'])) echo $instance['dribbble_shots']; ?>" />
				</label>
			</p>
              
  <?php
	}
}