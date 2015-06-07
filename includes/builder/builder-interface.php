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
	public $builder_options = array();

	/**
	 * @var array
	 */
	public $builder_metas;

    /**
     * @var array
     */
   // public $supported_post_type = array();


	/**
	 * Sets up our actions/filters.
	 *
	 * @return void
	 */
	public function __construct() {
        global $bontoolkit;

		/* Register shortcodes on 'init'. */
        $this->builder_options = bon_toolkit_get_builder_options();

       // $this->supported_post_type = $bontoolkit->builder_post_types;
		add_filter('the_content', array( &$this, 'init' ) );

	}

	/**
	 * Render the content
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function init($content) {

		global $post, $bon, $bontoolkit;

        if( !is_object( $post ) ) {
            return $content;
        } 
        
		if( !in_array( $post->post_type, $bontoolkit->builder_post_types ) ) {
			return $content;
		}

		$this->builder_metas = get_post_meta( $post->ID, $this->builder_options['name'], true );
		
		if( !$this->builder_metas || empty($this->builder_metas) || !is_array($this->builder_metas) ) {
			return $content;
		}

        if( !is_singular() ) {
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
		
		$use_placement_class = apply_filters('bon_toolkit_builder_use_placement_class', true);

		foreach($this->builder_metas as $meta) {

			foreach($meta as $key => $value) {

                $row_classes = apply_filters('bon_tookit_builder_render_row_class', array('row'), $key, $value );

				$value['classes'] = array();
                $value['column_classes'] = apply_filters('bon_toolkit_builder_render_column_class', array($value['default_size']) );
				$value['classes'] = array_merge($value['column_classes']);
				$value['classes'][] = 'bon-builder-element-'. str_replace('_', '', $key);

                if( isset( $value['element_class'] ) && !empty($value['element_class'] ) ) {
                    $element_class = explode( ' ', $value['element_class']);
                    $value['classes'] = array_merge($value['classes'], $element_class);
                }

                $value['callback'] = isset($this->builder_options['elements'][$key]['callback']) ? $this->builder_options['elements'][$key]['callback'] : '';
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
            if( $name != 'builder_options' && $name != 'builder_metas' ) {
                $this->$name = $default;
            }
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

		//global $bonbuilder;

		if(empty($type) || empty($value)) {
			return;
		}

		$column_classes = join( ' ', array_map( 'sanitize_html_class', array_unique( $value['classes'] ) ) );

		$style = '';
		if (!empty($value['margin'])) {
            $style = 'style="margin-bottom:' . $value['margin'] . 'px"';
        }

		$o = '<div class="'.$column_classes.'" '.$style.'>';
        if( isset( $value['callback'] ) && !empty( $value['callback'] ) && function_exists( $value['callback'] ) ) {
            $o .= call_user_func_array( $value['callback'] , array($value) );
        } else if(method_exists($this, "render_{$type}")) {
			$o .= call_user_func_array(array($this, "render_{$type}"), array($value));
		} else {
			$o .= apply_filters('bon_tookit_builder_render_element', $type, $value);
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
                        'size' => 'medium',
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
        $o .= do_shortcode('[bt-tabs style="'.$value['content_style'].'" direction="' . $value['direction'] . '" color="' . $value['color'] . '"]' . $tab . '[/bt-tabs]');
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

        $value['icon_class'] = $this->process_icon( $value['icon_class'] );


        extract($value);

        $icon            = '<div class="' . $value['icon_style'] . '-icon icon-wrapper">'.( isset( $link ) && ( $link != '' ) ? '<a href="'.esc_url( $link ).'" title="'.$title.'">' : '' ).'<i class="' . $value['icon_class'] . ' icon-anim-' . $value['icon_animation'] . '"></i>'.( isset( $link ) ? '</a>' : '' ).'</div>';
        $title           = '<h2>'.( isset( $link ) && ( $link != '' ) ? '<a href="'.esc_url( $link ).'" title="'.$title.'">' : '' ) . $value['title'] .( isset( $link ) ? '</a>' : '' ). '</h2>';
        $service_content = '<div class="service-content">';
        $service_content .= $title;
        $service_content .= '<div class="service-summary">';
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
    public function render_call_to_action($value) {

    	$o = apply_filters('bon_toolkit_builder_render_call_to_action_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}

        $value['button_icon'] = $this->process_icon( $value['button_icon'] );
        
        extract($value);
       	
        $target = isset( $value['button_target'] ) ? $value['button_target'] : '';

        $icon = '';
        if ($value['button_icon']) {
            $icon = '<i class="icon ' . $value['button_icon'] . '"></i>';
        }
        $o .= '<div class="panel callaction"><div class="panel-content">';
        $o .= '<h2 class="action-title">' . $value['title'] . '</h2>';
        $o .= '<h3 class="action-content subheader">' . $value['subtitle'] . '</h3>';
        $o .= '</div>';
        $o .= '<div class="panel-button"><a target="'.$target.'" href="' . esc_url( $value['button_link'] ) . '" title="' . $value['button_text'] . '">' . $icon . '<span>' . $value['button_text'] . '</span></a></div>';
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

        if( !isset( $color ) ) {
            $color = '';
        }
        
        $o = bon_toolkit_get_contact_form($email, $color);

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
        $o .= wptexturize( wpautop( do_shortcode( get_the_content( $post->ID ) ) ) );
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
        $o .=  wptexturize( wpautop( do_shortcode( $content ) ) );
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

        if( !isset( $target ) ) { $target = '_blank'; }

        $img = '<img src="'.$src.'" alt="'. ( isset($alt) ? $alt : $header ) .'" />';

        if(!empty($link)) {
        	$o .= '<a href="'.esc_url( $link ).'" title="'.$header.'" target="'.$target.'">'.$img.'</a>';
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

        $o = apply_filters('bon_toolkit_builder_render_header_output', '', $type, $header);

       	if( $o != '' ) {
       		return $o;
       	}

        if( !is_front_page() && $type == 'post_content' ) {
            $o = '<header class="bon-builder-element-header bon-builder-' . $type . '-header"><h1>' . $header . '</h1></header>';
        } else {

            $o = '<header class="bon-builder-element-header bon-builder-' . $type . '-header"><h3>' . $header . '</h3></header>';
        }

        return $o;
    }

    /**
	 * Rendering Widget Element.
	 *
	 * @since  1.1.0
	 * @param string $value
	 * @access public
	 * @return string
	 */
    public function render_widget($value) {

    	$o = apply_filters('bon_toolkit_builder_render_widget_output', '', $value);

       	if( $o != '' ) {
       		return $o;
       	}
       	
        if ( is_active_sidebar( $value['widget_id'] ) ) {
	        ob_start();
			dynamic_sidebar($value['widget_id']);
			$o = ob_get_clean();
		} else {
			$o = '<p>'._e('Please activate some widget','bon-toolkit').'</p>';
		}

        return $o;
    }

    public function process_icon( $icon ) {

        if( !function_exists( 'bon_icon_select_field') ) 
            return $icon;

        if( substr( $icon, 0, 4 ) == 'awe-' ) {
            $icon = str_replace( 'awe-', 'bi-', $icon );
        }

        if( substr( $icon, 0, 3 ) == 'bi-' && strpos( $icon , 'bonicons') == false ) {
            $icon = 'bonicons ' . $icon;
        }

        return $icon;
    }

}

$GLOBALS['bonbuilder'] = new BON_Toolkit_Builder_Interface();

?>