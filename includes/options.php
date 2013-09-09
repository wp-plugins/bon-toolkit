<?php

function bon_toolkit_set_options() {
	$options = array();

	$options[] = array(
		'type' => 'section',
		'id' => 'widget-setting',
		'label' => __('Widget Settings', 'bon-toolkit'),
		'class' => 'visible'
	);

	$options[] = array(
		'type' => 'info',
		'std' => 'Enable Widget Features',
	);

	$options[] = array(
		'type' => 'select',
		'id' => 'use_bon_toolkit_css',
		'std' => 'yes',
		'options' => array(
			'yes' => 'Yes',
			'no' => 'No',
		),
		'desc' => __('Use the default css from the plugin. Disable this if you want to use your own css','bon-toolkit'),
		'label' => __('Use Default Widget CSS', 'bon-toolkit')
	);

	$options[] = array(
		'type' => 'select',
		'id' => 'use_bon_toolkit_fontawesome',
		'std' => 'yes',
		'options' => array(
			'yes' => 'Yes',
			'no' => 'No',
		),
		'desc' => __('Use the plugin FontAwesome icon style','bon-toolkit'),
		'label' => __('Use FontAwesome from Plugin', 'bon-toolkit')
	);

	$options[] = array(
		'type' => 'text',
		'id' => 'font_awesome_prefix',
		'std' => 'awe-',
		'desc' => __('Your FontAwesome Icon prefix might be different from the plugin FontAwesome prefix specify the prefix in here.','bon-toolkit'),
		'label' => __('FontAwesome Prefix', 'bon-toolkit')
	);



	$options[] = array(
		'type' => 'section_close',
	);

	if( current_theme_supports('bon-portfolio') || 
		current_theme_supports('bon-poll') || 
		current_theme_supports('bon-featured-slider') || 
		current_theme_supports('bon-quiz') || 
		current_theme_supports('bon-featured-slider') || 
		current_theme_supports('bon-testimonial') ) {

		$options[] = array(
			'type' => 'section',
			'id' => 'post-type-settings',
			'std' => '',
			'label' => __('Post Type Settings', 'bon-toolkit'),
		);

		$options[] = array(
			'type' => 'info',
			'std' => 'Please note that some of this feature may unavailable if you\'re not using BonFramework or the theme do not support it.',
		);

	
		if(current_theme_supports( 'bon-portfolio' )) {
			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_portfolio',
				'std' => '',
				'desc' => __('Enable / Disable Portfolio Post Type','bon-toolkit'),
				'label' => __('Portfolio', 'bon-toolkit')
			);
		}
		if(current_theme_supports( 'bon-testimonial' )) {
			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_testimonial',
				'std' => '',
				'desc' => __('Enable / Disable Testimonial Post Type','bon-toolkit'),
				'label' => __('Testimonial', 'bon-toolkit')
			);
		}

		if(current_theme_supports( 'bon-poll' )) {
			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_poll',
				'std' => '',
				'desc' => __('Enable / Disable Poll Post Type','bon-toolkit'),
				'label' => __('Poll', 'bon-toolkit'),
			);
		}

		if(current_theme_supports( 'bon-review' )) {
			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_review',
				'std' => '',
				'desc' => __('Enable / Disable Review Post Type','bon-toolkit'),
				'label' => __('Review', 'bon-toolkit'),
			);
		}

		if(current_theme_supports( 'bon-featured-slider' )) {
			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_featured_slider',
				'std' => '',
				'desc' => __('Enable / Disable Featured Slider Post Type','bon-toolkit'),
				'label' => __('Featured Slider', 'bon-toolkit'),
			);
		}

		if(current_theme_supports( 'bon-quiz' )) {
			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_quiz',
				'std' => '',
				'desc' => __('Enable / Disable Quiz Post Type','bon-toolkit'),
				'label' => __('Quiz', 'bon-toolkit'),
			);

			$options[] = array(
				'type' => 'text',
				'id' => 'quiz_100',
				'std' => 'Perfect!!!',
				'desc' => '',
				'label' => __('Comment for Quiz Score 100', 'bon-toolkit'),
			);

			$options[] = array(
				'type' => 'text',
				'id' => 'quiz_90',
				'std' => 'Excellent!!!',
				'desc' => '',
				'label' => __('Comment for Quiz Score >= 90', 'bon-toolkit'),
			);

			$options[] = array(
				'type' => 'text',
				'id' => 'quiz_80',
				'std' => 'Good!!!',
				'desc' => '',
				'label' => __('Comment for Quiz Score >= 80', 'bon-toolkit'),
			);

			$options[] = array(
				'type' => 'text',
				'id' => 'quiz_60',
				'std' => 'Average!!!',
				'desc' => '',
				'label' => __('Comment for Quiz Score >= 60', 'bon-toolkit'),
			);

			$options[] = array(
				'type' => 'text',
				'id' => 'quiz_40',
				'std' => 'Bad!!!',
				'desc' => '',
				'label' => __('Comment for Quiz Score >= 40', 'bon-toolkit'),
			);

			$options[] = array(
				'type' => 'text',
				'id' => 'quiz_30',
				'std' => 'Poor!!!',
				'desc' => '',
				'label' => __('Comment for Quiz Score >= 30', 'bon-toolkit'),
			);

			$options[] = array(
				'type' => 'text',
				'id' => 'quiz_0',
				'std' => 'Worst!!!',
				'desc' => '',
				'label' => __('Comment for Quiz Score 0', 'bon-toolkit'),
			);
		}
	

		$options[] = array(
			'type' => 'section_close',
		);
	}

	

		// Advanced Widget
		$options[] = array(
			'type' => 'section',
			'id' => 'widget-settings',
			'std' => '',
			'label' => __('Widget Settings', 'bon-toolkit'),
		);
			
			$options[] = array(
			'type' => 'checkbox',
			'id' => 'enable_twitter_widget',
			'desc' => __('Enable / Disable Twitter Widget.','bon-toolkit'),
			'label' => __('Twitter Widget', 'bon-toolkit')
		);

		$options[] = array(
			'type' => 'checkbox',
			'id' => 'enable_dribbble_widget',
			'desc' => __('Enable / Disable Dribbble Widget.','bon-toolkit'),
			'label' => __('Dribbble Widget', 'bon-toolkit')
		);

		$options[] = array(
			'type' => 'checkbox',
			'id' => 'enable_flickr_widget',
			'desc' => __('Enable / Disable Flickr Widget.','bon-toolkit'),
			'label' => __('Flickr Widget', 'bon-toolkit')
		);

		$options[] = array(
			'type' => 'checkbox',
			'id' => 'enable_social_widget',
			'desc' => __('Enable / Disable Social Icons Widget.','bon-toolkit'),
			'label' => __('Social Icons Widget', 'bon-toolkit')
		);

		$options[] = array(
			'type' => 'checkbox',
			'id' => 'enable_video_widget',
			'desc' => __('Enable / Disable Video Widget.','bon-toolkit'),
			'label' => __('Video Widget', 'bon-toolkit')
		);

		$options[] = array(
			'type' => 'checkbox',
			'id' => 'enable_posts_widget',
			'desc' => __('Enable / Disable Posts Widget.','bon-toolkit'),
			'label' => __('Posts Widget', 'bon-toolkit')
		);

		$options[] = array(
			'type' => 'checkbox',
			'id' => 'enable_contactform_widget',
			'desc' => __('Enable / Disable Contact Form Widget.','bon-toolkit'),
			'label' => __('Contact Form Widget', 'bon-toolkit')
		);


		if( current_theme_supports( 'bon-advanced-widget' ) ) {

			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_advanced_archives',
				'desc' => __('Enable / Disable Advanced Archives Widget.','bon-toolkit'),
				'label' => __('Archives Widget', 'bon-toolkit')
			);

			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_advanced_tags',
				'desc' => __('Enable / Disable Advanced Tags Widget.','bon-toolkit'),
				'label' => __('Tags Widget', 'bon-toolkit')
			);

			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_advanced_search',
				'desc' => __('Enable / Disable Advanced Search Widget.','bon-toolkit'),
				'label' => __('Search Widget', 'bon-toolkit')
			);

			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_advanced_pages',
				'desc' => __('Enable / Disable Advanced Pages Widget.','bon-toolkit'),
				'label' => __('Pages Widget', 'bon-toolkit')
			);

			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_advanced_nav_menu',
				'desc' => __('Enable / Disable Advanced Navigation Menu Widget.','bon-toolkit'),
				'label' => __('Navigation Menu Widget', 'bon-toolkit')
			);

			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_advanced_categories',
				'desc' => __('Enable / Disable Advanced Categories Widget.','bon-toolkit'),
				'label' => __('Categories Widget', 'bon-toolkit')
			);

			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_advanced_calendar',
				'desc' => __('Enable / Disable Advanced Calendar Widget.','bon-toolkit'),
				'label' => __('Calendar Widget', 'bon-toolkit')
			);

			$options[] = array(
				'type' => 'checkbox',
				'id' => 'enable_advanced_authors',
				'desc' => __('Enable / Disable Advanced Authors Widget.','bon-toolkit'),
				'label' => __('Authors Widget', 'bon-toolkit')
			);
			
		}

		$options[] = array(
			'type' => 'section_close',
		);
	
	
	$options[] = array(
		'type' => 'section',
		'id' => 'api-settings',
		'std' => '',
		'label' => __('API Settings', 'bon-toolkit'),
	);

	$options[] = array(
		'type' => 'info',
		'std' => __('Here is the place to fill the api key needed for some features to work.','bon-toolkit'),
	);

	$options[] = array(
		'type' => 'text',
		'id' => 'google_api_key',
		'desc' => __('Api Key from Google to activate the Google Map Shortcode. Visit <b>Toolkit Help Tab</b> to find how to activate it','bon-toolkit'),
		'label' => __('Google Map API Key', 'bon-toolkit'),
	);

	$options[] = array(
		'type' => 'text',
		'id' => 'twitter_access_token',
		'desc' => __('The twitter access token key generated from your apps.','bon-toolkit'),
		'label' => __('Twitter Acces Token', 'bon-toolkit'),
	);

	$options[] = array(
		'type' => 'text',
		'id' => 'twitter_access_token_secret',
		'desc' => __('The twitter access token secret key generated from your apps.','bon-toolkit'),
		'label' => __('Twitter Acces Token Secret', 'bon-toolkit'),
	);

	$options[] = array(
		'type' => 'text',
		'id' => 'twitter_consumer_key',
		'desc' => __('The twitter consumer key generated from your apps.','bon-toolkit'),
		'label' => __('Twitter Consumer Key', 'bon-toolkit'),
	);

	$options[] = array(
		'type' => 'text',
		'id' => 'twitter_consumer_key_secret',
		'desc' => __('The twitter consumer secret key generated from your apps.','bon-toolkit'),
		'label' => __('Twitter Consumer Key Secret', 'bon-toolkit'),
	);


	$options[] = array(
		'type' => 'section_close',
	);
	
	

	$options[] = array(
		'type' => 'section',
		'id' => 'social-share-settings',
		'std' => '',
		'label' => __('Social Share Settings', 'bon-toolkit'),
	);

	$options[] = array(
				'type' => 'checkbox',
				'id' => 'automatic_share_button',
				'desc' => __('Turn on the social share button on posts and pages','bon-toolkit'),
				'label' => __('Turn on Social Share button', 'bon-toolkit')
			);

	$options[] = array(
		'type' => 'select',
		'id' => 'share_button_location',
		'std' => '',
		'options' => array(
			'after_post' => __('After Post Content', 'bon'),
			'before_post' => __('Before Post Content', 'bon'),
		),
		'desc' => __('Where do you want the share button to be put?','bon-toolkit'),
		'label' => __('Set Location for Share Button', 'bon-toolkit'),
	);

	$options[] = array(
		'type' => 'section_close',
	);

	$options[] = array(
		'type' => 'section',
		'id' => 'help-setting',
		'std' => '',
		'label' => __('Toolkit Help', 'bon-toolkit'),
	);

	$options[] = array(
		'type' => 'info',
		'std' => 'A few resources to get you started with the Bon Toolkit.',
	);

	$options[] = array(
		'type' => 'help',
		'label' =>  __('What is this plugin and why do I need it?','bon-toolkit'),
		'desc' => __('The Bon Toolkit provides extra functionality to the collection of themes from <a target="blank" href="http://bonfirelab.com" title="bonfirelab">Bonfirelab</a>. The Toolkit adds various widgets (Twitter, Flickr, Dribbble and social icons), and a few custom post types. The plugin is not a requirement to use themes from bonfirelab, but it will extend the themes to function as you see them in the demos.','bon-toolkit'),
	);

	$options[] = array(
		'type' => 'help',
		'label' =>  __('How to use the Shortcode?','bon-toolkit'),
		'desc' => __('When editing post there is a small black button with "B" in the Editor. Press it and choose the shortcode you want.','bon-toolkit'),
	);

	$options[] = array(
		'type' => 'help',
		'label' =>  __('How to Get Google Map API Key?','bon-toolkit'),
		'desc' => __('<ul><li>Visit this link <a target="blank" href="https://code.google.com/apis/console">https://code.google.com/apis/console</a> and Log In with your Google Account.</li><li>Next click the <b>Create Project</b>, then you\'ll be taken to the google service page.</li><li>Click the <b>Services</b> link from the left-hand menu then Activate the <b>Google Maps API v3 service</b>.</li><li> After activation Click the <b>API Access</b> link from the left-hand menu. </li><li>Your API key is available from the <b>API Access</b> page, in the <b>Simple API Access</b> section. Maps API applications use the Key for browser apps. </li></ul>','bon-toolkit'),
	);

	$options[] = array(
		'type' => 'help',
		'label' =>  __('Why are the some of the Post Type Settings available in one of my Bonfirelab themes but not the other?','bon-toolkit'),
		'desc' => __('The Toolkit plugin only shows options / settings for themes that support it. If your theme does not support either feature, their settings will not be shown.','bon-toolkit'),
	);

	$options[] = array(
		'type' => 'help',
		'label' =>  __('Can I use this plugin with other themes?','bon-toolkit'),
		'desc' => __('This toolkit was developed to extend the functionality of Bonfirelab themes, however you can still use some parts of the plugin features such as widget in other themes. Advanced features like Custom Post Types will only work with Bonfirelab Themes due to framework dependant.','bon-toolkit'),
	);

	$options[] = array(
		'type' => 'help',
		'label' =>  __('This plugin is awesome where do I find the developer?','bon-toolkit'),
		'desc' => __('You can find me on <a href="http://bonfirelab.com" target="blank" >bonfirelab.com</a>. Or follow me on <a href="http://twitter.com/nackle2k10" target="blank">Twitter</a>','bon-toolkit'),
	);


	$options[] = array(
		'type' => 'section_close',
	);

	$options = apply_filters('bon_toolkit_filter_options', $options);

	return $options;
}