<?php if ( ! defined( 'ABSPATH' ) ) exit('No direct script access allowed'); // Exit if accessed directly

/**
 * BON_Toolkit_Columns
 *
 * @package BON_Toolkit
 * @author Hermanto Lim
 * @since 1.0.0
 *
 */
class BON_Toolkit_Columns {

	/**
	 *
	 * @var int
	 */
	public $grid = 12;

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
	 * Allowed grids can be 2, 3, 4, 5, or 12 columns.
	 *
	 * @var array()
	 */
	public $allowed_grids = array( 2, 3, 4, 5, 12 );

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var string
	 */
	public $shortcode_tag = 'bt-col';


	/**
	 * Sets up our actions/filters.
	 *
	 * @return void
	 */
	public function __construct() {

		/* Register shortcodes on 'init'. */
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_styles' ), 1 );
		add_filter('the_content', array( &$this, 'process_shortcode'), 7);

	}

	public function enqueue_styles() {

		/* Use the .min stylesheet if SCRIPT_DEBUG is turned off. */
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		/* Enqueue the stylesheet. */
		wp_enqueue_style('bon-toolkit-columns', trailingslashit( BON_TOOLKIT_CSS ) . "columns.css", null, '1.0' );
	}

	/**
	 * Registers the [column] shortcode.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function init() {

		global $bon, $bontoolkit;

		$prefix = bon_toolkit_get_prefix();
		$this->prefix = $prefix;

		add_shortcode( $this->shortcode_tag, array( &$this, 'shortcode') );
		
	}

	/**
	 * Returns the content of the column shortcode.
	 *
	 * @return string
	 */
	public function shortcode( $attr, $content = null ) {

		/* If there's no content, just return back what we got. */
		if ( is_null( $content ) )
			return $content;

		/* Set up the default variables. */
		$output = '';
		$row_classes = array();
		$column_classes = array();

		/* Set up the default arguments. */
		$defaults = apply_filters(
			'bon_toolkit_column_default_attr_filter',
			array(
				'grid'  => $this->grid,
				'span'  => 1,
				'push'  => 0,
				'class' => ''
			)
		);

		/* Parse the arguments. */
		$attr = shortcode_atts( $defaults, $attr );

		/* Allow devs to filter the arguments. */
		$attr = apply_filters( 'bon_toolkit_column_attr_filter', $attr );

		/* Allow devs to overwrite the allowed grids. */
		$this->allowed_grids = apply_filters( 'bon_toolkit_column_allowed_grid_filter', $this->allowed_grids );

		/* Make sure the grid is in the allowed grids array. */
		if ( $this->is_first && in_array( $attr['grid'], $this->allowed_grids ) )
			$this->grid = absint( $attr['grid'] );

		/* Span cannot be greater than the grid. */
		$attr['span'] = ( $this->grid >= $attr['span'] ) ? absint( $attr['span'] ) : 1;

		/* The push argument should always be less than the grid. */
		$attr['push'] = ( $this->grid > $attr['push'] ) ? absint( $attr['push'] ) : 0;

		/* Add to the total $span. */
		$this->span = $this->span + $attr['span'] + $attr['push'];

		/* Column classes. */
		$column_classes[] = 'bon-toolkit-col';
		$column_classes[] = "column-span-{$attr['span']}";
		$column_classes[] = "column-push-{$attr['push']}";

		/* Add user-input custom class(es). */
		if ( !empty( $attr['class'] ) ) {
			if ( !is_array( $attr['class'] ) )
				$attr['class'] = preg_split( '#\s+#', $attr['class'] );
			$column_classes = array_merge( $column_classes, $attr['class'] );
		}

		/* Add the 'column-first' class if this is the first column. */
		if ( $this->is_first )
			$column_classes[] = 'column-first';

		/* If the $span property is greater than (shouldn't be) or equal to the $grid property. */
		if ( $this->span >= $this->grid ) {

			/* Add the 'column-last' class. */
			$column_classes[] = 'column-last';

			/* Set the $is_last property to true. */
			$this->is_last = true;
		}

		/* Object properties. */
		$object_vars = get_object_vars( $this );

		/* Allow devs to create custom classes. */
		$column_classes = apply_filters( 'bon_toolkit_column_class_filter', $column_classes, $attr, $object_vars );

		/* Sanitize and join all classes. */
		$column_class = join( ' ', array_map( 'sanitize_html_class', array_unique( $column_classes ) ) );

		/* Output */

		/* If this is the first column. */
		if ( $this->is_first ) {

			/* Row classes. */
			$row_classes = array( 'bon-toolkit-column-grid', "column-grid-{$this->grid}" );
			$row_classes = apply_filters( 'bon_toolkit_column_row_class_filter', $row_classes, $attr, $object_vars );
			$row_class = join( ' ', array_map( 'sanitize_html_class', array_unique( $row_classes ) ) );

			/* Open a wrapper <div> to contain the columns. */
			$output .= '<div class="' . $row_class . '">';

			/* Set the $is_first property back to false. */
			$this->is_first = false;
		}

		/* Add the current column to the output. */
		$output .= '<div class="' . $column_class . '">' . apply_filters( 'bon_toolkit_column_content_filter', $content ) . '</div>';

		/* If this is the last column. */
		if ( $this->is_last ) {

			/* Close the wrapper. */
			$output .= '</div>';

			/* Reset the properties that have been changed. */
			$this->reset();
		}

		/* Return the output of the column. */
		return apply_filters( 'bon_toolkit_column_filter', $output );
	}

	/**
	 * Resets the properties to their original states.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function reset() {

		foreach ( get_class_vars( __CLASS__ ) as $name => $default )
			$this->$name = $default;
	}


	function process_shortcode($content) {
	    global $shortcode_tags;

	    // Backup current registered shortcodes and clear them all out
	    $orig_shortcode_tags = $shortcode_tags;
	    remove_all_shortcodes();

	    add_shortcode($this->shortcode_tag, array($this, 'shortcode') );

	    // Do the shortcode (only the one above is registered)
	    $content = do_shortcode($content);

	    // Put the original shortcodes back
	    $shortcode_tags = $orig_shortcode_tags;

	    return $content;
	}
}

new BON_Toolkit_Columns();

?>