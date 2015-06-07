<?php
function bon_toolkit_get_builder_options()
{
    global $bontoolkit, $wp_registered_sidebars;
    
    $prefix = bon_toolkit_get_prefix();
    
    $category_options = bon_toolkit_get_categories('category');
    
    $default_margin  = '0';
    // the element in page options
    $builder_options = array(
        'name' => $prefix . 'builder',
        'size' => $prefix . 'builder_size',
        'elements' => array()
    );

    $suffix = bon_toolkit_get_builder_suffix();
    $bon_toolkit_options = get_option($bontoolkit->option_name);
    $coloroptions = array(
        'red' => __('Red','bon-toolkit'),
        'green' => __('Green','bon-toolkit'),
        'blue' => __('Blue','bon-toolkit'),
        'orange' => __('Orange','bon-toolkit'),
        'purple' => __('Purple','bon-toolkit'),
        'yellow' => __('Yellow', 'bon-toolkit'),
        'dark' => __('Dark','bon-toolkit')
    );

    $coloroptions2 = array(
        'red' => __('Red','bon-toolkit'),
        'green' => __('Green','bon-toolkit'),
        'blue' => __('Blue','bon-toolkit'),
        'orange' => __('Orange','bon-toolkit'),
        'purple' => __('Purple','bon-toolkit'),
        'yellow' => __('Yellow', 'bon-toolkit'),
        'dark' => __('Dark','bon-toolkit'),
        'light' => __('Light','bon-toolkit')
    );

    $widget_options = array();

    if(!empty($wp_registered_sidebars) && is_array($wp_registered_sidebars)) {
        foreach($wp_registered_sidebars as $key => $sidebar) {
            $widget_options[$key] = $sidebar['name'];
        }
    }
    
    /**
     * Custom Post Query Element
     * This element will output blog posts
     * Available Property :
     * @param $header
     * @param $size
     * @param $category
     * @param $show-thumbnail
     * @param $numberposts
     * @param $excerpt_length
     * @param $pagination
     * @param $orderyby
     * @param $order
     * @param margin
     */
        $builder_options['elements']['post'] = apply_filters('bon_toolkit_builder_element_post_filter', array(
            'header' => array(
                'title' => __('Title','bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'title',
                'type' => 'text'
            ),
            'default_size' => 'span3',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
            'size' => array(
                'title' => __('Post Size','bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'size',
                'options' => array(
                    '1-col' => __('1 Column','bon-toolkit'),
                    '2-col' => __('2 Columns','bon-toolkit'),
                    '3-col' => __('3 Columns','bon-toolkit'),
                    '4-col' => __('4 Columns','bon-toolkit'),
                ),
                'type' => 'select',
                'description' => __('Choose the post column options','bon-toolkit')
            ),
            'category' => array(
                'title' => __('Choose Category','bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'category',
                'options' => $category_options,
                'type' => 'select',
                'description' => __('Choose the post category you want to fetch for the post.','bon-toolkit')
            ),
            'show_thumbnail' => array(
                'title' => __('Show Thumbnail','bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'thumbnail',
                'type' => 'select',
                'options' => array(
                    '1' => __('Yes','bon-toolkit'),
                    '0' => __('No','bon-toolkit')
                )
            ),
            'numberposts' => array(
                'title' => __('Number of Posts','bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'numberposts',
                'type' => 'text',
                'std' => 9,
                'description' => __('This is the number of fetched item in one page.','bon-toolkit')
            ),
            'excerpt_length' => array(
                'title' => __('Excerpt\'s Length','bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'excerpt_length',
                'type' => 'text',
                'std' => 200,
                'description' => __('This is the number of thumbnail content character.','bon-toolkit')
            ),
            'pagination' => array(
                'title' => __('Show Pagination','bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'pagination',
                'type' => 'select',
                'options' => array(
                    '1' => __('Yes','bon-toolkit'),
                    '0' => __('No','bon-toolkit')
                ),
                
                'description' => __('Pagination will only appear when the number of blog post is greater than the number of fetched item in one page.','bon-toolkit')
            ),
            'orderby' => array(
                'title' => __('Order By', 'bon-toolkit'),
                'options' => array(
                    'date' => __('Date','bon-toolkit'),
                    'title' => __('Title','bon-toolkit'),
                    'rand' => __('Random','bon-toolkit'),
                    'comment_count' => __('Comment Count','bon-toolkit')
                ),
                'name' => $prefix . $suffix['post'] . 'orderby',
                'type' => 'select'
            ),
            'order' => array(
                'title' => __('Order Type', 'bon-toolkit'),
                'options' => array(
                    'asc' => __('Ascending','bon-toolkit'),
                    'desc' => __('Descending','bon-toolkit')
                ),
                'std' => 'desc',
                'name' => $prefix . $suffix['post'] . 'order',
                'type' => 'select'
            ),
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['post'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            )
        ));
            
    /**
     * Contact Form Element
     * This element will output contact form
     * Available Property :
     * @param $email
     * @param $margin
     */
        $builder_options['elements']['contact_form'] = apply_filters('bon_toolkit_builder_element_contactform_filter', array(
                'email' => array(
                    'title' => __('Email Address', 'bon-toolkit'),
                    'name' => $prefix . $suffix['contact_form'] . 'email',
                    'type' => 'text',
                    'description' => __('Input the email address for the contact form to send the contact email', 'bon-toolkit')
                ),
                'color' => array(
                    'title' => __('Submit Button Color', 'bon-toolkit'),
                    'name' => $prefix . $suffix['contact_form'] . 'color',
                    'type' => 'select',
                    'options' => $coloroptions
                ),
                'margin' => array(
                    'title' => __('Bottom Margin', 'bon-toolkit'),
                    'name' => $prefix . $suffix['contact_form'] . 'margin',
                    'std' => $default_margin,
                    'type' => 'text'
                ),
                'element_class' => array(
                    'title' => __('Element Class', 'bon-toolkit'),
                    'name' => $prefix . $suffix['contact_form'] . 'element_class',
                    'std' => '',
                    'type' => 'text',
                    'description' => __('Separate class with space.','bon-toolkit')
                ),
                'builder_icon' => 'bt-envelope-alt',
                'default_size' => 'span12',
                'allowed_size' => array(
                        'span6' => '1/2',
                        'span12' =>'1/1',
                    ),
            ));

    /**
     * Map Element
     * This element will output map
     * Available Property :
     * @param $latidue
     * @param $longitude
     * @param color
     * @param height
     */

   
        $builder_options['elements']['map'] = apply_filters('bon_toolkit_builder_element_map_filter', array(
                
                'latitude' => array(
                    'title' => __('Location Latitude', 'bon-toolkit'),
                    'name' => $prefix . $suffix['map'] . 'latitude',
                    'type' => 'text'
                ),
                'builder_icon' => 'bt-checkin',
                'longitude' => array(
                    'title' => __('Location Longitude', 'bon-toolkit'),
                    'name' => $prefix . $suffix['map'] . 'longitude',
                    'type' => 'text'
                ),
                'height' => array(
                    'title' => __('Map Height', 'bon-toolkit'),
                    'name' => $prefix . $suffix['map'] . 'height',
                    'type' => 'text',
                ),
                'zoom' => array(
                    'title' => __('Start Zoom', 'bon-toolkit'),
                    'name' => $prefix . $suffix['map'] . 'zoom',
                    'type' => 'text',
                    'std' => '16',
                ),
                'color' => array(
                    'title' => __('Color', 'bon-toolkit'),
                    'name' => $prefix . $suffix['map'] . 'color',
                    'type' => 'select',
                    'options' => array(
                        'red' => __('Red','bon-toolkit'),
                        'green' => __('Green','bon-toolkit'),
                        'blue' => __('Blue','bon-toolkit'),
                        'yellow' => __('Yellow','bon-toolkit'),
                        'orange' => __('Orange','bon-toolkit'),
                        'purple' => __('Purple','bon-toolkit'),
                    ),
                ),
                'margin' => array(
                    'title' => __('Bottom Margin', 'bon-toolkit'),
                    'name' => $prefix . $suffix['map'] . 'margin',
                    'std' => $default_margin,
                    'type' => 'text'
                ),
                'element_class' => array(
                    'title' => __('Element Class', 'bon-toolkit'),
                    'name' => $prefix . $suffix['map'] . 'element_class',
                    'std' => '',
                    'type' => 'text',
                    'description' => __('Separate class with space.','bon-toolkit')
                ),
                'default_size' => 'span12',
                'allowed_size' => array(
                        'span6' => '1/2',
                        'span12' =>'1/1',
                    ),
            ));
        
            
    /**
     * Text Block Element
     * This element will output a custom text content
     * Available Property :
     * @param $heeader
     * @param $content
     */
        $builder_options['elements']['text_block'] = apply_filters('bon_toolkit_builder_element_textblock_filter', array(        
            'header' => array(
                'title' => __('Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['text_block'] . 'title',
                'type' => 'text'
            ),
            'builder_icon' => 'bt-font',
            'content' => array(
                'title' => __('Content', 'bon-toolkit'),
                'name' => $prefix . $suffix['text_block'] . 'content',
                'type' => 'textarea',
                'class' => 'bon-builder-editor'
            ),
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['text_block'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['text_block'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span3',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));

    /**
     * Image Block Element
     * This element will output a custom text content
     * Available Property :
     * @param $heeader
     * @param $content
     */
        $builder_options['elements']['image_block'] = apply_filters('bon_toolkit_builder_element_imageblock_filter', array(        
            'header' => array(
                'title' => __('Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['image_block'] . 'title',
                'type' => 'text'
            ),
            'builder_icon' => 'bt-picture',
            'src' => array(
                'title' => __('Image URL', 'bon-toolkit'),
                'name' => $prefix . $suffix['image_block'] . 'src',
                'type' => 'upload'
            ),
            'link' => array(
                'title' => __('Link Image to', 'bon-toolkit'),
                'name' => $prefix . $suffix['image_block'] . 'link',
                'type' => 'text'
            ),
            'target' => array(
                'title' => __('Target', 'bon-toolkit'),
                'name' => $prefix . $suffix['image_block'] . 'target',
                'type' => 'select',
                'options' => array(
                    '_blank' => __('_blank','bon-toolkit'),
                    '_self' => __('_self','bon-toolkit'),
                    '_parent' => __('_parent', 'bon-toolkit'),
                    '_top' => __('_top', 'bon-toolkit'),
                ),
            ),
            'alt' => array(
                'title' => __('Alt Text', 'bon-toolkit'),
                'name' => $prefix . $suffix['image_block'] . 'alt',
                'type' => 'text'
            ),
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['image_block'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['image_block'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span3',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));
        
    /**
     * Service Element
     * This element will output a service block
     * Available Property :
     * @param $icon_class
     * @param $icon
     * @param $title
     * @param $caption
     * @param $margin
     */
        $builder_options['elements']['service'] = apply_filters('bon_toolkit_builder_element_service_filter', array(
            'icon_class' => array(
                'title' => __('Icon Class'),
                'name' => $prefix . $suffix['service'] . 'icon_class',
                'type' => 'icon',
                'description' => __('Class for the icon','bon-toolkit')
            ),
            'builder_icon' => 'bt-check',
            'icon_style' => array(
                'title' => __('Icon Style', 'bon-toolkit'),
                'name' => $prefix . $suffix['service'] . 'icon_style',
                'type' => 'select',
                'options' => array(
                    'round' => __('Round','bon-toolkit'),
                    'square' => __('Square','bon-toolkit'),
                    'round_corner' => __('Round Corner','bon-toolkit'),
                    'hexagon' => __('Hexagon', 'bon-toolkit')
                ),
            ),
            'title' => array(
                'title' => __('Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['service'] . 'title',
                'type' => 'text'
            ),
            'link' => array(
                'title' => __('Link to', 'bon-toolkit'),
                'name' => $prefix . $suffix['service'] . 'link',
                'type' => 'text'
            ),
            'content' => array(
                'title' => __('Content', 'bon-toolkit'),
                'name' => $prefix . $suffix['service'] . 'content',
                'type' => 'textarea'
            ),
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['service'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['service'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span4',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));
            
    /**
     * Content Element
     * This element will output a Content from the WordPress Editor the_content()
     * Available Property :
     * @param $heeader
     * @param $description
     * @param $margin
     */
        $builder_options['elements']['post_content'] = apply_filters('bon_toolkit_builder_element_postcontent_filter', array(
            'info' => array(
                'title' => __('Description','bon-toolkit'),
                'name' => $prefix . $suffix['post_content'] . 'no-name',
                'type' => 'info',
                'description' => __('Use this element to put the content from the WordPress Editor','bon-toolkit')
            ),
            'builder_icon' => 'bt-file',
            'header' => array(
                'title' => __('Title','bon-toolkit'),
                'name' => $prefix . $suffix['post_content'] . 'title',
                'type' => 'text',
                'description' => __('Leave blank if you don\'t want a title','bon-toolkit')
            ),
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['post_content'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['post_content'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span4',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));
    
    /**
     * Call to Action Element
     * This element will output a Call To Action Block
     * Available Property :
     * @param $title
     * @param $subtitle
     * @param $margin
     * @param $button_text
     * @param $button_link
     * @param button_icon
     */
        $builder_options['elements']['call_to_action'] = apply_filters('bon_toolkit_builder_element_call_to_action_filter', array(
            'title' => array(
                'title' => __('Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['call_to_action'] . 'title',
                'type' => 'textarea'
            ),
            'subtitle' => array(
                'title' => __('Sub Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['call_to_action'] . 'subtitle',
                'type' => 'textarea'
            ),
            'builder_icon' => 'bt-bullhorn',
            'button_text' => array(
                'title' => __('Button Text', 'bon-toolkit'),
                'name' => $prefix . $suffix['call_to_action'] . 'button_text',
                'type' => 'text',
                'std' => 'Read More'
            ),
            'button_link' => array(
                'title' => __('Link To', 'bon-toolkit'),
                'name' => $prefix . $suffix['call_to_action'] . 'button_link',
                'type' => 'text'
            ),
            'button_icon' => array(
                'title' => __('Icon Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['call_to_action'] . 'button_icon',
                'type' => 'icon'
            ),
            'button_target' => array(
                'title' => __('Target To', 'bon-toolkit'),
                'name' => $prefix . $suffix['call_to_action'] . 'button_target',
                'type' => 'select',
                'options' => array(
                    '_blank' => __('_blank','bon-toolkit'),
                    '_self' => __('_self','bon-toolkit'),
                    '_parent' => __('_parent', 'bon-toolkit'),
                    '_top' => __('_top', 'bon-toolkit'),
                ),
            ),
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['call_to_action'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['call_to_action'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span12',
            'allowed_size' => array(
                    'span12'=>'1/1'
                ),
        ));
        
    /**
     * Tabbed Element
     * This element will output a Tab
     * Available Property :
     * @param $header
     * @param $tab_item
     * @param $margin
     *
     */
        $builder_options['elements']['tab'] = apply_filters('bon_toolkit_builder_element_tab_filter', array(    
            'header' => array(
                'title' => __('Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['tab'] . 'title',
                'type' => 'text'
            ),
            'builder_icon' => 'bt-tab',
            'direction' => array(
                'title' => __('Tab Direction', 'bon-toolkit'),
                'name' => $prefix . $suffix['tab'] . 'direction',
                'type' => 'select',
                'options' => array(
                    'tab-default' => __('Default','bon-toolkit'),
                    'tab-left' => __('Left','bon-toolkit'),
                    'tab-right' => __('Right', 'bon-toolkit'),
                    'tab-bottom' => __('Bottom','bon-toolkit'),
                ),
            ),

            'color' => array(
                'title' => __('Tab Color', 'bon-toolkit'),
                'name' => $prefix . $suffix['tab'] . 'color',
                'type' => 'select',
                'options' => $coloroptions2,
            ),
            'content_style' => array(
                'title' => __('Tab Content Style', 'bon-toolkit'),
                'name' => $prefix . $suffix['tab'] . 'content_style',
                'type' => 'select',
                'options' => $coloroptions2,
            ),

            'repeat_element' => array(
                'type' => 'repeatable',
                'name' => $prefix . $suffix['tab'] . 'child',
                'repeat_num' => $prefix . $suffix['tab'] . 'num',
                'repeat_child' => array(
                    'repeat_title' => array(
                        'name' => $prefix . $suffix['tab'] . 'title',
                        'title' => __('Tab Title', 'bon-toolkit'),
                        'type' => 'text',
                    ),
                    'repeat_content' => array(
                        'name' => $prefix . $suffix['tab'] . 'content',
                        'type' => 'textarea',
                        'title' => __('Tab Content', 'bon-toolkit'),
                    ),
                )
            ),
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['tab'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['tab'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span4',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));
            
    /**
     * Toggle Element
     * This element will output a Toggle Accordion
     * Available Property :
     * @param $header
     * @param $toggle_item
     * @param $margin
     *
     */
        $builder_options['elements']['toggle'] = apply_filters('bon_toolkit_builder_element_toggle_filter', array(    
            'header' => array(
                'title' => __('Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['toggle'] . 'title',
                'type' => 'text'
            ),
            'builder_icon' => 'bt-accordion',
            'repeat_element' => array(
                'type' => 'repeatable',
                'name' => $prefix . $suffix['toggle'] . 'child',
                'repeat_num' => $prefix . $suffix['toggle'] . 'num',
                'repeat_child' => array(
                    'repeat_title' => array(
                        'name' => $prefix . $suffix['toggle'] . 'title',
                        'title' => __('Toggle Title', 'bon-toolkit'),
                        'type' => 'text',
                    ),
                    'repeat_content' => array(
                        'name' => $prefix . $suffix['toggle'] . 'content',
                        'type' => 'textarea',
                        'title' => __('Toggle Content', 'bon-toolkit'),
                    ),
                )
            ),
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['toggle'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['toggle'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span4',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));

    /**
     * Divider Element
     * This element will output a Divider
     * Available Property :
     * @param $header
     * @param $margin
     *
     */
        $builder_options['elements']['divider'] = apply_filters('bon_toolkit_builder_element_divider_filter', array(    
            'header' => array(
                'title' => __('Divider Text', 'bon-toolkit'),
                'name' => $prefix . $suffix['divider'] . 'text',
                'type' => 'text',
                'description' => __('Leave blank to use second style divider','bon-toolkit'),
            ),
            'builder_icon' => 'bt-ellipsis-horizontal',
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['divider'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['divider'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
             'default_size' => 'span12',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));


    /**
     * Alert Element
     * This element will output Alert
     * Available Property :
     * @param $content
     * @param $color
     * @param $margin
     *
     */
        $builder_options['elements']['alert'] = apply_filters('bon_toolkit_builder_element_alert_filter', array(    
            'content' => array(
                'title' => __('Alert Content', 'bon-toolkit'),
                'name' => $prefix . $suffix['alert'] . 'content',
                'type' => 'textarea',
            ),
            'builder_icon' => 'bt-alert',
            'color' => array(
                'title' => __('Alert Color', 'bon-toolkit'),
                'name' => $prefix . $suffix['alert'] . 'color',
                'type' => 'select',
                'options' => array(
                    'blue' => __('Blue', 'bon-toolkit'),
                    'white' => __('White', 'bon-toolkit'),
                    'red' => __('Red', 'bon-toolkit'),
                    'yellow' => __('Yellow', 'bon-toolkit'),
                    'green' => __('Green', 'bon-toolkit'),
                    'gray' => __('Gray', 'bon-toolkit'),
                ),
            ),

            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['alert'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['alert'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span12',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));

    /**
     * Video Element
     * This element will output Video
     * Available Property :
     *
     */
        $builder_options['elements']['video'] = apply_filters('bon_toolkit_builder_element_video_filter', array(    
            'title' => array(
                'title' => __('Video Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'title',
                'type' => 'text',
            ),
            'builder_icon' => 'bt-facetime-video',
            'info' => array(
                'title' => __('Description', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'no-name',
                'type' => 'info',
                'description' => __('To embed you only need to specify the url. For example: http://www.youtube.com/watch?v=abcdeFGHIJ. To use self hosted video leave the embed field empty.','bon-toolkit')
            ),

            'embed' => array(
                'title' => __('Video Url (Third Party Host)', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'embed',
                'type' => 'text',
                'description' => __('For List of Supported Providers please see <a href="http://codex.wordpress.org/Embeds" target="blank">http://codex.wordpress.org/Embeds</a>','bon-toolkit'),
            ),

            'poster' => array(
                'title' => __('Video Poster Url (Self Hosted)', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'poster',
                'type' => 'text',
            ),

            'm4v' => array(
                'title' => __('Video .m4v URL (Self Hosted)', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'm4v',
                'type' => 'text',
            ),


            'ogv' => array(
                'title' => __('Video .ogv URL (Self Hosted)', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'ogv',
                'type' => 'text',
            ),

            'desc' => array(
                'title' => __('Short Video Description', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'desc',
                'type' => 'textarea',
            ),

            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['video'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span4',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));

    /**
     * Twitter Element
     * This element will output Twitter Feed
     * Available Property :
     *
     */
        $builder_options['elements']['twitter'] = apply_filters('bon_toolkit_builder_element_twitter_filter', array(    
            'title' => array(
                'title' => __('Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['twitter'] . 'title',
                'type' => 'text',
            ),
            'builder_icon' => 'bt-twitter',
            'username' => array(
                'title' => __('Twitter Username', 'bon-toolkit'),
                'name' => $prefix . $suffix['twitter'] . 'username',
                'type' => 'text',
            ),

            'postcount' => array(
                'title' => __('How many post to fetch?', 'bon-toolkit'),
                'name' => $prefix . $suffix['twitter'] . 'postcount',
                'type' => 'text',
            ),

            'tweettext' => array(
                'title' => __('Follow Me Text', 'bon-toolkit'),
                'name' => $prefix . $suffix['twitter'] . 'tweettext',
                'type' => 'text',
            ),

            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['twitter'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['twitter'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span4',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));


    /**
     * Flickr Element
     * This element will output Flickr Feed
     * Available Property :
     *
     */
        $builder_options['elements']['flickr'] = apply_filters('bon_toolkit_builder_element_flickr_filter', array(    
            'flickr_title' => array(
                'title' => __('Title', 'bon-toolkit'),
                'name' => $prefix . $suffix['flickr'] . 'title',
                'type' => 'text',
            ),

            'flickr_id' => array(
                'title' => __('Flickr ID', 'bon-toolkit'),
                'name' => $prefix . $suffix['flickr'] . 'flickr_id',
                'type' => 'text',
            ),
            'builder_icon' => 'bt-flickr',
            'flickr_count' => array(
                'title' => __('How many post to fetch?', 'bon-toolkit'),
                'name' => $prefix . $suffix['flickr'] . 'flickr_count',
                'type' => 'text',
            ),

            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['flickr'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['flickr'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span3',
            'allowed_size' => array(
                    'span3'=>'1/4',
                ),
        ));
       
    /**
     * Widget Element
     * This element will output Choosen Sidebar
     * Available Property :
     *
     */
        $builder_options['elements']['widget'] = apply_filters('bon_toolkit_builder_element_flickr_filter', array(

            'info' => array(
                'title' => __('Description','bon-toolkit'),
                'name' => $prefix . $suffix['widget'] . 'no-name',
                'type' => 'info',
                'description' => __('Choose the widget slot then go to <strong>Widget</strong> menu and drag widget into the slot. Custom widget slot can be created through theme options <strong>Sidebar Generator</strong> if supported.','bon-toolkit')
            ),
            'widget_id' => array(
                'title' => __('Choose Widget Slot', 'bon-toolkit'),
                'name' => $prefix . $suffix['widget'] . 'widget_id',
                'type' => 'select',
                'options' => $widget_options,
            ),

            'builder_icon' => 'bt-reorder',
            'margin' => array(
                'title' => __('Bottom Margin', 'bon-toolkit'),
                'name' => $prefix . $suffix['widget'] . 'margin',
                'std' => $default_margin,
                'type' => 'text'
            ),
            'element_class' => array(
                'title' => __('Element Class', 'bon-toolkit'),
                'name' => $prefix . $suffix['widget'] . 'element_class',
                'std' => '',
                'type' => 'text',
                'description' => __('Separate class with space.','bon-toolkit')
            ),
            'default_size' => 'span4',
            'allowed_size' => array(
                    'span3'=>'1/4',
                    'span4'=>'1/3',
                    'span6'=>'1/2',
                    'span8'=>'2/3',
                    'span9'=>'3/4',
                    'span12'=>'1/1'
                ),
        ));

    return apply_filters('bon_toolkit_builder_options_filter', $builder_options);
}
?>