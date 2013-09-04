<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * BON_Toolkit_Builder_Interface
 *
 * @package BON_Toolkit
 * @author Hermanto Lim
 * @since 1.0.0
 *
 */
class BON_Toolkit_Builder_Interface {

	/**
	 * The current total number of columns in the grid.
	 *
	 * @var int
	 */
	public $span = 0;

	/**
	 *
	 * @var bool
	 */
	public $is_first = true;

	/**
	 *
	 * @var bool
	 */
	public $is_last = false;

	/**
	 * Allowed grids 12 columns.
	 *
	 * @var int
	 */
	public $allowed_grid = 12;

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var array
	 */
	public $builder_options;

	/**
	 * @var array
	 */
	public $builder_metas;


	/**
	 * Sets up our actions/filters.
	 *
	 * @return void
	 */
	public function __construct() {

		/* Register shortcodes on 'init'. */
		add_filter('the_content', array(&$this, 'init'));

	}

	/**
	 * Registers the [column] shortcode.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function init($content) {

		global $post, $bon, $bontoolkit;

		if($post->post_type != 'page') {
			return $content;
		}

		$this->builder_options = bon_toolkit_get_builder_options();

		$this->builder_metas = get_post_meta( $post->ID, $this->builder_options['name'], true );
		
		if( !$this->builder_metas || empty($this->builder_metas) || !is_array($this->builder_metas) ) {
			return $content;
		}

		return $this->render();
	}

	/**
	 * Returns the content of the column shortcode.
	 *
	 * @return string
	 */
	public function render() {

		/* Set up the default variables. */
		$output = '';
		$row_classes = apply_filters('bon_tookit_builder_render_row_class', array('row'));
		$use_placement_class = apply_filters('bon_toolkit_builder_use_placement_class', true);

		$len = count($this->builder_metas);
		
		foreach($this->builder_metas as $meta) {
			
			foreach($meta as $key => $value) {

				$value['classes'] = array();

				$value['column_classes'] = apply_filters('bon_toolkit_builder_render_column_class', array($value['default_size']) );
				$value['classes'] = array_merge($value['column_classes']);
				$value['classes'][] = 'bon-builder-element-'. str_replace('_', '', $key);
				
				$element_size = intval(trim($value['default_size'], 'span'));

				$this->span = $this->span + $element_size;


				/* If the $span property is greater than (shouldn't be) or equal to the $allowed_grid property. */
				if ( $this->span >= $this->allowed_grid ) {

					/* Set the $is_last property to true. */
					$this->is_last = true;

					if( $use_placement_class ) {
						$value['classes'][] = apply_filters('bon_toolkit_builder_last_column_class', 'column-last');
					}

				}
				/* If this is the first column. */
				if ( $this->is_first ) {

					if( $use_placement_class ) {
						$value['classes'][] = apply_filters('bon_toolkit_builder_first_column_class', 'column-first');
					}

					/* Row classes. */
					$row_class = join( ' ', array_map( 'sanitize_html_class', array_unique( $row_classes ) ) );
					
					/* Open a wrapper <div> to contain the columns. */
					$output .= '<div class="' . $row_class . '">';

					/* Set the $is_first property back to false. */
					$this->is_first = false;
				}
				// applying filter to the output in case theme want to have difference output
				// default output is filtered in the bon-toolkit-core-functions.php
				$output .= $this->render_element($key, $value);

				
				/* If this is the last column. */
				if ( $this->is_last ) {

					/* Close the wrapper. */

					$output .= '</div>';
					
					/* Reset the properties that have been changed. */
					$this->reset();
				} 

			}

		}

		return $output;
		
	}

	/**
	 * Resets the properties to their original states.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function reset() {

		foreach ( get_class_vars( __CLASS__ ) as $name => $default ) {
			$this->$name = $default;
		}
			
	}

	/**
	 * Rendering Element.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render_element($type, $value) {

		global $bonbuilder;

		if(empty($type) || empty($value)) {
			return;
		}
		$column_classes = join( ' ', array_map( 'sanitize_html_class', array_unique( $value['classes'] ) ) );

		$style = '';
		if (!empty($value['margin'])) {
            $style = 'style="margin-bottom:' . $value['margin'] . 'px"';
        }

		$o = '<div class="'.$column_classes.'" '.$style.'>';

		if ($type == 'post') {
		    $o.= $this->render_post($value);
		} else if ($type == 'call_to_action') {
		    $o.= $this->render_calltoaction($value);
		} else if ($type == 'toggle') {
		    $o.= $this->render_toggle($value);
		} else if ($type == 'tab') {
		    $o.= $this->render_tab($value);
		} else if ($type == 'service') {
		    $o.= $this->render_service($value);
		} else if ($type == 'post_content') {
		    $o.= $this->render_post_content($value);
		} else if ($type == 'text_block') {
		    $o.= $this->render_text_block($value);
		} else if ($type == 'contact_form') {
		    $o.= $this->render_contact_form($value);
		} else if ($type == 'divider') {
		    $o.= $this->render_divider($value);
		} else if ($type == 'alert') {
		    $o.= $this->render_alert($value);
		} else if ($type == 'twitter') {
		    $o.= $this->render_twitter($value);
		} else if ($type == 'dribbble') {
		    $o.= $this->render_dribbble($value);
		} else if ($type == 'flickr') {
		    $o.= $this->render_flickr($value);
		} else if ($type == 'image_block') {
		    $o.= $this->render_image_block($value);
		} else if ($type == 'video') {
		    $o.= $this->render_video($value);
		} else if ($type == 'map') {
		    $o.= $this->render_map($value);
		}else {
			// if type was defined by the theme use the filter to output the type
		    $o.= apply_filters('bon_tookit_builder_render_element', $type, $value);
		}

		$o .= '</div>';

		return $o;		
	}

	/**
	 * Rendering Post Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
	public function render_post($value) {

		$o = apply_filters('bon_toolkit_builder_render_post_output', '', $value);
        
        if($o != '') {
        	return $o;
        }

        extract($value);
        

        $o .= $this->render_header('post', $header);
        $post_size = apply_filters('bon_toolkit_builder_render_column_class', 'span3');
        $post_col  = '';
        if (!empty($size)) {
            switch ($size) {
                case '1-col':
                    $post_size = apply_filters('bon_toolkit_builder_render_column_class', 'span12');
                    $post_col  = 1;
                    break;
                case '2-col':
                    $post_size = apply_filters('bon_toolkit_builder_render_column_class', 'span6');
                    $post_col  = 2;
                    break;
                case '3-col':
                    $post_size = apply_filters('bon_toolkit_builder_render_column_class', 'span4');
                    $post_col  = 3;
                    break;
                case '4-col':
                    $post_size = apply_filters('bon_toolkit_builder_render_column_class', 'span3');
                    $post_col  = 4;
                    break;
            }
        }
        $loop = array(
            'post_type' => 'post',
            'posts_per_page' => $numberposts,
            'ignore_sticky_posts' => true,
            'cat' => $category,
            'order' => $order,
            'orderby' => $orderby
        );
        query_posts($loop);
        global $wp_query;
        if (have_posts()):
            $i = 1;
            while (have_posts()):
                the_post();
                $temp_title = the_title('<h4 class="entry-title">', '</h4>', false);
                $temp_link  = get_permalink();
                $temp_id    = get_the_ID();
                $temp_ex    = bon_toolkit_the_excerpt($excerpt_length);
                if ($i == 1) {
                    $o .= '<div class="entries-container row">';
                }
                $o .= '<article id="post-' . $temp_id . '" class="post-entry ' . $post_size . '">';
                $o .= '<div class="entry-content">';
                if (function_exists('apply_atomic_shortcode')) {
                    $o .= apply_atomic_shortcode('entry_title', $temp_title);
                } else {
                    $o .= $temp_title;
                }
                $o .= '<div class="entry-summary">';
                $o .= $temp_ex;
                $o .= '</div>';
                $o .= apply_atomic_shortcode('entry-permalink', '[entry-permalink class="button radius small flat"]');
                $o .= '</div><div class="entry-image">';
                if ($show_thumbnail == 1) {
                    $o .= (current_theme_supports('get-the-image')) ? get_the_image(array(
                        'size' => 'blog_small',
                        'echo' => false
                    )) : '';
                }
                $o .= '</div></article>';
                $i++;
                if ($i > $post_col) {
                    $i = 1;
                    $o .= '</div>';
                }
            endwhile;
            if ($i > 1) {
                $o .= '</div>';
            }
        endif;
        wp_reset_query();

        return $o;
    }

    /**
	 * Rendering Tab Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_tab($value) {

    	$o = apply_filters('bon_toolkit_builder_render_tab_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);
        $tab = '';
       	
       	
        foreach ($value['repeat_element'] as $child_element) {
            $tab .= '[bt-tab title="' . $child_element['repeat_title'] . '"]' . $child_element['repeat_content'] . '[/bt-tab]';
        }
        $o .= $this->render_header('tab', $header);
        $o .= do_shortcode('[bt-tabs direction="' . $value['direction'] . '" color="' . $value['color'] . '"]' . $tab . '[/bt-tabs]');
        return $o;
    }

    /**
	 * Rendering Toggle Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_toggle($value) {


       	$o = apply_filters('bon_toolkit_builder_render_toggle_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);

        $tab = '';
        foreach ($value['repeat_element'] as $child_element) {
            $tab .= '[bt-toggle title="' . $child_element['repeat_title'] . '"]' . $child_element['repeat_content'] . '[/bt-toggle]';
        }
        $o .= $this->render_header('toggle', $header);
        $o .= do_shortcode('[bt-toggles color="orange"]' . $tab . '[/bt-toggles]');
        return $o;
    }

    /**
	 * Rendering Service Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_service($value) {

    	$o = apply_filters('bon_toolkit_builder_render_service_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);
        
        $icon            = '<div class="' . $value['icon_style'] . '-icon icon-wrapper"><i class="' . $value['icon_class'] . ' icon-anim-' . $value['icon_animation'] . '"></i></div>';
        $title           = '<h2>' . $value['title'] . '</h2>';
        $service_content = '<div class="service-content">';
        $service_content .= $title;
        $service_content .= '<div class="servce-summary">';
        $service_content .= $value['content'];
        $service_content .= '</div>';
        $service_content .= '</div>';

        $o .= '<div class="service-column">';
        $o .= $icon;
        $o .= $service_content;
        $o .= '</div>';

        return $o;
    }

    /**
	 * Rendering Call To Action Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_calltoaction($value) {

    	$o = apply_filters('bon_toolkit_builder_render_calltoaction_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);
       	

        $icon = '';
        if ($value['button_icon']) {
            $icon = '<i class="icon ' . $value['button_icon'] . '"></i>';
        }
        $o .= '<div class="panel callaction"><div class="panel-content">';
        $o .= '<h1 class="action-title">' . $value['title'] . '</h1>';
        $o .= '<h2 class="action-content subheader">' . $value['subtitle'] . '</h2>';
        $o .= '</div>';
        $o .= '<div class="panel-button"><a href="' . $value['button_link'] . '" title="' . $value['button_text'] . '">' . $icon . '<span>' . $value['button_text'] . '</span></a></div>';
        $o .= '</div>';
        return $o;
    }

    /**
	 * Rendering Contact Form Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_contact_form($value) {

    	$o = apply_filters('bon_toolkit_builder_render_contact_form_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);
        
        $o .= '<form class="bon-builder-contact-forms"><div class="contact-form-wrapper">';

        $o .= '<div class="contact-form-input">';
        $o .= '<label for="name">'.__('Your Name', 'bon-toolkit').'</label>';
        $o .= '<input type="text" value="" name="name" class="name required" />';
        $o .= '<div class="contact-form-error">'.__('Please enter your name.','bon-toolkit').'</div>';
        $o .= '</div>';

        $o .= '<div class="contact-form-input">';
        $o .= '<label for="email-address">'.__('Email Address', 'bon-toolkit').'</label>';
        $o .= '<input type="email" value="" name="email" class="email-address required" />';
        $o .= '<div class="contact-form-error">'.__('Please enter valid email address.','bon-toolkit').'</div>';
        $o .= '</div>';

        $o .= '<div class="contact-form-input">';
        $o .= '<label for="subject">'.__('Subject', 'bon-toolkit').'</label>';
        $o .= '<input type="text" value="" name="subject" class="subject" />';
        $o .= '</div>';

        $o .= '<div class="contact-form-input">';
        $o .= '<label for="messages">'.__('Your Messages', 'bon-toolkit').'</label>';
        $o .= '<textarea name="messages" class="messages required"></textarea>';
        $o .= '<div class="contact-form-error">'.__('Please enter your messages.','bon-toolkit').'</div>';
        $o .= '</div>';

        $o .= '<input type="hidden" name="receiver" value="'.$email.'" />';

        $o .= '<div class="contact-form-input">';
        $o .= '<button type="submit" class="contact-form-submit bon-toolkit-button round-corner blue flat" name="submit">'.__('Send Message','bon-toolkit').'</button>';
        $o .= '<span class="contact-form-ajax-loader"><img src="'.trailingslashit( BON_TOOLKIT_IMAGES ).'loader.gif" alt="loading..." /></span>';
        $o .= '</div>';

        $o .= '</div><div class="sending-result"><div class="green bon-toolkit-alert"></div></div></form>';

        return $o;
    }

    /**
	 * Rendering Post Content Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_post_content($value) {

    	$o = apply_filters('bon_toolkit_builder_render_post_content_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);
        
        global $post;

        $o .= $this->render_header('post_content', $header);
        $o .= '<article class="post-content">';
        $o .= wptexturize(wpautop(get_the_content($post->ID)));
        $o .= '</article>';
        return $o;
    }

    /**
	 * Rendering Text Block Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_text_block($value) {

    	$o = apply_filters('bon_toolkit_builder_render_text_block_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);
        
        $o .= $this->render_header('text_block', $header);
        $o .= '<div class="text-block-content">';
        $o .= $content;
        $o .= '</div>';
        return $o;
    }

    /**
	 * Rendering Image Block Element.
	 *
	 * @since  1.0.2
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_image_block($value) {

    	$o = apply_filters('bon_toolkit_builder_render_image_block_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

       	$o = '';
        extract($value);
        
        $o .= $this->render_header('image_block', $header);
        $o .= '<div class="image-block-content">';

        $img = '<img src="'.$src.'" alt="'.$header.'" />';

        if(!empty($link)) {
        	$o .= '<a href="'.$link.'" title="'.$header.'" target="blank">'.$img.'</a>';
        } else {
        	$o .= $img;
        }
        $o .= '</div>';
        return $o;
    }

    /**
	 * Rendering Divider Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_divider($value) {

    	$o = apply_filters('bon_toolkit_builder_render_divider_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);
        
        if (!$header) {
            $o .= '<hr class="divider-bold divider-1" />';
        } else {
            $o .= '<div class="hr hr-text"><div class="custom-hr-text">' . $header . '</div></div>';
        }
        return $o;
    }

    /**
	 * Rendering Video Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_video($value) {
      	
      	$o = apply_filters('bon_toolkit_builder_render_video_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

      	$args = array(
      		'embed' => $value['embed'],
      		'm4v' => $value['m4v'],
      		'ogv' => $value['ogv'],
      		'poster' => $value['poster'],
      		'desc' => $value['desc'],
      		'echo' => false,
      	);

        $o .= $this->render_header('video', $value['title']);
        $o .= bon_toolkit_video($args);

        return $o;
    }

    /**
	 * Rendering Twitter Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_twitter($value) {

    	$o = apply_filters('bon_toolkit_builder_render_twitter_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

    	$args = array(
    		'username' => $value['username'],
    		'tweettext' => $value['tweettext'],
    		'count' => $value['postcount'],
    		'echo' => false,
    	);

    	$o .= $this->render_header('twitter', $value['title']);
        $o .= '<div class="twitter-widget">' . bon_toolkit_twitter($args) . '</div>';

        return $o;
    }

    /**
	 * Rendering Flickr Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_flickr($value) {

    	$o = apply_filters('bon_toolkit_builder_render_flickr_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}


    	$o .= $this->render_header('flickr', $value['flickr_title']);
        
        $o .= '<div class="flickr-widget">';
			
		$o .= '<script type="text/javascript" src="http://www.flickr.com/badge_code_v2.gne?count=' . $value['flickr_count'] . '&amp;display=latest&amp;size=s&amp;layout=x&amp;source=user&amp;user='.$value['flickr_id'].'"></script>';

		$o .= '</div>';

        return $o;
    }

    /**
	 * Rendering Alert Element.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_alert($value) {

    	$o = apply_filters('bon_toolkit_builder_render_alert_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        extract($value);
        
        $o .= do_shortcode('[bt-alert color="' . $color . '"]' . $content . '[/bt-alert]');

        return $o;
    }

    /**
	 * Rendering Map.
	 *
	 * @since  1.0.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_map($value) {

    	$o = apply_filters('bon_toolkit_builder_render_map_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}


        extract($value);
       
        if(empty($height)) {
        	$height = '400px';
        } else {
        	$height = absint( $height ) . 'px';
        }
        $o .= do_shortcode('[bt-map color="' . $color . '" latitude="'.$latitude.'" longitude="'.$longitude.'" zoom="'.$zoom.'" height="'.$height.'"]');
        return $o;
    }

    /**
	 * Rendering Header Element.
	 *
	 * @since  1.0.0
	 * @param string $type
	 * @param string $header
	 * @access public
	 * @return string
	 */
    public function render_header($type, $header) {
        if (empty($header) || empty($type)) {
            return;
        }

        $o = apply_filters('bon_toolkit_builder_render_header_output', $type, $header);

       	if( $o != '' ) {
       		return $o;
       	}

        $o .= '<header class="bon-builder-element-header bon-builder-' . $type . '-header"><h3>' . $header . '</h3></header>';
        return $o;
    }

}

$GLOBALS['bonbuilder'] = new BON_Toolkit_Builder_Interface();

?>