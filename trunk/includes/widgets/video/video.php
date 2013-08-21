<?php
/*-----------------------------------------------------------------------------------*/
/* Bon Toolkit Video Widget
/*-----------------------------------------------------------------------------------*/


add_action( 'widgets_init', 'load_bon_toolkit_video_widget' );

function load_bon_toolkit_video_widget() {
	register_widget( 'bon_toolkit_video_widget' );
}

/*-----------------------------------------------------------------------------------*/
/*  Widget class
/*-----------------------------------------------------------------------------------*/
class bon_toolkit_video_widget extends WP_Widget {

	

/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
function bon_toolkit_video_widget() {

	$widget_ops = array( 'classname' => 'bon-toolkit-video-widget', 'description' => __('Show Video', 'bon-toolkit') );
	$control_ops = array( 'id_base' => 'bon-toolkit-video-widget' );
	$this->WP_Widget('bon-toolkit-video-widget', BON_TOOLKIT_PREFIX . __(' Video Widget', 'bon-toolkit'), $widget_ops, $control_ops);

}



function widget( $args, $instance ) {

	global $bontoolkit;

	$awe = BON_TOOLKIT_FONT_AWESOME;

	if(!$awe) {
		$awe = 'awe-';
	}
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

	echo '<div class="bon-toolkit-video">';

    if(!empty($embed)) {
    	echo '<div class="bon-toolkit-video-embed">';
    	$embed_code = wp_oembed_get($embed);
    	echo $embed_code;
    	echo '</div>';

    } else if(!empty ($m4v) && !empty($ogv)) {
    	echo '<div id="jp-'.$this->id.'" class="bon-toolkit-jplayer jp-jplayer jp-jplayer-video" data-poster="'.$poster.'" data-m4v="'.$m4v.'" data-ogv="'.$ogv.'"></div>';

    	echo '<div class="jp-video-container">
            <div class="jp-video">
                <div class="jp-type-single">
                    <div id="jp-interface-'.$this->id.'" class="jp-interface">
                        <div class="jp-controls">
                            <div class="jp-play" tabindex="1">
                                <span class="'.$awe.'play icon"></span>
                            </div>
                            <div class="jp-pause" tabindex="1">
                                <span class="'.$awe.'pause icon"></span>
                            </div>
                            <div class="jp-progress-container">
                                <div class="jp-progress">
                                    <div class="jp-seek-bar">
                                        <div class="jp-play-bar"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="jp-mute" tabindex="1"><span class="'.$awe.'volume-up icon"></span></div>
                            <div class="jp-unmute" tabindex="1"><span class="'.$awe.'volume-off icon"></span></div>
                            <div class="jp-volume-bar-container">
                                <div class="jp-volume-bar">
                                    <div class="jp-volume-bar-value"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    } else {
    	echo '<p>'. __('Error Loading Video', 'bon-toolkit') . '</p>';
    }
    if($desc != '') {
		echo '<div class="bon-toolkit-video-desc">' . $desc . '</div>';
    }
	echo '</div>';
		
	
	
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