<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * Widget Social
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

add_action( 'widgets_init', 'load_bon_toolkit_social_widget' );

function load_bon_toolkit_social_widget() {
	register_widget( 'Bon_Toolkit_Widget_Social' );
}

class Bon_Toolkit_Widget_Social extends WP_Widget {
	/**
	 * @var array
	 * 
	 */
	public $social_params = array();

	function __construct() {
	    $widget_ops = array( 'classname' => 'bon-toolkit-social-widget', 'description' => __('Show social icon links', 'bon-toolkit') );
		$control_ops = array( 'width'  => 800, 'height' => 350, );
		$this->WP_Widget('bon-toolkit-social', BON_TOOLKIT_PREFIX . __(' Social Icons Widget', 'bon-toolkit'), $widget_ops, $control_ops);
		
		$this->social_params = array(
			'wordpress_icon',
			'forrst_icon',
			'foursquare_icon',
			'delicious_icon',
			'blogger_icon',
			'behance_icon',
			'twitter_icon',
			'dribbble_icon',
			'facebook_icon',
			'vimeo_icon',
			'tumblr_icon',
			'linkedin_icon',
			'flickr_icon',
			'google_icon',
			'rss_icon',
			'youtube_icon',
			'pinterest_icon',
			'stumbleupon_icon',
			'rdio_icon',
			'spotify_icon',
			'instagram_icon',

		);
	}

	function widget( $args, $instance ) {
 		extract($args);

		$icons_title = apply_filters('widget_title', $instance['title']);
		
		$size = (isset($instance['size']) ? $instance['size'] : '' );
		$color = (isset($instance['color']) ? $instance['color'] : '' );
		$color_style = (isset($instance['color_style']) ? $instance['color_style'] : '' );
		$shape_style = (isset($instance['shape_style']) ? $instance['shape_style'] : '' );

 		$class = 'bon-toolkit-social-icon ';
 		if(isset($size)) {
 			$class .= ' ' . $size;
 		}
 		if(isset($color)) {
 			$class .= ' ' . $color;
 		}
 		if(isset($color_style)) {
 			$class .= ' ' . $color_style;
 		}
 		if(isset($shape_style)) {
 			$class .= ' ' . $shape_style;
 		}

		echo $before_widget; ?>
		
		<?php if ( $icons_title ) echo $before_title . $icons_title . $after_title; ?>

		<div class="bon-toolkit-social-widget-wrapper <?php echo $class; ?>">
				<?php
					if( isset($this->social_params) && is_array($this->social_params) ) {
						foreach($this->social_params as $param) {

							
							if(isset($instance[$param]) && !empty($instance[$param])) {
								$icon_class = str_replace('_icon', '', $param);
							
							?>
							<a href="<?php echo $instance[$param]; ?>" target="blank" class="<?php echo $icon_class; ?>" title="<?php echo ucfirst($icon_class); ?>"><i class="bt-icon-<?php echo $icon_class; ?>"></i></a>
							
							<?php
							}
						}
					}
				?>
			
				<div class="clear" style="clear:both;"></div>
				
		</div>			
			
			<?php
		echo $after_widget;	
  }

	// Updating the widget
	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title']);
		
		$instance['color'] = strip_tags( $new_instance['color']);
		$instance['color_style'] = strip_tags( $new_instance['color_style']);
		$instance['shape_style'] = strip_tags( $new_instance['shape_style']);
		$instance['size'] = strip_tags( $new_instance['size']);

		foreach($this->social_params as $param) {
			$instance[$param] = strip_tags( $new_instance[$param]);
		}
	

		return $instance;
	}

	function form( $instance ) {
		
		$count = count($this->social_params);
		$count_half = ceil($count / 2);
		$i = 1;
		foreach($this->social_params as $param) {

			if($i == 1) {

				echo '<div class="bon-toolkit-widget-controls columns-3">';

			}

			$text = ucfirst(str_replace('_icon', '', $param));
			?>

				<p>
					<label for="<?php echo $this->get_field_id($param); ?>"><code><?php printf(__('%s Link','bon-toolkit'), $text); ?></code></label>
					<input class="widefat" type="text" id="<?php echo $this->get_field_id($param); ?>" name="<?php echo $this->get_field_name($param); ?>" value="<?php if(isset($instance[$param])) echo $instance[$param]; ?>" />
			 	</p>
			<?php

			if($i >= $count_half) {
				echo '</div>';
				$i = 0;
			}

			$i++;
		}
		if($i <= $count_half) {

			echo '</div>';
		}
		?>
		

	 	<div class="bon-toolkit-widget-controls columns-3 column-last">
	 	<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><code><?php _e('Title:','bon-toolkit'); ?></code></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php if(isset($instance['title'])) echo $instance['title']; ?>" />
		</p>
	 	<?php
	 		$color = (isset($instance['color']) ? $instance['color'] : '');
	 		$color_style = (isset($instance['color_style']) ? $instance['color_style'] : '');
	 		$shape_style = (isset($instance['shape_style']) ? $instance['shape_style'] : '');
	 		$size = (isset($instance['size']) ? $instance['size'] : '');
	 	?>
	 	<p>
			<label for="<?php echo $this->get_field_id('color_style'); ?>"><code><?php _e('Color Style','bon-toolkit'); ?></code></label>
			<select class="widefat" type="text" id="<?php echo $this->get_field_id('color_style'); ?>" name="<?php echo $this->get_field_name('color_style'); ?>">
				<option <?php selected($color_style, ''); ?> value=""><?php _e('Gradient','bon-toolkit'); ?></option>
				<option <?php selected($color_style, 'flat'); ?> value="flat"><?php _e('Flat','bon-toolkit'); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('color'); ?>"><code><?php _e('Color','bon-toolkit'); ?></code></label>
			<select class="widefat" type="text" id="<?php echo $this->get_field_id('color'); ?>" name="<?php echo $this->get_field_name('color'); ?>">
				<option <?php selected($color, ''); ?> value=""><?php _e('Social Color','bon-toolkit'); ?></option>
				<option <?php selected($color, 'bw'); ?> value="bw"><?php _e('Black and White','bon-toolkit'); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('shape_style'); ?>"><code><?php _e('Color Style','bon-toolkit'); ?></code></label>
			<select class="widefat" type="text" id="<?php echo $this->get_field_id('shape_style'); ?>" name="<?php echo $this->get_field_name('shape_style'); ?>">
				<option <?php selected($shape_style, ''); ?> value=""><?php _e('Square','bon-toolkit'); ?></option>
				<option <?php selected($shape_style, 'round'); ?> value="round"><?php _e('Round','bon-toolkit'); ?></option>
				<option <?php selected($shape_style, 'square-round'); ?> value="square-round"><?php _e('Square Rounded Corner','bon-toolkit'); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('size'); ?>"><code><?php _e('Size','bon-toolkit'); ?></code></label>
			<select class="widefat" type="text" id="<?php echo $this->get_field_id('size'); ?>" name="<?php echo $this->get_field_name('size'); ?>">
				<option <?php selected($size, ''); ?> value=""><?php _e('Medium','bon-toolkit'); ?></option>
				<option <?php selected($size, 'small'); ?> value="small"><?php _e('Small','bon-toolkit'); ?></option>
				<option <?php selected($size, 'large'); ?> value="large"><?php _e('Large','bon-toolkit'); ?></option>
			</select>
		</p>
		</div><div style="clear:both;">&nbsp;</div>
		<?php
	}
}

?>