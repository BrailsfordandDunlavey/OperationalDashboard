=== Disable Gravity Forms Fields ===
Contributors: EngageWP
Tags: Gravity Forms
Tested up to: 4.2
Stable tag: 1.3
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a simple plugin that lets Gravity Forms users set a form field's attribute to either disabled or readonly.

== Description ==

Simply install the plugin and add the appropraite CSS class to the form field you want to assign an attribute. This is done within the Form Editor under the Advanced tab of the field. The classes are "disabled" for the disabled attribute and "readonly" for the readonly attribute (don't include the quotation marks).

== Installation ==

This section describes how to install the plugin and get it working.

<h2>Automatically</h2>

1. Search for Disable Gravity Forms Fields in the Add New Plugin section of the WordPress admin
2. Install & Activate

<h2>Manually</h2>

1. Download the zip file and upload `disable-gf-fields` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= How do I use this plugin? =

This plugin requires jQuery to be loaded. If your WordPress installation does not currently load jQuery, you must load it for the plugin to work. I like to load jQuery using the Google Hosted Libraries API. You can load jQuery via Google by placing this snippet in your (child) theme's functions.php file:

`
//* Load jQuery via Google Hosted Libraries
add_action('init', 'rv_load_google_jquery');
function rv_load_google_jquery() {
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', false, '1.11.2');
	}
}
`

Once you've installed and activated the plugin, just add a the appropriate class to your field ("disabled" or "readonly").

= What is the difference between the disabled and readonly attributes? =

Both attributes set a field to be uneditable by the user. This means that the field will not accept any input from a user. However, there are a few important differences between the two attributes. Here's a good overview of <a href="http://www.dotnet-tricks.com/Tutorial/aspnet/60a5170712-Difference-between-disabled-and-read-only-attributes.html" target="_blank">disabled vs. readonly attributes</a>.

= Do you offer support for this plugin? =

This plugin is very simple so it's unlikely you'll need support. If you do have any questions, feel free to <a href="http://www.engagewp.com/contact" target="_blank">email me</a>. 

== Screenshots ==

1. Gravity Forms Advanced Tab w/ a CSS class of "disabled". This will set the field's attribute to disabled. Alternatively, you can enter the CSS class "readonly" to set the attribute to readonly.

== Changelog ==

= 1.3 =
* Wrapped jQuery dependency in array in the enqueue call
* Removed some specificity from the jQuery selectors (will work for any <code>input</code> with the appropriate classes)

= 1.2 =
* Added the jQuery dependency to wp_enqueue_scripts() call

= 1.1 =
* Added readonly attribute support

= 1.0 =
* Launch version