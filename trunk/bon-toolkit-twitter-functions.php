<?php

	/**
	 * Returns tweets from a transient or calls our private oauth function to get the tweets, parses them,
	 * and sets a transient if needed.
	 * 
	 * @param string $username The username to be used
	 * @param string $count Number of tweets to be returned
	 * @return array of the tweets
	 */
	function bon_toolkit_get_tweets($username, $count) {

		global $bontoolkit;

		$bon_toolkit_options = get_option($bontoolkit->option_name);

		$config = array();
		$config['username'] = $username;
		$config['count'] = $count;
		$config['access_token'] = $bon_toolkit_options['twitter_access_token'];
		$config['access_token_secret'] = $bon_toolkit_options['twitter_access_token_secret'];
		$config['consumer_key'] = $bon_toolkit_options['twitter_consumer_key'];
		$config['consumer_key_secret'] = $bon_toolkit_options['twitter_consumer_key_secret'];

		$transname = 'bon_toolkit_tw_' . $username . '_' . $count;

		$result = get_transient( $transname );
		if( !$result ) {
			$result = bon_toolkit_twitter_oauth($config);
			if( isset($result['errors']) ){
				$result = NULL; 
			} else {
				$result = bon_toolkit_parse_tweet( $result );
				set_transient( $transname, $result, 300 );
			}
		} else {
			if( is_string($result) )
				unserialize($result);
		}

		return $result;
	}

	/**
	 * Get the tweets feed from Twitter API 1.1
	 *
	 * @param array $config 
	 * @return array $results
	 */
	function bon_toolkit_twitter_oauth($config) {
		if( empty($config['access_token']) ) 
			return array('error' => __('Access Token not properly configured.', 'bon-toolkit'));		
		if( empty($config['access_token_secret']) ) 
			return array('error' => __('Access Token Secret not properly configured.', 'bon-toolkit'));
		if( empty($config['consumer_key']) ) 
			return array('error' => __('Consumer Key not properly configured.', 'bon-toolkit'));		
		if( empty($config['consumer_key_secret']) ) 
			return array('error' => __('Consumer Key Secret not properly configured.', 'bon-toolkit'));		

		$options = array(
			'trim_user' => true,
			'exclude_replies' => false,
			'include_rts' => true,
			'count' => $config['count'],
			'screen_name' => $config['username']
		);

		require_once('oauth/twitteroauth.php');
		$connection = new TwitterOAuth($config['consumer_key'], $config['consumer_key_secret'], $config['access_token'], $config['access_token_secret']);
		$result = $connection->get('statuses/user_timeline', $options);

		return $result;
	}

	/**
	 * Parse the tweets to the needed information
	 *
	 * @param array $results of the tweets to be parsed
	 * @return array parsed tweets with timestamp, text, and id
	 */
	function bon_toolkit_parse_tweet($results = array()) {
		$tweets = array();
		if($results) {
			foreach($results as $result) {
				$timestamp = bon_toolkit_twitter_relative_time($result['created_at']);
				$tweets[] = array(
					'timestamp' => $timestamp,
					'text' => filter_var($result['text'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH),
					'id' => $result['id_str']
				);
			}
		}

		return $tweets;
	}

	/**
	 * Changes text to links
	 *
	 * @param string $text text to be linkified
	 * @return string linkified text 
	 */
	function bon_toolkit_twitter_hyperlink($matches) {
		return '<a href="' . $matches[0] . '" target="_blank">' . $matches[0] . '</a>';
	}

	/**
	 * Changes text to links
	 *
	 * @param string $text text to be linkified
	 * @return string linkified text 
	 */
	function bon_toolkit_twitter_username_link($matches) {
		return '<a href="http://twitter.com/' . $matches[0] . '" target="_blank">' . $matches[0] . '</a>';
	}
	/**
	 * Changes text to links
	 *
	 * @param string $text text to be linkified
	 * @return string linkified text 
	 */
	function bon_toolkit_twitter_format_link($text) {
		// convert links
		$string = preg_replace_callback(
			"/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", 'bon_toolkit_twitter_hyperlink', $text
		);

		// convert @usernames
		$string = preg_replace_callback(
			'/@([A-Za-z0-9_]{1,15})/', 'bon_toolkit_twitter_username_link', $string
		);

		return $string;
	}

	function bon_toolkit_twitter_relative_time($a) {
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

	function bon_toolkit_twitter($args = '') {

		$defaults = array(
			'username' => '',
			'count' => '',
			'tweettext' => '',
			'echo' => true,
		);

		$args = wp_parse_args( (array) $args, $defaults );

		$tweets = bon_toolkit_get_tweets($args['username'], $args['count']);

		$o = '<ul>';

		if( $tweets && is_array($tweets) ) {
			foreach( $tweets as $tweet ) {
				$text = mb_convert_encoding( utf8_encode($tweet['text']), "HTML-ENTITIES", "UTF-8");
				$text = bon_toolkit_twitter_format_link($text);
				$o.= '<li class="tweet-item">';
					$o .= $text;
					$o .= ' <a class="twitter-timestamp" href="http://twitter.com/' . $args['username'] . '/status/' . $tweet['id'] . '">' . $tweet['timestamp'] . '</a>';
				$o.= '</li>';
			}
		} else {
			$o.= '<li>' . __('There was an error grabbing the Twitter feed', 'bon-toolkit') . '</li>';
		}

		$o.= '</ul>';

		if( !empty($args['tweettext']) ) {
			$o.= '<a class="twitter-link" href="http://twitter.com/' . $args['username'] . '">' . $args['tweettext'] . '</a>';
		}

		if($args['echo'] === true) {
			echo $o;
		} else {
			return $o;
		}
	}
?>