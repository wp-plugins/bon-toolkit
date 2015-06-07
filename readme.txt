===Bon Toolkit===
Contributors: nackle2k10
Donate Link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=d%2enackle%40gmail%2ecom&lc=ID&item_name=Bonfirelab&item_number=bon%2dtoolkit&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: widgets, icons, retina, social, shortcode
License: GPLv2 or later
Requires at least: 3.5.0
Tested up to: 4.2.2
Stable tag: 1.3.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The Bon Toolkit extends functionality to Bonfirelab's Themes. It provides custom post types build, widgets, and some shortcodes.

==Description==

**Portfolio**

If your theme supports portfolio, the Toolkit adds a Portfolio custom post type. Once enabled, you will see a Portfolio Post type in the menu item, which you can use to create your portfolio.

**Quiz**

If your theme supports quiz post type, the Toolkit adds quiz support. Once enabled, you will be provided with a simple quiz tools. 

**Poll**

If your theme supports poll post type, the Toolkit adds poll support. Once enabled, you will be provided with a simple polling features.

**Toolkit Widgets**

The Toolkit adds Twitter, Flickr, Dribbble, Video (Support Self Hosted and Third Party Hosted) and more than 20 Retina Ready social media icons to your theme.

**Toolkit Shortcodes**

The Toolkit also adds Google Maps, Tabs, Accordions, Buttons, Alerts, Column grid, Entry likes shortcodes to your theme.

**Toolkit Page Builder**

The Toolkit handle the page builder admin. The Bon Theme Framework is required to use the page builder.


==Frequently Asked Questions==

= What is this plugin and why do I need it? =

The Bon Toolkit provides extra functionality to the collection of themes from Bonfirelab. The Toolkit adds various widgets (Twitter, Flickr, Dribbble and social icons), and a few custom post types. The plugin is not a requirement to use themes from bonfirelab, but it will extend the themes to function as you see them in the demos.

= Why are the some of the Post Type Settings available in one of my Bonfirelab themes but not the other? =

The Toolkit plugin only shows options / settings for themes that support it. If your theme does not support either feature, their settings will not be shown.

= Can I use this plugin with other themes? =

This toolkit was developed to extend the functionality of Bonfirelab themes, however you can still use some parts of the plugin features such as widget in other themes. Advanced features like Custom Post Types will only work with Bonfirelab Themes due to framework dependant.

==Screenshots==

1. The Bon Toolkit widget will appear in your list of available widgets.
2. A view of the available settings for the Toolkit.
3. Widgets that will appear in your Widgets area once activated.
4. Retina social icons widget active in a footer.

==Changelog==

= v1.0 - July 31th, 2013 =
* Original Release.
= v1.0.1 - August 20th, 2013 =
* Added parameter $value in bon_toolkit_builder_render_{element_name}_output
= v1.0.2 - August 28th, 2013 =
* Added New image block element on page builder
* Added Contact Form Element Output
* Added Map Element in Builder
* Added Contact Form CSS
* Fix undefined element type in builder.php
* Tidy up repeated code in builder-interface.php
* Added Upload function for image block
= v1.0.3 - September 4th, 2013 =
* Mayor update for widgets
* Mayor update in builder-interface.php
= v1.0.4 - September 6th, 2013 =
* Fix Accidentally removed contact form widget output
= v1.0.5 - September 9th, 2013 =
* Added Social Share Button
= v1.0.6 - September 10th, 2013 =
* Minimized Script and CSS
= v1.0.7 - September 10th, 2013 =
* Add filter to social share
= v1.0.8 - September 19th, 2013 =
* Fixed the shortcode_exists problem for WP 3.5
= v1.0.9 - September 21th, 2013 =
* Google Map No Longer using Api Key
= v1.1.0 - October 2nd, 2013 =
* Update Page builder interface
* Add widget element for Page builder
= v1.1.1 - November 5th, 2013 =
* Fix Map in page builder
= v1.1.2 - November 8th, 2013 =
* Fix Call To Action Render in Page Builder
= v1.1.3 - November 8th, 2013 =
* Fix Unescapped input in Builder.php
= v1.1.4 - December 11st, 2013 =
* Add Language Files for France and Spanish
* Add Activation Hook
= v1.1.5 - January 2nd, 2014 =
* Add Social Share Options to Page
= v1.1.6 - January 16th, 2014 =
* Fix Map Instance
= v1.1.7 - February 6th, 2014 =
* Fix Checkbox Options
= v1.1.8 - March 18th, 2014 =
* Fix Post Content h1 Tag on non homepage pages for SEO improvement
= v1.1.9 - April 11st, 2014 =
* Added Color Callback to contact form
= v.1.2.0 - May 22nd, 2014 =
* Removed Post Type Icon Settings
* Added ID column to Featured Slider table
* Update font awesome icon to v4
* Update all font awesome default prefix
* Remove font awesome prefix options
* Update tinymce shortcode button


= v.1.2.1 - July 7th, 2014 =
*Provide backward compatibility with bonframework version below 1.2

= v.1.2.2 - July 8th, 2014 =
*Fix Builder instance for supported post type

= v.1.2.5 - September 8th, 2014 =
*Returning page builder support for other post type
*Add conditional to the google json return

= v.1.2.6 - September 18th, 2014 =
*Fixed Map Street View Button
*Fixed Page Builder Modal on WP 4.0

= v.1.2.7 - October 15th, 2014 =
*Allow http or https protocol when queueing google map script

= v.1.2.8 - October 16th, 2014 =
*Added new draggable and scrollwheel parameter to the bt-map shortcode

= v.1.2.9 - October 21st, 2014 =
*Fix Element class in page builder

= v.1.3.0 - February 10th, 2015 =
*Add Target for Call to Action Element
*Fix BT Page Builder Button on the editor

= v.1.3.1 - May 22th, 2015 =
*Allow Contact Form to accept phone parameter

= v.1.3.2 - May 22th, 2015 =
*Target options for image block link

==Installation==

This plugin can be installed directly from your site.

1. Log in and navigate to Plugins &rarr; Add New.
2. Type "Bon Toolkit" into the Search input and click the "Search Widgets" button.
3. Locate the Bon Toolkit in the list of search results and click "Install Now".
4. Click the "Activate Plugin" link at the bottom of the install screen.
5. Navigate to Settings &rarr; Bon Toolkit to modify the plugin's settings. The widgets will be available in Appearance &rarr; Widgets.

It can also be installed manually.

1. Download the "Bon Toolkit" plugin from WordPress.org.
2. Unzip the package and move to your plugins directory.
3. Log into WordPress and navigate to the "Plugins" screen.
4. Locate "Bon Toolkit" in the list and click the "Activate" link.
5. Navigate to Settings &rarr; Bon Toolkit to learn about the plugin's features. The widgets will be available in Appearance &rarr; Widgets.

==Upgrade Notice==


