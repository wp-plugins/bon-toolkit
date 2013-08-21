<?php

/*-----------------------------------------------------------------------------------*/
/* Okay Twitter Widget
/*-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'load_bon_toolkit_twitter_widget' );

function load_bon_toolkit_twitter_widget() {
	register_widget( 'bon_toolkit_twitter_widget' );
}


// Widget class.
class bon_toolkit_twitter_widget extends WP_Widget {

	
	function bon_toolkit_twitter_widget() {

		/* Widget settings. */
		$widget_ops = array( 'classname' => 'bon-toolkit-twitter-widget', 'description' => __('A widget that displays your latest tweet.', 'bon-toolkit') );

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'bon-toolkit-twitter-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'bon-toolkit-twitter-widget', BON_TOOLKIT_PREFIX . __(' Twitter Widget', 'bon-toolkit'), $widget_ops, $control_ops );
	}

	/**
	 * Find links and create the hyperlinks
	 */
	function hyperlinks($text) {
	    $text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"$1\" >$1</a>", $text);
	    $text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"http://$1\" >$1</a>", $text);
	    // match name@address
	    $text = preg_replace("/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i","<a href=\"mailto://$1\" >$1</a>", $text);
	        //mach #trendingtopics. Props to Michael Voigt
	    $text = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/#search?q=$2\" >#$2</a>$3 ", $text);
	    return $text;
	}
	/**
	 * Find twitter usernames and link to them
	 */
	function twitter_users($text) {
	       $text = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" class=\"twitter-user\">@$2</a>$3 ", $text);
	       return $text;
	}

	function relative_time($a) {
		//get current timestampt
		$b = strtotime("now"); 
		//get timestamp when tweet created
		$c = strtotime($a);
		//get difference
		$d = $b - $c;
		//calculate different time values
		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$week = $day * 7;
			
		if(is_numeric($d) && $d > 0) {
			//if less then 3 seconds
			if($d < 3) return "right now";
			//if less then minute
			if($d < $minute) return floor($d) . " seconds ago";
			//if less then 2 minutes
			if($d < $minute * 2) return "about 1 minute ago";
			//if less then hour
			if($d < $hour) return floor($d / $minute) . " minutes ago";
			//if less then 2 hours
			if($d < $hour * 2) return "about 1 hour ago";
			//if less then day
			if($d < $day) return floor($d / $hour) . " hours ago";
			//if more then day, but less then 2 days
			if($d > $day && $d < $day * 2) return "yesterday";
			//if less then year
			if($d < $day * 365) return floor($d / $day) . " days ago";
			//else return more than a year
			return "over a year ago";
		}
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

		$conkey = $instance['consumerkey'];
		$consecret = $instance['consumersecret'];
		$token = $instance['accesstoken'];
		$tokensecret = $instance['accesstokensecret'];


		$transName = 'list_tweets';
	    $cacheTime = 20;
	    if(false === ($twitterData = get_transient($transName) ) ){
	    	require_once 'twitteroauth.php';
			
			$twitterConnection = new TwitterOAuth(
								$conkey,	// Consumer Key
								$consecret,   	// Consumer secret
								$token,       // Access token
								$tokensecret    	// Access token secret
								);

			$twitterData = $twitterConnection->get(
					  'statuses/user_timeline',
					  array(
					    'screen_name'     => $twitter_username,
					    'count'           => $twitter_postcount,
					    'size'			  => 'normal',
					    'exclude_replies' => false
					  )
					);
			
			if($twitterConnection->http_code != 200)
			{
				$twitterData = get_transient($transName);
			}
	        // Save our new transient.
	        set_transient($transName, $twitterData, 60 * $cacheTime);
	    }
		
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
			
		?>
			

            <div class="twitter-widget">
	            <div class="tweet" id="twitter_list_<?php echo (isset($args['widget_id'])) ? $args['widget_id'] : ''; ?>">
	            	
			
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
            <?php
            	if(!empty($twitterData) && !isset($twitterData['error'])){
            		$i=0;
					$hyperlinks = true;
					$encode_utf8 = true;
					$twitter_users = true;
					$update = true;
					echo '
<ul class="twitter_update_list">';
		            foreach($twitterData as $item){
		                    $msg = $item->text;
		                    $img = $item->user->profile_image_url;
		                    $permalink = 'http://twitter.com/#!/'. $twitter_username .'/status/'. $item->id_str;
		                    if($encode_utf8) $msg = utf8_encode($msg);
                                    $msg = $this->encode_tweet($msg);
		                    $link = $permalink;
		                     echo '
<li class="twitter-item">';
		                      if ($hyperlinks) {    $msg = $this->hyperlinks($msg); }
		                      if ($twitter_users)  { $msg = $this->twitter_users($msg); }

		                      //$img = $this->profile_image($img, $twitter_username);
		                      //echo '<a href="'.$link.'">' . $img . '</a>';
		                      echo '<div>' . $msg;
		                      if($update) {
			                    	echo '<span class="twitter-timestamp"> ' . $this->relative_time($item->created_at) . '</span>';
			                     }
		                      echo '</div>';
 		                    
		                    echo '</li>
';
		                    $i++;
		                    if ( $i >= $twitter_postcount ) break;
		            }
					echo '</ul>
';
            	}
            ?>
	            </div>
	            <?php if ($tweettext) { ?>
	            
	            <a href="http://twitter.com/<?php echo $twitter_username; ?>" class="twitter-link"><i class="awe-twitter icon"></i><span class="tweet-text"><?php echo $tweettext; ?></span></a>
			
			<?php } ?>
			</div> 
		<?php 
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/* ---------------------------- */
	/* ------- Update Widget -------- */
	/* ---------------------------- */
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['postcount'] = strip_tags( $new_instance['postcount'] );
		$instance['tweettext'] = strip_tags( $new_instance['tweettext'] );
		$instance['consumerkey'] = strip_tags( $new_instance['consumerkey'] );
		$instance['consumersecret'] = strip_tags( $new_instance['consumersecret'] );
		$instance['accesstoken'] = strip_tags( $new_instance['accesstoken'] );
		$instance['accesstokensecret'] = strip_tags( $new_instance['accesstokensecret'] );

		/* No need to strip tags for.. */

		return $instance;
	}
	
	/* ---------------------------- */
	/* ------- Widget Settings ------- */
	/* ---------------------------- */
	
	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => 'Latest Tweets',
		'username' => 'envato',
		'postcount' => '5',
		'tweettext' => 'Follow on Twitter',
		'consumerkey' => '', 
		'consumersecret' => '', 
		'accesstoken' => '', 
		'accesstokensecret' => '', 
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

		<!-- consumerkey: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'consumerkey' ); ?>"><?php _e('Twitter Consumer Key', 'bon-toolkit') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'consumerkey' ); ?>" name="<?php echo $this->get_field_name( 'consumerkey' ); ?>" value="<?php echo $instance['consumerkey']; ?>" />
		</p>

		<!-- consumersecret: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'consumersecret' ); ?>"><?php _e('Twitter Consumer Secret', 'bon-toolkit') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'consumersecret' ); ?>" name="<?php echo $this->get_field_name( 'consumersecret' ); ?>" value="<?php echo $instance['consumersecret']; ?>" />
		</p>

		<!-- accesstoken: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'accesstoken' ); ?>"><?php _e('Twitter Access Token', 'bon-toolkit') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'accesstoken' ); ?>" name="<?php echo $this->get_field_name( 'accesstoken' ); ?>" value="<?php echo $instance['accesstoken']; ?>" />
		</p>

		<!-- accesstokensecret: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'accesstokensecret' ); ?>"><?php _e('Twitter Access Token Secret', 'bon-toolkit') ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'accesstokensecret' ); ?>" name="<?php echo $this->get_field_name( 'accesstokensecret' ); ?>" value="<?php echo $instance['accesstokensecret']; ?>" />
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