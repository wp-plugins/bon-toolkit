<?php
class BON_Toolkit_Page_Builder {
	/**
	 * @var string
	 */
	public $page = array('post-new.php','post.php');

	/**
	 * @var array
	 */
	public $builder_options = array();

	/**
	 * @var array
	 */
	public $builder_element = array();

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var array
	 */
	public $supported_post_type = array();

	/**
	 * The Class Constructor
	 * @since 1.0.0
	 *
	 */
		function __construct() {
			global $bontoolkit;
			
			$this->prefix = bon_toolkit_get_prefix();
			$this->set_builder_options();

			$this->supported_post_type = $bontoolkit->builder_post_types;

			add_action( 'add_meta_boxes', array(&$this, 'set_meta_box') );
			add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_scripts'), 1000 );
			add_action( 'wp_ajax_bon_toolkit_builder', array( &$this, 'render_element') );
			add_action( 'wp_ajax_nopriv_bon_toolkit_builder', array( &$this, 'render_element') );
			add_action( 'save_post', array(&$this, 'save') );
		}

	/**
	 * Adding Metabox to WordPress editing
	 * @access public
	 * @since 1.0.0
	 * @return void
	 *
	 */
		function set_meta_box() {
			foreach( $this->supported_post_type as $type ) {
				add_meta_box( 'bon-toolkit-builder', __( 'BT Page Builder', 'bon-toolkit' ), array($this, 'render_meta_box'), $type, 'advanced', 'high' );
			}
		}

	/**
	 * Setting Up Script and Style for admin
	 * @access public
	 * @since 1.0.0
	 * @return void
	 *
	 */
		function enqueue_scripts($hook) {
			if( !in_array($hook, $this->page ) ) {
					return;
			}

			global $post;

			if( in_array( $post->post_type, $this->supported_post_type ) ) {

				wp_register_style('bon-toolkit-builder', trailingslashit( BON_TOOLKIT_CSS ) . 'builder.css');
				wp_register_script('bon-toolkit-builder', trailingslashit( BON_TOOLKIT_JS ) . 'builder.js', array('jquery', 'jquery-ui-sortable'), '1.0', true );
				wp_register_script('bon-toolkit-builder-modal', trailingslashit( BON_TOOLKIT_JS ) . 'modal.js', array('jquery'), '1.0', true );
				
				wp_enqueue_style( 'bon-toolkit-builder' );

				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script('bon-toolkit-builder');
				wp_enqueue_script('bon-toolkit-builder-modal');

				wp_localize_script( 'bon-toolkit-builder', 'bon_toolkit_builder_ajax', array('url' => admin_url('admin-ajax.php')) );
			}
		}

	/**
	 * Get the column size array from builder-options.php an then set the array into BON_Toolkit_Builder::$builder_options variable
	 * @access public
	 * @since 1.0.0
	 * @return array
	 *
	 */
		function set_builder_options() {

			$this->builder_options = bon_toolkit_get_builder_options();
		}

	/**
	 * Metabox Callback Function
	 * @access public
	 * @since 1.0.0
	 * @return void
	 *
	 */
		function render_meta_box() {
			global $post;
			
			echo '<div id="bon-builder-wrapper">';

			wp_nonce_field( plugin_basename( __FILE__ ), 'bon_toolkit_builder_nonce');

				if( $this->builder_options ){
					$this->builder_options['metas'] = get_post_meta($post->ID, $this->builder_options['name'], true);
					$this->render_panel();
				}

				echo "<div class='clear'></div>";

			echo '</div>';
		}

	/**
	 * Rendering The The Builder Panel in WordPress Editor
	 * @access public
	 * @param array $args
	 * @since 1.0.0
	 * @return void
	 *
	 */
		function render_panel(){

			extract($this->builder_options);
			?>	
				<div class="bon-builder-panel">
					<div id="bon-builder-action" class="quicktags-toolbar">
						<?php wp_nonce_field( 'bon_toolkit_builder_select', 'bon_toolkit_builder_select_nonce'); ?>
							<?php
								foreach( $elements as $key => $value ){
									$i = '';
									if(isset($value['builder_icon'])) {
										$i = '<i class="'.$value['builder_icon'].'"></i>';
									}
									echo '<button value="'.$key.'" class="button bon-builder-add-elem">' . $i . ucwords(str_replace('_', ' ', $key )) . '</button>';
								}
							?>

						<img class="ajax-loader" alt="<?php _e('loading...','bon-toolkit'); ?>" src="<?php echo trailingslashit( BON_TOOLKIT_IMAGES );?>ajax-loader.gif" />
						<br class="clear">
					</div>
					<div class="bon-builder-elements" id="bon-builder-elements">
						<?php $this->render_selected_elements(); ?>
					</div>
				</div>
				<p id="builder-notice"><?php _e('Choose the builder element from the button bar above. You can drag the position and resize the block of the generated element','bon-toolkit'); ?></p>
			<?php
		}

	/**
	 * Rendering The Selected Element needed for the Builder Panel
	 * @access public
	 * @since 1.0.0
	 * @return void
	 *
	 */
		function render_selected_elements() {
			global $post;
			extract($this->builder_options);

			?>	
				<div class="bon-builder-selected-elem-wrap" id="bon-builder-selected-elem-wrap">
					<div class="bon-builder-selected-elements" id="bon-builder-selected-elements">
					<?php
						if($metas != ''){

							$i=0;
							foreach($metas as $meta) {
								if(array_key_exists(key($meta), $this->builder_options['elements'])) {
									$this->render_element($meta, key($meta));
								}
							}
						}
					?>
					</div>
					<br class="clear">
				</div>

			<?php
			
		}

	/**
	 * Rendering The Element needed for the Builder Panel
	 * @access public
	 * @since 1.0.0
	 * @param array $val
	 * @param string $elem_type
	 * @return void
	 *
	 */
		function render_element($elem_val = '', $elem_type = ''){

			if ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
			{
				   $elem_type = isset($_POST['elem_type']) ? $_POST['elem_type'] : '';
			       check_ajax_referer( 'bon_toolkit_builder_select', 'nonce' );
			}
			
			extract($this->builder_options);

			if(empty($elem_val)){
				$block_size = $elements[$elem_type]['default_size'];
			} else {
				$block_size = $elem_val[$elem_type]['default_size'];
			}
			
			$block_elem = array('name'=>$name,'size'=>$size,'elemname'=>$name.'[]','sizename'=>$size.'[]');

			$this->render_block_element($block_elem, $block_size, $elem_type, $elem_val);
			
			if ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
			{
				   wp_die();
			}
			
		}
	
	/**
	 * Rendering The Block Element in the Builder Panel
	 * @access public
	 * @since 1.0.0
	 * @param string $item
	 * @param string $size
	 * @param string $type
	 * @return void
	 *
	 */
		function render_block_element($elem = '', $size = '', $type = '', $elem_val = ''){
			$text = ucfirst(str_replace('_', ' ', $type));

			$allowed_size = $this->get_allowed_size_data($type);
			?>	
				<div class="bon-builder-element-block <?php echo $size; ?>" rel="<?php echo $type; ?>" data-allowedsize='[<?php echo $allowed_size; ?>]'>
					<div class="bon-builder-single-elem-wrap" data-title="<?php printf(__('Edit %s Property','bon-toolkit'), $text); ?>">
						<div class="bon-builder-single-elem">
							
							<span class="bon-builder-element-label"><?php echo $text; ?></span>
							<input type="hidden" id="<?php echo $elem['name'];?>" class="bon-toolkit-builder" value="<?php echo $type; ?>" name="<?php echo $elem['elemname'];?>">
							<input type="hidden" id="<?php echo $elem['size'];?>" class="bon-toolkit-builder-size" value="<?php echo $size; ?>" name="<?php echo $elem['sizename'];?>">
							<div class="bon-builder-actions-wrap action-left">
								<div class="bon-builder-element-actions">
									<div class="action-add-size action-button action-button-top">&blacktriangle;</div>
									<div class="action-sub-size action-button action-button-bottom">&blacktriangledown;</div>
								</div>					
							</div>
							<div class="bon-builder-actions-wrap action-right">
								<div class="bon-builder-element-actions">
									<div class="action-edit-element action-button action-button-top">✐</div>
									<div class="action-delete-element action-button action-button-bottom" >x</div>
								</div>
							</div>
						</div>
						<?php
						 $percentage = $this->format_size_text($type, $size);
						?>
						<div class="bon-builder-element-size-label"><div class="bon-builder-size-line"></div><div class="bon-builder-element-size-ruler"><span><?php echo $percentage; ?></span></div></div>
					</div>
					<?php $this->render_element_meta($type, $elem_val); ?>
				</div>	
			<?php
			
		}

	/**
	 * Rendering The Block Element Meta Options
	 * @access public
	 * @since 1.0.0
	 * @param string $item
	 * @param string $size
	 * @param string $type
	 * @return void
	 *
	 */
		function render_element_meta($type, $elem_val = ''){
			extract($this->builder_options);
			?>

			<div class="bon-builder-element-meta" id="bon-builder-element-meta">
				<?php
				if($type) {
					foreach( $elements[$type] as $input_key => $input_value ) {

						if( $input_key == 'default_size' || $input_key == 'allowed_size' || $input_key == 'builder_icon' || $input_key == 'callback') {
							continue;
						} else {

							if(isset($elem_val) && !empty($elem_val) && is_array($elem_val)) {
								$input_value['value'] = isset($elem_val[$type][$input_key]) ? $elem_val[$type][$input_key] : '';
							} else {
								$input_value['value'] = '';
							}

							$this->get_meta_interface( $input_value );
						}
					}
				}
				?>
			</div> <!-- close bon-builder-element-meta -->
			<?php
		}
    
    //Print exceptional input element ( from meta-template )
	function render_repeat_element($args = '', $values = ''){
		extract($args);
		?>
		<div class="bon-builder-meta-body">
			<div class="bon-builder-meta-title meta-tab"><?php _e('Add Tab','bon-toolkit'); ?></div>
			<div id="bon-builder-page-tab-add-more" class="bon-builder-page-tab-add-more"></div>
			<br class="clear">
			<div class="bon-builder-meta-input">
				<input type="hidden" class="tab-num" id="tab-num" name="<?php echo $args['repeat-num']['name']; ?>[]" value="<?php echo empty($values)? 0: count($values); ?>" />
				<div class="bon-builder-added-tab" id="bon-builder-added-tab">
					<ul>
						<li>
						<?php foreach ( $args as $arg_key => $arg_val ) { 
							if($arg_key == 'repeat-num'|| $arg_key == 'value') {
								continue;
							} else { if(!empty($values)) {
						?>
							<?php $this->get_meta_interface($arg_val, false);	?>
							<div id="unpick-tab" class="unpick-tab"></div>
						
						<?php } } }?>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Rendering Meta Interface
	 * @access public
	 * @since 1.0.0
	 * @param array $meta_box
	 * @return void
	 *
	 */

		function get_meta_interface($meta_box, $name_trailing = true) {

			$defaults = array(
				'type' => '',
				'std' => '',
				'class' => '',
				'title' => '',
				'options' => '',
				'repeat_num' => '',
				'repeat_child' => ''
			);

			$meta_box = wp_parse_args( $meta_box, $defaults );

			extract($meta_box);
			$id = $name;
			if($name_trailing) {
				$name = $name . '[]';
			}
			if(!empty($value)) {
				if(!is_array($value)) {
					$value = (empty($value)) ? esc_html($std) : esc_html($value);
				}
			} else {
				$value = esc_html($std);
			}
			switch($meta_box['type']){
				case "text": 
				?>
				<div class="bon-builder-meta-body">
					<label><?php echo $title; ?></label>
					<div class="bon-builder-meta-input">
						<input type="text" class="<?php echo $class; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
						<?php if(isset($description)){ ?>
							<span class="bon-builder-meta-description"><?php echo $description; ?> </span>
						<?php } ?>
					</div>
				</div>
				<?php
				break;
				case "upload": 
				?>
				<div class="bon-builder-meta-body">
					<label><?php echo $title; ?></label>
					<div class="bon-builder-meta-input">	
						<input name="<?php echo $name; ?>" type="text" class="upload_media_text_meta <?php echo $class; ?>" value="<?php echo $value; ?>" />
						<a href="#" class="bon-builder-upload-button button"><?php _e('Upload Image','bon-toolkit'); ?></a>
						<?php if(isset($description)){ ?>
							<span class="bon-builder-meta-description"><?php echo $description; ?></span>
						<?php } ?>
					</div>
				</div>
				<?php
				break;
				case "textarea":
				?>
				<div class="bon-builder-meta-body">
					<label><?php echo $title; ?></label>
					<div class="bon-builder-meta-input">
						<textarea class="<?php echo $class; ?>" name="<?php echo $name; ?>" ><?php echo stripslashes($value); ?></textarea>
						<?php if(isset($description)){ ?>
							<span class="bon-builder-meta-description"><?php echo $description; ?></span>
						<?php } ?>
					</div>
				</div>
				<?php
				break;
				case "select": 
				?>
				<div class="bon-builder-meta-body">
					<label><?php echo $title; ?></label>
					<div class="bon-builder-meta-input">	
						<select class="<?php echo $class; ?>" name="<?php echo $name; ?>">
							<?php foreach($options as $key_option => $option){ ?>
								<option value="<?php echo $key_option ; ?>" <?php if( $key_option==esc_html($value) ){ echo 'selected'; }?> ><?php echo $option ; ?></option>
							<?php } ?>
						</select>
						<?php if(isset($description)){ ?>
							<span class="bon-builder-meta-description"><?php echo $description; ?></span>
						<?php } ?>
					</div>
				</div>
				<?php
				break;
				case "info": 
				?>
				<div class="bon-builder-meta-body">
					<div class="bon-builder-meta-description">	
						<?php if(isset($description)){ ?>
							<strong><span class="bon-builder-meta-description big-desc"><?php echo $description; ?></span></strong>
						<?php } ?>
					</div>
				</div>
				<?php
				break;
				case "repeatable":
				?>
				<div class="bon-builder-elements-meta-body">
					
					<div class="bon-builder-meta-repeat">
						<input type="hidden" id="repeat-count" name="<?php echo $repeat_num; ?>[]" value="<?php echo empty($value)? 1: count($value); ?>" />
						<div class="bon-builder-added-tab" id="bon-builder-added-tab">
							<ul>
								<?php
									if( !empty($repeat_child) ) {
										if(!empty($value) && is_array($value)) {
											foreach($value as $child_val) {
										?>
											<li>	
										<?php
												$i = 0;
													foreach ($repeat_child as $child_key => $child_meta){ 
														$child_meta['value'] = $child_val[$child_key];
														$child_meta['name'] = $meta_box['name'] . '[' . $child_meta['name'] . '][]';
														$this->get_meta_interface($child_meta, false);
													}
												?>
												<div class="bon-builder-repeat-action">
													<a id="bon-builder-add-child" title="<?php _e('Add Field','bon-toolkit'); ?>" class="bon-builder-add-child button">+</a>
													<a class="bon-builder-remove-child button" title="<?php _e('Remove Field','bon-toolkit'); ?>" >-</a>
												</div>
											</li>

										<?php
												$i++;
											}	
											
										} else {
											?>
											<li>	
												<?php 
												$i = 0;
												foreach($repeat_child as $child ) {

													$child['name'] = $meta_box['name'] . '[' . $child['name'] . '][]';
													$this->get_meta_interface($child, false);
													$i++;
												} ?>
												<div class="bon-builder-repeat-action">
													<a id="bon-builder-add-child" title="<?php _e('Add Field','bon-toolkit'); ?>" class="bon-builder-add-child button">+</a>
													<a class="bon-builder-remove-child button" title="<?php _e('Remove Field','bon-toolkit'); ?>" >-</a>
												</div>
											</li>
											<?php
										}
									}
								?>
							</ul>
						</div>
					</div>
				</div>
				<?php
				break;

				case "icon" : ?>
					<?php if( function_exists( 'bon_icon_select_field') ) : ?>
					<div class="bon-builder-meta-body" id="bon-builder-meta-body-<?php echo $id; ?>">
						<label><?php echo $title; ?></label>
						<div class="bon-builder-meta-input">
						<?php echo bon_icon_select_field( $id, $name, '#bon-builder-meta-modal #bon-builder-meta-body-'.$id, esc_attr( $value ) ); ?>
						</div>
					</div>
					<?php else: ?>
					<div class="bon-builder-meta-body">
						<label><?php echo $title; ?></label>
						<div class="bon-builder-meta-input">
							<input type="text" class="<?php echo $class; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
							<?php if(isset($description)){ ?>
								<span class="bon-builder-meta-description"><?php echo $description; ?> </span>
							<?php } ?>
						</div>
					</div>
					<?php endif; ?>

				<?php
				break;
			}
		}

	
	/**
	 * Save Action
	 * @access public
	 * @since 1.0.0
	 * @param string $post_id
	 * @return void
	 *
	 */
		function save($post_id) {
			// Verification
			if(defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) return;
			if(!isset($_POST['bon_toolkit_builder_nonce'])) return;
			if(!wp_verify_nonce($_POST['bon_toolkit_builder_nonce'], plugin_basename( __FILE__ ))) return;
			
			// Save data of page
			if( in_array( $_POST['post_type'], $this->supported_post_type ) ){
				if(!current_user_can('edit_page', $post_id)) return;
				$this->save_builder($post_id);
			}
		}

	/**
	 * Saving up the builder data
	 * @access public
	 * @since 1.0.0
	 * @param string $post_id
	 * @return void
	 *
	 */
		function save_builder($post_id){
			$meta_box = $this->builder_options;
			if ($meta_box){
				if(isset($_POST[$meta_box['size']])){
					$num = sizeof($_POST[$meta_box['size']]);
				} else {
					$num = 0;
				}
				$element_meta = array();
				$element_meta_num = array();
				
				for($i=0; $i<$num; $i++) {
					$element_new = sanitize_text_field( $_POST[$meta_box['name']][$i] );
					$element_size_new = sanitize_text_field( $_POST[$meta_box['size']][$i] );
					$element = $meta_box['elements'][$element_new];
					if(!isset($element_meta_num[$element_new])){
						$element_meta_num[$element_new] = 0;
						if($element_new == 'tab'){
							$element_meta_num['repeat_tab'] = 0;
						} else if($element_new == 'toggle'){
							$element_meta_num['repeat_toggle'] = 0;
						}
					}
					
					foreach($element as $key => $value){

						if($key == 'repeat_element'){

							if($element_new == "tab"){
								$repeat_type = 'repeat_tab';
							} else {
								$repeat_type = 'repeat_toggle';
							}

							$child_num = $_POST[$value['repeat_num']][$element_meta_num[$element_new]];

							for($j=0; $j<$child_num; $j++){

								
								foreach( $value['repeat_child'] as $child_key => $child_val) {
									$element_meta[$i][$element_new][$key][$j][$child_key] = isset( $_POST[$value['name']][$child_val['name']][$element_meta_num[$repeat_type]] )? stripslashes($_POST[$value['name']][$child_val['name']][$element_meta_num[$repeat_type]]) : '';
								} 

								$element_meta_num[$repeat_type]++;
							}
						} else if($key == 'default_size') {
							$element_meta[$i][$element_new]['default_size'] = $element_size_new; 
						} else if($key == 'allowed_size' || $key == 'builder_icon' || $key == 'callback' ) {
							continue;
						} else {
							
							if(isset($_POST[$value['name']][$element_meta_num[$element_new]])){
								$element_meta_value = stripslashes($_POST[$value['name']][$element_meta_num[$element_new]]);
								$element_meta[$i][$element_new][$key] = $element_meta_value; 
							} else {
								$element_meta[$i][$element_new][$key] = '';
							}
						}
					}
					$element_meta_num[$element_new]++;
				}
				
				$element_meta_old = get_post_meta($post_id, $meta_box['name'], true);

				if($element_meta == $element_meta_old){
					add_post_meta($post_id, $meta_box['name'], $element_meta, true);
				} else if(!$element_meta){
					delete_post_meta($post_id, $meta_box['name'], $element_meta_old);
				}else if($element_meta != $element_meta_old){
					update_post_meta($post_id, $meta_box['name'], $element_meta, $element_meta_old);
				}

			}
		}

	

	/**
	 * Retrive Data Array to use in HTML Data
	 * @access public
	 * @since 1.0.0
	 * @param string $type
	 * @return json_data
	 * Example:
	 * <code>
	 * <?php
	 * $allowed_size = $this->get_allowed_size_data($type);
	 * ?>
	 * </code>
	 */
		function get_allowed_size_data($type) {
			
			$return = '';

			$allowed_size_arr = $this->builder_options['elements'][$type]['allowed_size'];

			$len = count($allowed_size_arr);

			$i = 1;
			if(is_array($allowed_size_arr) && !empty($allowed_size_arr)) {
				foreach($allowed_size_arr as $key => $value) {
					
					$return .= '{"key":"'.$key.'","value":"'.$value.'"}';
					
					if($i < $len) {
						$return .= ',';
					}
					$i++;
				}		
			}
			return $return;
		}

		function format_size_text($type, $size) {
			$r =  explode( '/', $this->builder_options['elements'][$type]['allowed_size'][$size]);
			$r = ( intval($r[0]) / intval($r[1]) ) * 100;
			$r = ((int) $r == $r) ? $r . '%' : number_format($r, 2) . '%';

			return $r;
		}
}
new BON_Toolkit_Page_Builder();
?>