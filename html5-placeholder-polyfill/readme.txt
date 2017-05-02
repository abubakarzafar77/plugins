=== HTML5 Placeholder Polyfill ===
Contributors:      McGuive7, MIGHTYminnow
Donate link:       http://mightyminnow.com
Tags:              placeholder, form, polyfill, html5, input, textarea, placeholder.js, jQuery, internet explorer
Requires at least: 3.0
Tested up to:      4.0
Stable tag:        1.2.0
License:           GPLv2 or later
License URI:       http://www.gnu.org/licenses/gpl-2.0.html

Mimics HTML5 placeholder behavior in browsers that don't natively support it.

== Description ==
The HTML5 Placeholder Plugin simulates placeholder functionality for inputs and textareas, which is particularly handy in [browsers that still need some work](http://caniuse.com/#search=placeholder).

A **[MIGHTYminnow](http://mightyminnow.com)** plugin.

= Features =
* No setup necessary, works immediately after installing
* Super lightweight, less than 3kb minified jQuery
* Automatically detects for placeholder support - no need for extra feature detection like Modernizr
* Based on Mathias Bynens' widely popular and well tested polyfill - thanks Mathias!

= How to use =
This plugin creates and uses a placeholder() jQuery function, and invokes it for inputs and textareas. This happens automatically as soon as you install it. If for some crazy reason you would like to invoke it on other elements aside from inputs and textareas, you can do so with you own jQuery, like so:
	
	jQuery('yourElement').placeholder()


== Installation ==

= Manual Installation =

1. Upload the entire `/html5-placeholder-polyfill` directory to the `/wp-content/plugins/` directory.
2. Activate HTML5 Placeholder Polyfill through the 'Plugins' menu in WordPress.
3. All set, no additional configuration needed. Placeholders will now work on inputs and textareas in all browsers.

== Frequently Asked Questions ==

= How does this plugin work? =

This plugin implements an elegant jQuery script written by Mathias Bynens to simulate proper placeholder functionality in browsers that don't natively support it. The current version of the plugin is based on the following version of Mathias' code: https://github.com/mathiasbynens/jquery-placeholder/blob/fdd8d253edc8708fbe5d3ecf9a17a919713a3733/jquery.placeholder.js. It may be updated in the future if any improvements are made on his end.

= I need to modify the plugin behavior, what should I do? =

1. Write to me with your request/suggestion - maybe the plugin should be improved for all.
2. If you'd rather have at it yourself, more power to you. The plugin utilizes jQuery that has been minified (html5-placeholder-polyfill.min.js), but the unminified version is also included (html5-placeholder-polyfill.js). You can tinker with the unminified version, minify it, and copy it into html5-placeholder-polyfill.min.js.

== Screenshots ==
1. Before installing the plugin (IE8, IE9, etc.)
2. After installing the plugin

== Changelog ==

= 1.2.0
* Functionality added to admin and login screen.
* Added support for SCRIPT_DEBUG (non-minified jQuery is included if SCRIPT_DEBUG is set to true).

= 1.1.0 =
* Removed extra blank lines at end of main php file causing the following error: "The plugin generated 2 characters of unexpected output during activation. If you notice “headers already sent” messages, problems with syndication feeds or other issues, try deactivating or removing this plugin."

= 1.0.0 =
* Switching to non-beta version number

= 0.1.0 =
* First release

== Upgrade Notice ==

= 1.2.0
* Functionality added to admin and login screen.
* Added support for SCRIPT_DEBUG (non-minified jQuery is included if SCRIPT_DEBUG is set to true).

= 1.1.0 =
* Removed extra blank lines at end of main php file causing the following error: "The plugin generated 2 characters of unexpected output during activation. If you notice “headers already sent” messages, problems with syndication feeds or other issues, try deactivating or removing this plugin."

= 1.0.0 =
* No real need to upgrade, just switching to non-beta version number

= 0.1.0 =
First Release
