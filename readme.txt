=== Plugin Installer Speedup ===
Contributors: szepe.viktor
Donate link: https://szepe.net/wp-donate/
Tags: administration, installation, upload plugins
Requires at least: 4.0
Tested up to: 4.7.1
Stable tag: 0.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Make plugin installation faster.

== Description ==

Speed up plugin installation.

* Don't load featured plugins.
* Make Search Plugins button visible.
* Set focus to search field.
* Skip plugin install confirmation.
* Add admin menu items under "Plugins" for Upload Plugin and Favorites.
* Add admin bar menu item under "+ New".
* Remove "-master" from (mainly GitHub) ZIP archive names.

No admin page for this plugin. Ready to go right after activation.

[GitHub repository](https://github.com/szepeviktor/plugin-installer-speedup)

The feature of plugin upload from URL has been moved to a
[MU plugin](https://github.com/szepeviktor/wordpress-plugin-construction/blob/master/mu-plugin-upload-from-url/plugin-upload-from-url.php).

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `plugin-install-speedup.php` and the `js` folder to the `/wp-content/plugins/plugin-installer-speedup/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Is this plugin dangerous? =

Yes it is. You can easily install any plugin in two seconds.

== Changelog ==

= 0.2.2 =
* Make search visible again
* WordPress 4.7.1 compatibility
* Document "-master" feature

= 0.2.1 =
* Mika removed

= 0.2 =
* Set focus to search field
* Admin bar menu in "+ New"

= 0.1 =
* Initial release
