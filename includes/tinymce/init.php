<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * BON_Toolkit_Tinymce_Init
 *
 * @package BON_Toolkit
 * @author Hermanto Lim
 * @version 1.0.0
 * @since 1.0.0
 *
 */
class BON_Toolkit_Tinymce_Init {

    function __construct() 
    {	
        add_action('init', array(&$this, 'init'));
	}

	/**
	 * Registers TinyMCE rich editor buttons
	 *
	 * @return	void
	 */
	function init()
	{
		add_action( 'admin_print_styles', array( $this, 'print_styles' ) );

		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;
	
		if ( get_user_option('rich_editing') == 'true' )
		{
			add_filter( 'mce_external_plugins', array(&$this, 'add_rich_plugins') );
			add_filter( 'mce_buttons', array(&$this, 'register_rich_buttons') );
		}

	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Defins TinyMCE rich editor js plugin
	 *
	 * @return	void
	 */
	function add_rich_plugins( $plugin_array )
	{
		$plugin_array['bonToolkitShortcodes'] = BON_TOOLKIT_TINYMCE . '/plugin.js';
		return $plugin_array;
	}
	
	// --------------------------------------------------------------------------
	
	/**
	 * Adds TinyMCE rich editor buttons
	 *
	 * @return	void
	 */
	function register_rich_buttons( $buttons )
	{
		array_push( $buttons, "|", 'bon_toolkit_button' );
		return $buttons;
	}

	function print_styles() {
		$icon = trailingslashit( BON_TOOLKIT_IMAGES ) . 'icon.png';
		echo '<style>.mce-ico.mce-i-bt-shortcode-icon{ background: url("'.$icon.'") no-repeat; }</style>';
	}

}

new BON_Toolkit_Tinymce_Init();
?>