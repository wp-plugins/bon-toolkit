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

        if(isset($bon_toolkit_options['google_api_key']) && !empty($bon_toolkit_options['google_api_key']) ) {
            add_shortcode('bt-map', array( $this, 'map_shortcode' ));    
        }

        add_shortcode('bt-tabs', array( $this, 'tabs_shortcode' ));
        add_shortcode('bt-tab', array( $this, 'tab_shortcode' ));
        add_shortcode('bt-toggles', array( $this, 'toggles_shortcode' ));
        add_shortcode('bt-toggle', array( $this, 'toggle_shortcode' ));
        add_shortcode('bt-alert', array( $this, 'alert_shortcode' ));
        add_shortcode('bt-button', array( $this, 'button_shortcode' ));
        //add_shortcode('bt-widget', array( $this, 'widget_shortcode' ));
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

        $current = $post->ID;

        $attr = shortcode_atts( array(
                        'latitude' => '36.964645',
                        'longitude' => '-122.01523',
                        'zoom'  => '14',
                        'color' => 'red',
                        'height' => '400px',
                        'width' => '100%'
                    ), $attr);

        $color = 'red';
        
        if(isset($attr['color']) && !empty($attr['color']) ) {
            $color = $attr['color'];
        }

        $marker = BON_TOOLKIT_IMAGES . '/marker-'.$color.'.png';

        $output = '<div id="'.$post->ID.'" style="height:'.$attr['height'].'; width:'.$attr['width'].';" data-marker="'.$marker.'" class="bon-toolkit-map '.$color.'" data-latitude="'.$attr['latitude'].'" data-longitude="'.$attr['longitude'].'" data-zoom="'.$attr['zoom'].'"></div>';

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
        ), $attr ) );

        preg_match_all( '/bt-tab title="([^\"]+)"/i', $content, $matches, PREG_OFFSET_CAPTURE );

        $tab_titles = array();
        if( isset($matches[1]) ){ $tab_titles = $matches[1]; }

        $output = '';

        if( count($tab_titles) ){
            $output .= '<section class="bon-toolkit-tabs '.$color. ' ' .$direction.'"><nav class="tab-nav">';

            $i = 0;
            foreach( $tab_titles as $tab ) {
              
                $output .= '<a class="';
                if($i== 0 ) {
                    $output .= 'active';
                }
                $output .= '" href="#tab-target-' . $GLOBALS['tabs_count'] . '-' . sanitize_title( $tab[0] ) . '" >' . $tab[0] . '</a>';
                
                $i++;
            }
            $output .= '</nav>';

            $output .= '<div class="tab-contents">';
            $output .= do_shortcode( $content );
            $output .= '</div></section>';

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
            'type' => 'round'
        ), $attr));
        
       return '<a target="'.$target.'" class="bon-toolkit-button '.$size.' '.$style.' '.$color.' '. $type .'" href="'.$url.'">' . do_shortcode($content) . '</a>';
    }


    function widget_shortcode( $attr, $content = null ) {
        // Configure defaults and extract the attributes into variables
        extract( shortcode_atts( 
            array( 
                'type'   => '',
                'title'  => '',
            ), 
            $attr 
        ));
        

        $args = array(
            'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
            'after_widget'  => '</div></div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>'
        );

        ob_start();
        the_widget( $type, $attr, $args ); 
        $output = ob_get_clean();

        return $output;

    }

   

}

new BON_Toolkit_Shortcodes();
?>