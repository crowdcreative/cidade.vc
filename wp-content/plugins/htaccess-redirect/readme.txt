=== .htaccess Redirect ===
Contributors: enethrie, aubreypwd
Donate link: http://aubreypwd.com
Tags: htaccess, .htaccess, redirect, outside, links
Requires at least: 2.0
Tested up to: 3.8
Stable tag: 0.3.1

This plugin modifies your .htaccess file to redirect requests to new locations.

== Description ==

This plugin modifies your .htaccess file to redirect requests to new locations. This is especially useful (and intended) to redirect requests to web locations/pages outside of your WordPress installation to pages now in WordPress. For instance, you could redirect http://example.com/old/raw/web/user/enethrie/my... to http://example.com/enethrie/ or http://somewhereelse.com/

== Installation ==

To install this plugin just copy the .php file to your wp-content/plugins folder, or use WordPress Dashboard to install this plugin by searching for it.

== Changelog ==

= 0.3.1 =
- Fixes issue where plugin would crash when using older versions of PHP (see http://goo.gl/9ljpRP).

= 0.3 =
- Fix to [Plugin Isn't Working since WP update](http://wordpress.org/support/topic/plugin-htaccess-redirect-plugin-isnt-working-since-wp-update?replies=1)

= 0.2 =
- Fixes to filenames with spaces and some regex symbols

= 0.1 =
- First releases, please visit https://bitbucket.org/enethrie/.htaccess-redirect for more.