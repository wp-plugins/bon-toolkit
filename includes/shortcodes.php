<?php
class BON_Toolkit_Shortcodes {

    function __construct() {
        add_action( 'init', array( $this, 'add_shortcodes' ) );
        add_filter( 'init', array( $this, 'filter_process_shortcodes' ) ); 

    }

    /*--------------------------------------------------------------------------------------
    *
    * add_shortcodes
    *
    * @since 1.0
    * 
    *-------------------------------------------------------------------------------------*/

    function process_shortcodes($content) {
        global $shortcode_tags;
        
        // Backup current registered shortcodes and clear them all out
        $orig_shortcode_tags = $shortcode_tags;
        $shortcode_tags = array();
        remove_all_shortcodes();

        $this->add_shortcodes();
     
        // Do the shortcode (only the one above is registered)
        $content = do_shortcode( $content );
     
        // Put the original shortcodes back
        $shortcode_tags = $orig_shortcode_tags;
     
        return $content;
    }

    function filter_process_shortcodes() {
        add_filter('the_content', array( &$this, 'process_shortcodes'), 7);
    }

    function add_shortcodes() {
        global $bontoolkit;
        $bon_toolkit_options = get_option($bontoolkit->option_name);

        add_shortcode('bt-map', array( $this, 'map_shortcode' ));    
        add_shortcode('bt-tabs', array( $this, 'tabs_shortcode' ));
        add_shortcode('bt-tab', array( $this, 'tab_shortcode' ));
        add_shortcode('bt-toggles', array( $this, 'toggles_shortcode' ));
        add_shortcode('bt-toggle', array( $this, 'toggle_shortcode' ));
        add_shortcode('bt-alert', array( $this, 'alert_shortcode' ));
        add_shortcode('bt-button', array( $this, 'button_shortcode' ));
        add_shortcode('bt-icon', array( $this, 'icon_shortcode' ));
        
        if ( function_exists('shortcode_exists') && !shortcode_exists( 'entry-published' ) ) {
            add_shortcode('entry-published', array( $this, 'entry_published_shortcode' ));
        }

        if ( function_exists('shortcode_exists') && !shortcode_exists( 'entry-author' ) ) {
            add_shortcode('entry-author', array( $this, 'entry_author_shortcode' ));
        }
    }

    /*--------------------------------------------------------------------------------------
    *
    * bon_hr_shortcode
    * @usage 
    * [bt-map latitude="" longitude="" zoom="" height="400px" width="100%"]
    * @since 1.0
    * 
    *-------------------------------------------------------------------------------------*/
    function map_shortcode( $attr ) {
        global $post;

        wp_enqueue_script( 'bon-toolkit-map' );

        static $instance;
        $instance++;

        $attr = shortcode_atts( array(
                        'latitude' => '',
                        'longitude' => '',
                        'zoom'  => '14',
                        'color' => 'red',
                        'height' => '400px',
                        'width' => '100%',
                        'scrollwheel' => true,
                        'draggable' => true,
                    ), $attr);

        $color = 'red';
        
        if(isset($attr['color']) && !empty($attr['color']) ) {
            $color = $attr['color'];
        }

        $marker = BON_TOOLKIT_IMAGES . '/marker-'.$color.'.png';

        $output = '<div id="'.$instance.'" style="height:'.$attr['height'].'; width:'.$attr['width'].';" data-marker="'.$marker.'" class="bon-toolkit-map '.$color.'" data-latitude="'.$attr['latitude'].'" data-longitude="'.$attr['longitude'].'" data-zoom="'.$attr['zoom'].'" data-sw="'.$attr['scrollwheel'].'" data-dg="'.$attr['draggable'].'"></div>';

        return $output;
    }

    function tabs_shortcode( $attr, $content = null ) {

        if( isset($GLOBALS['tabs_count']) )
            $GLOBALS['tabs_count']++;
        else
            $GLOBALS['tabs_count'] = 0;
    

        extract( shortcode_atts( array(
            'direction' => '',
            'color' => 'orange',
            'style' => 'light',
        ), $attr ) );

        preg_match_all( '/bt-tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );

        $tab_titles = array();
        if( isset($matches[1]) ){ $tab_titles = $matches[1]; }

        $output = '';

        if( count($tab_titles) ){

            $tab_nav = '<nav class="tab-nav">';

            $i = 0;
            foreach( $tab_titles as $tab ) {
              
                $tab_nav .= '<a class="';
                if($i== 0 ) {
                    $tab_nav .= 'active';
                }
                $tab_nav .= '" href="#tab-target-' . $GLOBALS['tabs_count'] . '-' . sanitize_title( $tab[0] ) . '" >' . $tab[0] . '</a>';
                
                $i++;
            }
            $tab_nav .= '</nav>';


            $output .= '<section class="bon-toolkit-tabs '.$color. ' ' .$direction.'">';
            if( $direction != 'tab-bottom' ) {
                $output .= $tab_nav;
            }
            $output .= '<div class="tab-contents '.$style.'">';
            $output .= do_shortcode( $content );
            $output .= '</div>';
            if( $direction == 'tab-bottom' ) {
                $output.= $tab_nav;
            }
            $output .= '</section>';

        } else {
            $output .= do_shortcode( $content );
        }

        return $output;
    }

    function tab_shortcode( $attr, $content = null ) {

        if( !isset($GLOBALS['current_tabs']) ) {
            
            $GLOBALS['current_tabs'] = $GLOBALS['tabs_count'];
            $state = 'active';

        } else {

            if( $GLOBALS['current_tabs'] == $GLOBALS['tabs_count'] ) {

                $state = ''; 

            } else {

                $GLOBALS['current_tabs'] = $GLOBALS['tabs_count'];
                $state = 'active';

            }
        }

        $defaults = array( 'title' => 'Tab');
        extract( shortcode_atts( $defaults, $attr ) );

        $output = '';

        return '<div id="tab-target-' . $GLOBALS['tabs_count'] . '-'. sanitize_title( $title ) .'" class="tab-content ' . $state . '">'. do_shortcode( $content ) .'</div>';

    }

    function toggles_shortcode( $attr, $content = null ) {

        if( isset($GLOBALS['toggles_count']) )
            $GLOBALS['toggles_count']++;
        else
            $GLOBALS['toggles_count'] = 0;

        $defaults = array();
        extract( shortcode_atts( $defaults, $attr ) );

        // Extract the tab titles for use in the tab widget.
        preg_match_all( '/bt-toggle title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );

        $toggle_titles = array();
        if( isset($matches[1]) ){ 
            $toggle_titles = $matches[1]; 
        }


        $output = '';

        if( count($toggle_titles) ){

            $output .= '<ul class="bon-toolkit-accordion" id="accordion-' . $GLOBALS['toggles_count'] . '">';

            $output .= do_shortcode( $content );

            $output .= '</ul>';

        } else {

            $output .= do_shortcode( $content );

        }

        return $output;

    }

    function toggle_shortcode( $attr, $content = null ) {

        if( !isset($GLOBALS['current_toggle']) )
            $GLOBALS['current_toggle'] = 0;
        else 
            $GLOBALS['current_toggle']++;

        if( isset($GLOBALS['toggle_count']) )
            $GLOBALS['toggle_count']++;
        else
            $GLOBALS['toggle_count'] = 0;


        $output = '';

        $defaults = array( 'title' => 'Toggle', 'state' => '');
        extract( shortcode_atts( $defaults, $attr ) );

        
        $output .= '<li class="accordion-group"><label for="toggle-target-'. $GLOBALS['toggle_count'] .'">'.( $title ).'</label>';

        if($state == 'active') {
                
               $output .= '<input type="radio" checked="checked" name="toggle-section-'. $GLOBALS['toggles_count'] .'" id="toggle-target-'. $GLOBALS['toggle_count'] .'">';

        } else {

               $output .= '<input type="radio" name="toggle-section-'. $GLOBALS['toggles_count'] .'" id="toggle-target-'. $GLOBALS['toggle_count'] .'">';
        }

        $output .= '<span class="accordion-open">-</span><span class="accordion-close">+</span><div class="toggle-content">'. do_shortcode( $content ) .'</div>';

        $output .= '</li>';

        return $output;
    }

    function alert_shortcode( $attr, $content = null ) {
        extract(shortcode_atts(array(
            'color'   => 'white'
        ), $attr));
        
       return '<div class="bon-toolkit-alert '.$color.'">' . do_shortcode($content) . '</div>';
    }

    function button_shortcode( $attr, $content = null ) {
        extract(shortcode_atts(array(
            'url' => '#',
            'target' => '_self',
            'color' => 'grey',
            'style' => 'grad',
            'size' => 'small',
            'block' => '',
            'type' => 'round'
        ), $attr));
            
        $block_cls = '';

        if( $block == 'yes') {
            $block_cls = 'block';
        }
       return '<a target="'.$target.'" class="bon-toolkit-button '.$size.' '.$style.' '.$color.' '. $block_cls .' '. $type .'" href="'.$url.'">' . do_shortcode($content) . '</a>';
    }

    function icon_shortcode( $attr, $content = null ) {
        extract(shortcode_atts(array(
            'size'   => '',
            'icon' => '',
        ), $attr));
        
       return '<i class="bonicons ' . $icon . ' ' . $size . '"></i>';
    }

   
    /**
     * Displays the published date of an individual post.
     *
     * @since 1.0
     * @access public
     * @param array $attr
     * @return string
     */
    function entry_published_shortcode( $attr ) {
        $attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Posted on:','bon'), 'format' => get_option( 'date_format' ) ), $attr );

        $published = '<span class="entry-published-meta entry-post-meta"><strong class="published-text entry-meta-title">'.$attr['text'].'</strong> <abbr title="' . get_the_time( esc_attr__( 'l, F jS, Y, g:i a', 'bon' ) ) . '">' . get_the_time( $attr['format'] ) . '</abbr></span>';
        return $attr['before'] . $published . $attr['after'];
    }

    /**
     * Displays an individual post's author with a link to his or her archive.
     *
     * @since 1.0
     * @access public
     * @param array $attr
     * @return string
     */
    function entry_author_shortcode( $attr ) {
        $attr = shortcode_atts( array( 'before' => '', 'after' => '', 'text' => __('Author:','bon') ), $attr );
        $author = '<span class="entry-author-meta entry-post-meta"><strong class="author-text entry-meta-title">'.$attr['text'].'</strong> <a class="url fn n" rel="author" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' ) ) . '">' . get_the_author_meta( 'display_name' ) . '</a></span>';
        return $attr['before'] . $author . $attr['after'];
    }
}

new BON_Toolkit_Shortcodes();
?>