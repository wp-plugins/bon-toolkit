<?php
/*
Plugin Name: Bon Toolkit
Plugin URI: http://bonfirelab.com
Description: Various widgets, shortcodes and elements for your WordPress site.
Version: 1.3.2
Author: Hermanto Lim
Author URI: http://www.bonfirelab.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'BON_Toolkit' ) ) {

	class BON_Toolkit {

		/**
		 * @var string
		 */
		public $version = '1.3.2';

		/**
		 * @var string
		 */
		public $plugin_url;

		/**
		 * @var string
		 */
		public $plugin_path;

		/**
		 * @var string
		 */
		public $option_page;

		/**
		 * @var string
		 */
		public $option_name = 'bon_toolkit_options';

		/**
		 * @var string
		 */
		public $setting_page = 'bon_toolkit_plugin_options';

		/**
		 * @var string
		 */
		public $template_url;

		/**
		 * @var string
		 * Used as metabox prefix
		 */
		public $prefix = 'bon_toolkit_';

		/**
		 * @var string
		 * options token
		 */
		public $token = 'bon_toolkit';


		/**
		 * @var array
		 * 
		 */
		public $suffix = array();

		/**
		 * @var array
		 * Used as metabox prefix
		 */
		public $cpt_arr = array();

		/**
		 * @var array
		 * Suported post type for builder
		 */
		public $builder_post_types = array();

		/**
		 * Bon Toolkit Constructor.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Define version constant
			if( !defined('BON_TOOLKIT_VERSION') ) {
				define( 'BON_TOOLKIT_VERSION', $this->version );
			}
			if( !defined('BON_TOOLKIT_IMAGES') ) {
				define('BON_TOOLKIT_IMAGES', $this->plugin_url() . '/assets/images' );
			}
			if( !defined('BON_TOOLKIT_JS') ) {
				define('BON_TOOLKIT_JS', $this->plugin_url() . '/assets/js' );
			}
			if( !defined('BON_TOOLKIT_CSS') ) {
				define('BON_TOOLKIT_CSS', $this->plugin_url() . '/assets/css' );
			}
			// this used in shortcode
			if( !defined('BON_TOOLKIT_PREFIX') ) {
				define('BON_TOOLKIT_PREFIX', 'BT' );
			}

			if( !defined('BON_TOOLKIT_TINYMCE') ) {
				define('BON_TOOLKIT_TINYMCE', $this->plugin_url() . '/includes/tinymce/' );
			}

			if( !defined('BON_TOOLKIT_FONT_AWESOME') ) {
				define( 'BON_TOOLKIT_FONT_AWESOME', 'bonicons bi-' );
			}

			// Installation
			register_activation_hook( __FILE__, array( $this, 'activate' ) );

			$this->includes();

			$this->template_url	= apply_filters( 'bon_toolkit_filter_template_url', 'bon-toolkit/' );

			// Activation Hook
			add_action('admin_init', array( $this, 'admin_init' ), 1 );

			// Set priority to 10 to let the theme support from theme init first for post type features
			add_action('after_setup_theme', array( $this, 'set_cpt_features' ), 10 );

			add_action('init', array( $this, 'set_page_builder' ), 99 );

			add_action('init', array( $this, 'init') , 1 );

			add_action('admin_menu', array( $this, 'add_options_page'), 30 );

			add_action('init', array( $this, 'load_textdomain') );

			add_action('admin_enqueue_scripts', array( $this, 'load_admin_scripts') );

			add_action('wp_enqueue_scripts', array( $this, 'plugin_style') );

			$this->suffix = $this->set_builder_suffix();
			// Loaded action
			do_action( 'bon_toolkit_loaded' );

		}

		/**
		 * activate function.
		 *
		 * @access public
		 * @return void
		 */
		public function activate() {

			$options = $this->get_option_array();
			$new_val = array();

			foreach( $options as $option ) {
				if( $option['type'] == 'multicheck' || $option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'checkbox' || $option['type'] == 'select' ) {
					$new_val[$option['id']] = isset( $option['std'] ) ? $option['std'] : '';
				}
			}

			if ( get_option( $this->option_name ) === false ) {

			    // The option hasn't been added yet. We'll add it with $autoload set to 'no'.
			    $deprecated = null;
			    $autoload = 'no';
			    
			    add_option( $this->option_name, $new_val, $deprecated, $autoload );

			}

		}

		// -------------- Localization -------------- //
		public function load_textdomain() {
			load_plugin_textdomain( 'bon-toolkit', false, dirname( plugin_basename( __FILE__ ) ) . '/includes/languages/' );
		}

		// -------------- Include Required File -------------- //
		public function includes() { 

			if ( ! is_admin() || defined('DOING_AJAX') )
				$this->frontend_includes();

			// Functions
			include_once( 'bon-toolkit-core-functions.php' );
			include_once( 'bon-toolkit-twitter-functions.php' );
			include_once( 'includes/tinymce/init.php');
		}

		public function include_classes() {
			if( current_theme_supports( 'bon-poll' ) && ($this->check_options('enable_poll') === true) ) {
				include_once 'includes/classes/class-poll.php';
			}
			if( current_theme_supports( 'bon-quiz' ) && ($this->check_options('enable_quiz') === true) ) {
				include_once 'includes/classes/class-quiz.php';
			}

			include_once 'includes/classes/class-likes.php';
			include_once 'includes/classes/class-columns.php';
			include_once 'includes/classes/class-social-share.php';
		}

		// -------------- Include Required Front End File -------------- //
		public function frontend_includes() {
			include_once 'includes/shortcodes.php';
		}
		// -------------- Register plugin options -------------- //
		public function admin_init() {

			/* Registers admin stylesheets for the framework. */
			add_action( 'admin_enqueue_scripts', array($this, 'register_widget_styles'), 1 );

			/* Loads admin stylesheets for the framework. */
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_widget_styles') );


			if( ! defined( 'BON_FW_VERSION' ) || version_compare( BON_FW_VERSION, '1.0', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'fw_warning') );
			}

			register_setting( $this->setting_page , $this->option_name, array( $this, 'validate_options' ) );
		}

		/**
		 * Registers the framework's 'widget.css' stylesheet file.  The function does not load the stylesheet.  It merely
		 * registers it with WordPress.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function register_widget_styles() {
			wp_register_style( 'bon-toolkit-core-widget', trailingslashit( BON_TOOLKIT_CSS ) . "widget.css", false, '', 'screen' );
		}

		/**
		 * Loads the widget.css stylesheet for admin-related features.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function enqueue_widget_styles( $hook_suffix ) {

			/* Load admin styles if on the widgets screen and the current theme supports 'hybrid-core-widgets'. */
			if ( current_theme_supports( 'bon-core-widgets' ) && 'widgets.php' == $hook_suffix )
				wp_enqueue_style( 'bon-toolkit-core-widget' );
		}

		public function init() {

			$this->set_widget_features();
			$this->include_classes();
			
			add_action('wp_enqueue_scripts', array(&$this, 'ajax_url'));
			
		}

		// -------------- Display Error if Framework Not Available -------------- //
		public function fw_warning() {
			echo '<div class="updated">';
			echo '<p>'.__( 'BonFramework is not available in your theme or you are currently using the older version. Some features of the Bon Toolkit are disabled.', 'bon-toolkit' ).'</p>';
			echo '</div>';
		}

		// -------------- Add menu page -------------- //
		public function add_options_page() {
			//
			if(class_exists('BON_Main')) {

				$this->option_page = add_submenu_page('bon_options', __( 'Bon Toolkit', 'bon-toolkit' ), __( 'Bon Toolkit', 'bon-toolkit' ), 'manage_options', $this->token , array( &$this, 'render_form' ) );

			} else {
				$this->option_page = add_options_page('Bon Toolkit Options', 'Bon Toolkit', 'manage_options', 'bon-toolkit', array( $this, 'render_form') );
			}
		}

		// -------------- Enqueue admin scripts -------------- //
		public function load_admin_scripts($hook) {
			
			if( $hook != $this->option_page )
				return;
			
			//Register and enqueue custom admin scripts	
			wp_register_script('bon_toolkit_admin_js', trailingslashit( BON_TOOLKIT_JS ) . 'admin.js', array('jquery','media-upload','thickbox'));
			wp_enqueue_script('bon_toolkit_admin_js');

			//Register and enqueue custom admin stylesheet
			wp_register_style( 'bon_toolkit_admin_css', trailingslashit( BON_TOOLKIT_CSS ) . 'admin.css', false, '1.0.0' );
			wp_enqueue_style( 'bon_toolkit_admin_css' );

		}
		

		// -------------- Enqueue front-end scripts and styles into footer -------------- //
		public function plugin_style() {

			if( !is_admin() ) {
				$bon_toolkit_options = get_option($this->option_name);
				
				if ( ! defined( 'BON_TOOLKIT_USE_CSS' ) ) {
					define( 'BON_TOOLKIT_USE_CSS', $bon_toolkit_options['use_bon_toolkit_css'] == 'yes' ? true : false );
				}

				if ( ! defined( 'BON_TOOLKIT_USE_FONT_AWESOME' ) ) {
					define( 'BON_TOOLKIT_USE_FONT_AWESOME', $bon_toolkit_options['use_bon_toolkit_fontawesome'] == 'yes' ? true : false );
				}
				
				if ( BON_TOOLKIT_USE_CSS ) {
					wp_register_style( 'bon_toolkit', trailingslashit( BON_TOOLKIT_CSS ) . 'toolkit.css', false, '1.0.0' );
					wp_enqueue_style( 'bon_toolkit' );
				}

				if ( !class_exists( 'BON_Main') ) {
					wp_register_style( 'bon_toolkit_font_awesome', trailingslashit( BON_TOOLKIT_CSS ) . 'font-awesome.css', false, '3.2.1' );
					wp_enqueue_style( 'bon_toolkit_font_awesome' );
				}

				wp_register_style( 'bon_toolkit_font_style', trailingslashit( BON_TOOLKIT_CSS ) . 'bt-social.css', false, '1.0.0' );
				wp_enqueue_style( 'bon_toolkit_font_style' );
				

				if ( $this->check_options('enable_social_widget') === true ) {
					wp_register_style( 'bon_toolkit_icon_style', trailingslashit( BON_TOOLKIT_CSS ) . 'social-icon.css', array('bon_toolkit_font_style'), '1.0.0');
					wp_enqueue_style( 'bon_toolkit_icon_style' );
				}

				$bon_toolkit_params = array(
					'plugin_url'                       => $this->plugin_url(),
					'ajax_url'                         => $this->ajax_url(),
				);

				$protocol = is_ssl() ? 'https' : 'http';

				wp_register_script( 'googlemap3', "{$protocol}://maps.googleapis.com/maps/api/js?sensor=false", false, false, false );
				wp_register_script( 'bon-toolkit-map', trailingslashit( BON_TOOLKIT_JS ) . 'map.js', array('jquery', 'googlemap3'), '1.0.0', true );

				if( !wp_script_is( 'fitvids', 'registered' )) {
					wp_register_script( 'fitvids', trailingslashit( BON_TOOLKIT_JS ) . 'jquery.fitvids.js', array('jquery'), '1.0.2', true );
				}

				wp_register_script( 'jplayer', trailingslashit( BON_TOOLKIT_JS ) . 'jplayer/jquery.jplayer.min.js', array('jquery'), false, false );
				wp_enqueue_script( 'jplayer' );

				wp_register_script( 'bon-toolkit', trailingslashit( BON_TOOLKIT_JS ) . 'toolkit.js', array('jquery','fitvids'), '1.0.0', true );
				wp_enqueue_script( 'bon-toolkit' );

				wp_localize_script( 'bon-toolkit', 'bon_toolkit_ajax', array('url' => admin_url('admin-ajax.php')) );
			}

		}

		// -------------- Build the options form -------------- //
		public function render_form() { 

			$menus = array();
			$fields = array();
			$settings = get_option( $this->option_name );
			$options = $this->get_option_array();
			foreach ( $options as $field ) { 
				$return = $this->render_element($field);
				$menus[] = $return[1];
				$fields[] = $return[0];
			}
			

			?>
			<div class="wrap">
				<div class="icon32" id="icon-options-general"><br></div>
				<h2><?php _e('Bon Toolkit Options','bon-toolkit'); ?></h2>
				<?php settings_errors(); ?>
				<form method="post" action="options.php">
					<?php settings_fields($this->setting_page); ?>

					<!-- Settings navigation -->
					<ul class="tab-nav">
						<?php 
							foreach ( $menus as $menu ) {
								echo $menu;
							}
						?>
					</ul>
					
					<div class="bon-toolkit-admin">
						<div id="bon-toolkit-options">
						<?php 
							foreach ( $fields as $field ) {
								echo $field;
							}
						?>
							
						</div><!-- toolkit options -->
					</div><!-- toolkit admin -->
					
					<div id="submit-options">
						<div class="restore"></div>
						
						<?php echo submit_button('Save Changes'); ?>
					</div>
				</form>
			</div><!-- wrap -->
			<?php	
		}

		public function get_option_array() {
			include_once('includes/options.php');

			return $options = bon_toolkit_set_options();
		}

		// -------------- Render Option Element -------------- //
		public function render_element( $field ) {
			if ( ! ( $field || is_array( $field ) ) )
				return;
			$menu = '';
			$o = '';

			global $allowedtags;
			
			$settings = get_option( $this->option_name );
			
			// get field data
			$type = isset( $field['type'] ) ? $field['type'] : null;
			$label = isset( $field['label'] ) ? $field['label'] : null;
			$desc = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : null;
			$options = isset( $field['options'] ) ? $field['options'] : null;
			$std = isset( $field['std'] ) ? $field['std'] : null;
			$class = isset( $field['class'] ) ? $field['class'] : null;

			$meta = null;
			if(isset($field['id'])) {
				if(isset($settings[$field['id']])) {
					$meta = $settings[$field['id']];
				}
			}
			// the id and name for each field
			$id = isset( $field['id'] ) ? $field['id'] : null;

			$name = isset( $field['id'] ) ? $field['id'] : null;
			$toggle = isset( $field['toggle'] ) ? $field['toggle'] : null;


			if((!isset($meta)) && isset($std)) {
				$meta = '';
			}
			


			switch ($type) {
				
				case 'section' :

					$menu .= '<li><a id="nav-'.esc_attr($id).'" href="#">'.$label.'</a></li>';

					$o .= '<div id="'. esc_attr( $id ) . '" class="box '.esc_attr($class).'">';

				break; 

				case 'select' :
					$o .= '
					<div class="setting '.esc_attr($class).'" data-toggle="' . ((isset($toggle)) ? 'setting-' . esc_attr($toggle) : '') .'">
						<strong>'. $label .'</strong>
						
						<div class="options">
							<select name="' .$this->option_name . '['  . esc_attr( $name ) . ']" id="' . esc_attr( $id ) . '">'; 
								foreach ( $options as $val => $option ) {
									$o .= '<option' . selected( $meta, $val, false ) . ' value="' . $val . '">' . $option . '</option>';
								}
								$o .= '</select>
								' . $desc . '	
						</div>
					</div><!-- setting -->';
				break; 

				case 'checkbox' :
					$o .= '
					<div id="setting-'.esc_attr($id).'" class="setting '.esc_attr($class).'" data-toggle="' . ((isset($toggle)) ? 'setting-' . esc_attr($toggle) : '') .'" >
						<strong>'. $label .'</strong>
						
						<div class="options">
							<input type="checkbox" class="bon-input" name="'.$this->option_name . '[' . esc_attr( $name ) . ']" id="' . esc_attr( $id ) . '" ' . checked( $meta, true, false ) . ' value="1" />
									<label for="' . esc_attr( $id ) . '">' . $desc . '</label>	
						</div>
					</div><!-- setting -->';
				break;

				case 'text' :
					$o .= '
					<div id="setting-'.esc_attr($id).'" class="setting '.esc_attr($class).'" data-toggle="' . ((isset($toggle)) ? 'setting-' . esc_attr($toggle) : '') .'" >
						<strong>'. $label .'</strong>
						
						<div class="options">
							<input type="text" class="bon-input" name="'.$this->option_name . '[' . esc_attr( $name ) . ']" id="' . esc_attr( $id ) . '" value="'.$meta.'" />
									<br /><br />' . $desc . '
						</div>
					</div><!-- setting -->';
				break;

				case 'multicheck' :

					$o .= '<div id="setting-'.esc_attr($id).'" class="setting '.esc_attr($class).'" data-toggle="' . ((isset($toggle)) ? 'setting-' . esc_attr($toggle) : '') .'" >';
					$o .= '<strong>'. $label .'</strong><div class="options">';

					$o .= '<ul>';
					foreach ( $options as $val => $option ) {
						$c_name = $this->option_name . '[' . esc_attr( $name ) .'][]';
						$checked = '';
						if( $meta && is_array( $meta ) ) {
							$checked = checked( in_array( $val, $meta ), 1, false);
						}
						$o .= '<li class="opt"><input type="checkbox" value="'.$val.'" name="' . $c_name . '" id="' . esc_attr( $id ) . '-' . $val . '"' . $checked . ' /> 
								<label for="' . esc_attr( $id ) . '-' . $val . '">' . $option . '</label></li>';
					}
					$o .= '</ul><br/>' . $desc .'</div></div>'; 

				break;

				case 'section_close':
					$o .= '</div>';
				break;

				case 'help':
					$o .= '<div class="setting help">';
					$o .= '<h3>'.esc_attr($label).'</h3>';
					$o .= ( isset( $field['desc'] ) ? '<div>' . $field['desc'] . '</div>' : null);
					$o .= '</div>';
				break;

				case 'info' :
					$o .= '<p class="settings-info">'.$std.'</p>';
				break;
			}

			return array($o, $menu);
		}


		// -------------- Sanitize output -------------- //
		public function validate_options($input) {
			return $input;
		}

		// -------------- Setting Widget -------------- //
		public function set_widget_features() {

			$widget_arr = array(
				'bon-toolkit-dribbble-widget' => array('key_option' => 'enable_dribbble_widget', 'file' => 'widget-dribbble'),
				'bon-toolkit-flickr-widget' => array('key_option' => 'enable_flickr_widget', 'file' => 'widget-flickr'),
				'bon-toolkit-twitter-widget' => array('key_option' => 'enable_twitter_widget', 'file' => 'widget-twitter'),
				'bon-toolkit-social-widget' => array('key_option' => 'enable_social_widget', 'file' => 'widget-social'),
				'bon-toolkit-video-widget' => array('key_option' => 'enable_video_widget', 'file' => 'widget-video'),
				'bon-toolkit-contact-form-widget' => array('key_option' => 'enable_contactform_widget', 'file' => 'widget-contactform'),
				'bon-toolkit-post-widget' => array('key_option' => 'enable_posts_widget', 'file' => 'widget-posts'),
			);

			$widget_arr = apply_filters( 'bon_toolkit_filter_widget_opt', $widget_arr );
			
			foreach($widget_arr as $key => $value) {
				if( $this->check_options( esc_attr( $value['key_option'] ) ) === true ) {
					include_once 'includes/widgets/' . esc_attr($value['file']) . '.php';
				} else if( $value['key_option'] == 'no_key') {
					include_once 'includes/widgets/' . esc_attr($value['file']) . '.php';
				}
			}

			$ad_arr = array(
				'bon-toolkit-archives-widget' => array('key_option' => 'enable_advanced_archives', 'file' => 'widget-archives'),
				'bon-toolkit-authors-widget' => array('key_option' => 'enable_advanced_authors', 'file' => 'widget-authors'),
				'bon-toolkit-calendar-widget' => array('key_option' => 'enable_advanced_calendar', 'file' => 'widget-calendar'),
				'bon-toolkit-categories-widget' => array('key_option' => 'enable_advanced_categories', 'file' => 'widget-categories'),
				'bon-toolkit-nav-menu-widget' => array('key_option' => 'enable_advanced_nav_menu', 'file' => 'widget-nav-menu'),
				'bon-toolkit-pages-widget' => array('key_option' => 'enable_advanced_pages', 'file' => 'widget-pages'),
				'bon-toolkit-search-widget' => array('key_option' => 'enable_advanced_search', 'file' => 'widget-search'),
				'bon-toolkit-tags-widget' => array('key_option' => 'enable_advanced_tags', 'file' => 'widget-tags'),
			);

			foreach($ad_arr as $key => $value) {
				if( $this->check_options( esc_attr( $value['key_option'] ) ) === true && current_theme_supports( 'bon-advanced-widget' ) ) {
					include_once 'includes/widgets/' . esc_attr($value['file']) . '.php';
				}
			}
		}

		
		// -------------- Setting Custom Post Type -------------- //
		public function set_cpt_features() {

			$this->cpt_arr = array(
				'bon-featured-slider' => array('key_option' => 'enable_featured_slider', 'file' => 'slider'),
				'bon-poll' =>  array('key_option' => 'enable_poll', 'file' => 'poll'),
				'bon-portfolio' =>  array('key_option' => 'enable_portfolio', 'file' => 'portfolio'),
				'bon-review' =>  array('key_option' => 'enable_review', 'file' => 'review'),
				'bon-testimonial' =>  array('key_option' => 'enable_testimonial', 'file' => 'testimonial'),
				'bon-quiz' =>  array('key_option' => 'enable_quiz', 'file' => 'quiz'),
			);

			if( !class_exists('BON_Main') || !class_exists('BON_Cpt') || !class_exists('BON_Metabox') ) {
				// class is needed in order to create the CPT and MetaBox
				return;

			} else {
				
				$this->cpt_arr = apply_filters( 'bon_toolkit_filter_cpt_opt', $this->cpt_arr );

				foreach($this->cpt_arr as $key => $value) {
					if( current_theme_supports( $key ) && $this->check_options( esc_attr( $value['key_option'] ) ) === true ) {
						include_once 'includes/posttypes/' . esc_attr($value['file']) . '.php';
					}
				}
				
			}

		} // end

		public function check_options($options) {

			$bon_toolkit_options = get_option($this->option_name);

			if( isset($bon_toolkit_options[$options]) && $bon_toolkit_options[$options] == true) {
				return true;
			}

			return false;

		}

		// -------------- Define Plugin Url -------------- //
		public function plugin_url() {
			if ( $this->plugin_url ) return $this->plugin_url;
			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		// -------------- Define Plugin Path -------------- //
		public function plugin_path() {
			if ( $this->plugin_path ) return $this->plugin_path;

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		// -------------- Get Ajax Url -------------- //
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		public function set_page_builder() {

			if( current_theme_supports('bon-page-builder') && class_exists('BON_Main') ) {

				$bon_toolkit_options = get_option($this->option_name);

				$builder_options = isset( $bon_toolkit_options['page_builder_post_type'] ) ? $bon_toolkit_options['page_builder_post_type'] : '';

				$builder_support = get_theme_support( 'bon-page-builder' );
				if( is_array( $builder_options ) && !empty( $builder_options ) ) {

					$this->builder_post_types = $builder_options;

				} else {

					if( !empty( $builder_support[0] ) ) {
						$this->builder_post_types = array_merge( $this->builder_post_types, $builder_support[0] );
					}

					$post_types = get_post_types( array( 'public' => true ) );

					foreach ( $post_types as $type ) {

						$post_type_supports = get_all_post_type_supports( $type );

						if( !empty( $post_type_supports ) && array_key_exists( 'bon-page-builder', $post_type_supports ) && !in_array( $type, $this->builder_post_types ) ) {
							$this->builder_post_types[] = $type;
						}

					}
				}

				include_once 'includes/builder/builder-options.php';
				include_once 'includes/builder/builder.php';
				include_once 'includes/builder/builder-interface.php';
			}
			
		}

		public function set_builder_suffix() {
			
			$suffix = apply_filters('bon_tookit_builder_suffix_filters', array(
		        'post' => 'builder_post_',
		        'contact_form' => 'builder_contact_form_',
		        'tab' => 'builder_tab_',
		        'toggle' => 'builder_toggle_',
		        'call_to_action' => 'builder_call_to_action_',
		        'post_content' => 'builder_post_content_',
		        'service' => 'builder_service_',
		        'text_block' => 'builder_text_block_',
		        'divider' => 'builder_divider_',
		        'alert' => 'builder_alert_',
		        'video' => 'builder_video_',
		        'map' => 'builder_map_',
		        'audio' => 'builder_audio_',
		        'listing' => 'builder_listing_',
		        'portfolio' => 'builder_portfolio_',
		        'widget' => 'builder_widget_',
		        'twitter' => 'builder_twitter_',
		        'dribbble' => 'builder_dribbble_',
		        'flickr' => 'builder_flickr_',
		        'menu' => 'builder_menu_',
		        'archive' => 'builder_archive_',
		        'category' => 'builder_category_',
		        'shop' => 'builder_shop_',
		        'calendar' => 'builder_calendar_',
		        'rss' => 'builder_rss_',
		        'author' => 'builder_author_',
		        'page' => 'builder_page_',
		        'image_block' => 'builder_image_block_'
		    ));

			return $suffix;
		}

		function flush_rewrite() {
		    // First, we "add" the custom post type via the above written function.
		    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
		    // They are only referenced in the post_type column with a post entry, 
		    // when you add a post of this CPT.
		    $this->set_cpt_features();

		    // ATTENTION: This is *only* done during plugin activation hook in this example!
		    // You should *NEVER EVER* do this on every page load!!
		    flush_rewrite_rules();
		}

	} // end class

	/**
	 * Init bon toolkit class
	 */
	$GLOBALS['bontoolkit'] = new BON_Toolkit();

} // end ! class exists