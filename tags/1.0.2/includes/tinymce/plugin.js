(function ()
{
	// create bonShortcodes plugin
	tinymce.create("tinymce.plugins.bonToolkitShortcodes",
	{
		
		createControl: function ( btn, e )
		{
			if ( btn == "bon_toolkit_button" )
			{	
				var a = this;

				// adds the tinymce button
				btn = e.createMenuButton('bon_toolkit_button', {
                    title: "Insert Bon Toolkit Shortcode",
					image: "../wp-content/plugins/bon-toolkit/assets/images/icon.png",
					icons: false
                });

                btn.onRenderMenu.add(function (c, b)
				{	
					a.addImmediate( b, 'Map', '[bt-map color="blue" latitude="" longitude="" zoom="16" width="100%" height="600px"]<br/><br/>');
					a.addImmediate( b, 'Poll', '[bt-poll id=""]<br/><br/>');
					a.addImmediate( b, 'Quiz', '[bt-quiz id=""]<br/><br/>');
					a.addImmediate( b, 'Likes', '[bt-likes]<br/><br/>');
					a.addImmediate( b, 'Columns', '[bt-col grid="12" span="3"] [/bt-col]<br/><br/>');
					a.addImmediate( b, 'Tabs', '[bt-tabs color="orange"]<br/><br/>[/bt-tabs]<br/><br/>');
					a.addImmediate( b, 'Tab', '[bt-tab title="Tab Title"]<br/><br/>[/bt-tab]<br/>');
					a.addImmediate( b, 'Toggles', '[bt-toggles]<br/><br/>[/bt-toggles]<br/><br/>');
					a.addImmediate( b, 'Toggle', '[bt-toggle title="Togggle Title" state=""]<br/><br/>[/bt-toggle]<br/>');
					a.addImmediate( b, 'Alert', '[bt-alert color="red"]content here[/bt-alert]<br/>');

					a.addImmediate( b, 'Button', '[bt-button url="" target="blank" color="" style="grad" size="" type=""]content here[/bt-button]');
				});
                
                return btn;
			}
			
			return null;
		},
		
		addImmediate: function ( ed, title, sc) {
			ed.add({
				title: title,
				onclick: function () {
					tinyMCE.activeEditor.execCommand( "mceInsertContent", false, sc )
				}
			})
		},
		getInfo: function () {
			return {
				longname: 'Bon Toolkit Shortcode',
				author: 'Hermanto LIm',
				authorurl: 'http://themeforest.net/user/nackle2k10/',
				infourl: 'http://wiki.moxiecode.com/',
				version: "1.0"
			}
		}
	});
	
	// add bonShortcodes plugin
	tinymce.PluginManager.add("bonToolkitShortcodes", tinymce.plugins.bonToolkitShortcodes);
})();