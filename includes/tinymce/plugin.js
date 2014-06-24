(function ()
{
	
	tinymce.PluginManager.add("bonToolkitShortcodes", function( editor, url ){

		function mapColors() {
			var i, colors = [], colorMap;

			colorMap = editor.settings.textcolor_map || [
				"000000", "Black",
				"993300", "Burnt orange",
				"333300", "Dark olive",
				"003300", "Dark green",
				"003366", "Dark azure",
				"000080", "Navy Blue",
				"333399", "Indigo",
				"333333", "Very dark gray",
				"800000", "Maroon",
				"FF6600", "Orange",
				"808000", "Olive",
				"008000", "Green",
				"008080", "Teal",
				"0000FF", "Blue",
				"666699", "Grayish blue",
				"808080", "Gray",
				"FF0000", "Red",
				"FF9900", "Amber",
				"99CC00", "Yellow green",
				"339966", "Sea green",
				"33CCCC", "Turquoise",
				"3366FF", "Royal blue",
				"800080", "Purple",
				"999999", "Medium gray",
				"FF00FF", "Magenta",
				"FFCC00", "Gold",
				"FFFF00", "Yellow",
				"00FF00", "Lime",
				"00FFFF", "Aqua",
				"00CCFF", "Sky blue",
				"993366", "Brown",
				"C0C0C0", "Silver",
				"FF99CC", "Pink",
				"FFCC99", "Peach",
				"FFFF99", "Light yellow",
				"CCFFCC", "Pale green",
				"CCFFFF", "Pale cyan",
				"99CCFF", "Light sky blue",
				"CC99FF", "Plum",
				"FFFFFF", "White"
			];

			for (i = 0; i < colorMap.length; i += 2) {
				colors.push({
					text: colorMap[i + 1],
					color: colorMap[i]
				});
			}

			return colors;
		}

		function renderColorPicker() {
			var ctrl = this, colors, color, html, last, rows, cols, x, y, i;

			colors = mapColors();

			html = '<table class="mce-grid mce-grid-border mce-colorbutton-grid" role="list" cellspacing="0"><tbody>';
			last = colors.length - 1;
			rows = editor.settings.textcolor_rows || 5;
			cols = editor.settings.textcolor_cols || 8;

			for (y = 0; y < rows; y++) {
				html += '<tr>';

				for (x = 0; x < cols; x++) {
					i = y * cols + x;

					if (i > last) {
						html += '<td></td>';
					} else {
						color = colors[i];
						html += (
							'<td>' +
								'<div id="' + ctrl._id + '-' + i + '"' +
									' data-mce-color="' + color.color + '"' +
									' role="option"' +
									' tabIndex="-1"' +
									' style="' + (color ? 'background-color: #' + color.color : '') + '"' +
									' title="' + color.text + '">' +
								'</div>' +
							'</td>'
						);
					}
				}

				html += '</tr>';
			}

			html += '</tbody></table>';

			return html;
		}

		function onPanelClick(e) {

			var buttonCtrl = this.parent(), value;

			if ((value = e.target.getAttribute('data-mce-color'))) {
				if (this.lastId) {
					document.getElementById(this.lastId).setAttribute('aria-selected', false);
				}

				e.target.setAttribute('aria-selected', true);
				this.lastId = e.target.id;

				buttonCtrl.hidePanel();
				value = '#' + value;
				buttonCtrl.color(value);
				buttonCtrl._value = value;
				//editor.execCommand(buttonCtrl.settings.selectcmd, false, value);
			}
		}

		function onButtonClick() {
			var self = this;
			if (self._color) {
				editor.execCommand(self.settings.selectcmd, false, self._color);
			}
		}
   

		var menuOptions = [
			{
				text: 'Map',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Map',
						body: [
							{
								type: 'textbox',
								name: 'latitude',
								label: 'Latitude',
								value: ''
							},
							{
								type: 'textbox',
								name: 'longitude',
								label: 'Longitude',
								value: ''
							},
							{
								type: 'listbox',
								name: 'color',
								label: 'Color',
								values : [
									{ text: 'Blue', value: 'blue' },
									{ text: 'Red', value: 'red' },
									{ text: 'Green', value: 'green' },
									{ text: 'Yellow', value: 'yellow' },
									{ text: 'Purple', value: 'purple' },
									{ text: 'Orange', value: 'orange' }
								]
							},
							{
								type: 'textbox',
								name: 'zoom',
								label: 'Zoom',
								value: '16'
							},
							{
								type: 'textbox',
								name: 'width',
								label: 'Width',
								value: '100%'
							},
							{
								type: 'textbox',
								name: 'height',
								label: 'Height',
								value: '600px'
							},
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bt-map latitude="' + e.data.latitude + '" longitude="' + e.data.longitude + '" zoom="' + e.data.zoom + '" width="' + e.data.width + '" height="' + e.data.height + '"]');
						}
					});
				}
			}, 
			{
				text: 'Poll',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Poll',
						body: [
							{
								type: 'textbox',
								name: 'poll_id',
								label: 'Poll ID',
								value: ''
							}
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bt-poll id="'+ e.data.poll_id +'"]');
						}
					});
				}
			},
			{
				text: 'Quiz',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Quiz',
						body: [
							{
								type: 'textbox',
								name: 'quiz_id',
								label: 'Quiz ID',
								value: ''
							}
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bt-quiz id="'+ e.data.quiz_id +'"]');
						}
					});
				}
			},
			{
				text: 'Likes',
				onclick: function() {
					editor.insertContent( '[bt-likes]');
				}
			},
			{
				text: 'Column',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Column',
						body: [
							/*{
								type: 'listbox',
								name: 'grid',
								label: 'What is the grid base?',
								values: [
									{ text: '12', value: '12' },
									{ text: '5', value: '5' },
									{ text: '4', value: '4' },
									{ text: '2', value: '2' },
									{ text: '3', value: '3' }
								]
							},*/
							{
								type: 'listbox',
								name: 'span',
								label: 'Col width?',
								values: [
									{ text: '1', value: '1' },
									{ text: '2', value: '2' },
									{ text: '3', value: '3' },
									{ text: '4', value: '4' },
									{ text: '5', value: '5' },
									{ text: '6', value: '6' },
									{ text: '7', value: '7' },
									{ text: '8', value: '8' },
									{ text: '9', value: '9' },
									{ text: '10', value: '10' },
									{ text: '11', value: '11' },
									{ text: '12', value: '12' }
								]
							},

							{
								type: 'listbox',
								name: 'offset',
								label: 'Col offset?',
								values: [
									{ text: '0', value: '0' },
									{ text: '1', value: '1' },
									{ text: '2', value: '2' },
									{ text: '3', value: '3' },
									{ text: '4', value: '4' },
									{ text: '5', value: '5' },
									{ text: '6', value: '6' },
									{ text: '7', value: '7' },
									{ text: '8', value: '8' },
									{ text: '9', value: '9' },
									{ text: '10', value: '10' },
									{ text: '11', value: '11' },
								]
							},

							{
								type: 'listbox',
								name: 'mdspan',
								label: 'Medium Col width (optional for tablet)?',
								values: [
									{ text: '', value: '' },
									{ text: '1', value: '1' },
									{ text: '2', value: '2' },
									{ text: '3', value: '3' },
									{ text: '4', value: '4' },
									{ text: '5', value: '5' },
									{ text: '6', value: '6' },
									{ text: '7', value: '7' },
									{ text: '8', value: '8' },
									{ text: '9', value: '9' },
									{ text: '10', value: '10' },
									{ text: '11', value: '11' },
									{ text: '12', value: '12' }
								]
							},

							{
								type: 'listbox',
								name: 'mdoffset',
								label: 'Medium Col offset (optional for tablet)?',
								values: [
									{ text: '', value: '' },
									{ text: '0', value: '0' },
									{ text: '1', value: '1' },
									{ text: '2', value: '2' },
									{ text: '3', value: '3' },
									{ text: '4', value: '4' },
									{ text: '5', value: '5' },
									{ text: '6', value: '6' },
									{ text: '7', value: '7' },
									{ text: '8', value: '8' },
									{ text: '9', value: '9' },
									{ text: '10', value: '10' },
									{ text: '11', value: '11' },
								]
							},

							{
								type: 'listbox',
								name: 'smspan',
								label: 'Small Col width (optional for mobile)?',
								values: [
									{ text: '', value: '' },
									{ text: '1', value: '1' },
									{ text: '2', value: '2' },
									{ text: '3', value: '3' },
									{ text: '4', value: '4' },
									{ text: '5', value: '5' },
									{ text: '6', value: '6' },
									{ text: '7', value: '7' },
									{ text: '8', value: '8' },
									{ text: '9', value: '9' },
									{ text: '10', value: '10' },
									{ text: '11', value: '11' },
									{ text: '12', value: '12' }
								]
							},

							{
								type: 'listbox',
								name: 'smoffset',
								label: 'Small Col offset (optional for tablet)?',
								values: [
									{ text: '', value: '' },
									{ text: '0', value: '0' },
									{ text: '1', value: '1' },
									{ text: '2', value: '2' },
									{ text: '3', value: '3' },
									{ text: '4', value: '4' },
									{ text: '5', value: '5' },
									{ text: '6', value: '6' },
									{ text: '7', value: '7' },
									{ text: '8', value: '8' },
									{ text: '9', value: '9' },
									{ text: '10', value: '10' },
									{ text: '11', value: '11' },
								]
							},

							{
								type: 'textbox',
								multiline: true,
								minHeight: 100,
								rows: 10,
								name: 'content',
								label: 'Content',
								value: '',
							},
							
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bt-col span="'+ e.data.span +'" offset="'+e.data.offset+'" md_span="'+ e.data.mdspan +'" md_offset="'+e.data.mdoffset+'" sm_span="'+ e.data.smspan +'" sm_offset="'+e.data.smoffset+'"]'+ e.data.content +'[/bt-col]');
						}
					});
				}
			},
			{
				text: 'Tabs',
				menu: [
					{
						text: 'Tabs Open Container',
						onclick: function(){
							editor.windowManager.open({
								title: 'Insert Tabs Open Container',
								body: [
									{
										type: 'listbox',
										name: 'color',
										label: 'Tab Color',
										values : [
											{ text: 'Blue', value: 'blue' },
											{ text: 'Red', value: 'red' },
											{ text: 'Green', value: 'green' },
											{ text: 'Yellow', value: 'yellow' },
											{ text: 'Purple', value: 'purple' },
											{ text: 'Orange', value: 'orange' },
											{ text: 'Dark', value: 'dark' },
											{ text: 'Light', value: 'light' }
										]
									},
									{
										type: 'listbox',
										name: 'style',
										label: 'Tab Content Color',
										values : [
											{ text: 'Blue', value: 'blue' },
											{ text: 'Red', value: 'red' },
											{ text: 'Green', value: 'green' },
											{ text: 'Yellow', value: 'yellow' },
											{ text: 'Purple', value: 'purple' },
											{ text: 'Orange', value: 'orange' },
											{ text: 'Dark', value: 'dark' },
											{ text: 'Light', value: 'light' }
										]
									},
									{
										type: 'listbox',
										name: 'direction',
										label: 'Tab Direction',
										values : [
											{ text: 'Top', value: 'tab-default' },
											{ text: 'Left', value: 'tab-left' },
											{ text: 'Right', value: 'tab-right' },
											{ text: 'Bottom', value: 'tab-bottom' },
										]
									}
								],
								onsubmit: function(e) {
					                editor.insertContent( '[bt-tabs direction="'+ e.data.direction +'" style="'+ e.data.style +'" id="'+ e.data.color +'"]');
								}
							});
						}
					},
					{
						text: 'Tab Content',
						onclick: function(){
							editor.windowManager.open({
								title: 'Insert Tab Content',
								body: [
									{
										type: 'textbox',
										name: 'title',
										label: 'Tab Title',
										value : ''
									},
									{
										type: 'textbox',
										multiline: true,
										minHeight: 100,
										rows: 10,
										name: 'content',
										label: 'Content',
										value: '',
									}
								],
								onsubmit: function(e) {
					                editor.insertContent( '[bt-tab title="'+ e.data.title +'"]'+e.data.content+'[/bt-tab]');
								}
							});
						}
					},
					{
						text: 'Tabs Close Container',
						onclick: function(){
							editor.insertContent( '[/bt-tabs]');
						}
					}
				]
			},
			{
				text: 'Toggles',
				menu: [
					{
						text: 'Toggles Open Container',
						onclick: function(){
					        editor.insertContent( '[bt-toggles]');
						}
					},
					{
						text: 'Toggle Content',
						onclick: function(){
							editor.windowManager.open({
								title: 'Insert Tab Content',
								body: [
									{
										type: 'textbox',
										name: 'title',
										label: 'Title',
										value : ''
									},
									{
										type: 'listbox',
										name: 'state',
										label: 'State',
										values : [
											{ text: 'Default', value: 'default' },
											{ text: 'Active', value: 'active'}
										]
									},
									{
										type: 'textbox',
										multiline: true,
										minHeight: 100,
										rows: 10,
										name: 'content',
										label: 'Content',
										value: '',
									}
								],
								onsubmit: function(e) {
					                editor.insertContent( '[bt-toggle title="'+ e.data.title +'"]'+e.data.content+'[/bt-toggle]');
								}
							});
						}
					},
					{
						text: 'Toggles Close Container',
						onclick: function(){
							editor.insertContent( '[/bt-toggles]');
						}
					}
				]
			},
			{
				text: 'Alert',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Alert',
						body: [
							{
								type: 'listbox',
								name: 'color',
								label: 'Color',
								values: [
									{ value: 'blue', text: 'Blue' },
									{ value: 'white', text: 'White' },
									{ value: 'red', text: 'Red' },
									{ value: 'yellow', text: 'Yellow' },
									{ value: 'green', text: 'Green' },
									{ value: 'gray', text: 'Gray' }
								]
							},
							{
								type: 'textbox',
								name: 'content',
								label: 'Content',
								multiline: true,
								minHeight: 100,
								rows: 10,
								value: '',
							}
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bt-alert color="'+ e.data.color +'"]' + e.data.content + '[/bt-alert]');
						}
					});
				}
			},
			{
				text: 'Button',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Button',
						body: [
							{
								type: 'listbox',
								name: 'color',
								label: 'Color',
								values: [
									{ value: 'blue', text: 'Blue' },
									{ value: 'orange', text: 'Orange' },
									{ value: 'red', text: 'Red' },
									{ value: 'pink', text: 'Pink' },
									{ value: 'purple', text: 'Purple' },
									{ value: 'yellow', text: 'Yellow' },
									{ value: 'green', text: 'Green' },
									{ value: 'dark', text: 'Dark' },
									{ value: 'light', text: 'Light' },
								]
							},
							{
								type: 'textbox',
								name: 'url',
								label: 'URL Target',
								value: '',
							},
							{
								type: 'listbox',
								name: 'target',
								label: 'Target',
								values: [
									{ value: '_blank', text: '_blank' },
									{ value: '_self', text: '_self' },
									{ value: '_parent', text: '_parent' },
									{ value: '_top', text: '_top' },
								]
							},
							{
								type: 'listbox',
								name: 'size',
								label: 'Size',
								values: [
									{ value: 'small', text: 'Small' },
									{ value: 'medium', text: 'Medium' },
									{ value: 'large', text: 'Large' },
								]
							},
							{
								type: 'listbox',
								name: 'style',
								label: 'Style',
								values: [
									{ value: 'gradient', text: 'Gradient' },
									{ value: 'flat', text: 'Flat' },
								]
							},
							{
								type: 'listbox',
								name: 'type',
								label: 'Type',
								values: [
									{ value: 'square', text: 'Square' },
									{ value: 'round-corner', text: 'Round Corner' },
									{ value: 'round', text: 'Round' },
								]
							},
							{
								type: 'listbox',
								name: 'block',
								label: 'Full Width Block',
								values: [
									{ value: 'no', text: 'No' },
									{ value: 'yes', text: 'Yes' },
								]
							},
							{
								type: 'textbox',
								name: 'label',
								label: 'Label',
								value: '',
							},
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bt-button block="'+e.data.block+'" size="'+e.data.size+'" url="'+e.data.url+'" style="'+e.data.style+'" type="'+e.data.type+'" target="'+e.data.target+'" color="'+ e.data.color +'"]' + e.data.label + '[/bt-button]');
						}
					});
				}
			}, 
			{
				text: 'Icon',
				onclick: function() {
					editor.windowManager.open({
						title: 'Insert Icon',
						body: [
							{
								type: 'textbox',
								name: 'icon',
								label: 'Icon Class',
								value: '',
							},
							{
								type: 'listbox',
								name: 'size',
								label: 'Size',
								values: [
									{ text: '1x', value: 'bi-1x' },
									{ text: '2x', value: 'bi-2x' },
									{ text: '3x', value: 'bi-3x' },
									{ text: '4x', value: 'bi-4x' },
									{ text: '5x', value: 'bi-5x' },
								],
							},
						],
						onsubmit: function(e) {
			                editor.insertContent( '[bt-icon icon="'+e.data.icon+'" size="'+e.data.size+'" ]');
						}
					});
				}
			},
			
		];

		editor.addButton( 'bon_toolkit_button', {
			icon: 'bt-shortcode-icon',
			title: 'Insert BT Shortcode',
			type: 'menubutton',
			menu: menuOptions
		});
	});
})();