=== Reduce HTTP Requests, Disable Emojis & Disable Embeds, Speedup WooCommerce ===
Contributors: optimisation.io, hosting.io
Tags: Disable Emoji, Disable Embeds, Disable Gravatars, Remove Querystrings, Reduce HTTP Requests, speedup WooCommerce, Close comments, Optimization, FREE CDN
Requires at least: 4.5
Tested up to: 4.9.8
Stable tag: 1.5.21
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Reduce HTTP requests - Disable Emojis, Disable Gravatars, Disable Embeds and Remove Querystrings. SpeedUp WooCommerce, Added support to disable pingbacks, disable trackbacks, close comments after 28 days, Added the ability to force pagingation after 20 posts,
Disable WooCommerce scripts and CSS on non WooCommerce Pages, Disable RSS, Disable XML-RPC, Disable Autosave, Remove Windows Live Writer tag, Remove Shortlink Tag, Remove WP API from header and
 many more features to help speed and SEO gains. Free CDN now included in cache addon.

== Description ==
<strong>Reduce HTTP requests</strong> - Disable Emojis, Disable Gravatars, Disable Embeds and Remove Querystrings. SpeedUp WooCommerce, Added support to disable pingbacks, disable trackbacks, close comments after 28 days, Added the ability to force pagingation after 20 posts,
Disable WooCommerce scripts and CSS on non WooCommerce Pages, Disable RSS, Disable XML-RPC, Disable Autosave, Remove Windows Live Writer tag, Remove Shortlink Tag, Remove WP API from header and
 many more features to help speed and SEO gains.  Now includes <strong>Disable Comments, Heartbeat Control, Selective Disable</strong>

 <strong>**NEW Features:**</strong>
 Better Stats on Dashboard
 Disable loading dashicons on front end if admin bar disabled
 Disable Author Pages

Disabling Emojis does not disable emoticons, it disables the support for Emojis added since WP 4.2 and removes 1 HTTP request.<br>

Disabling Embeds  - script that auto formats pasted content in the visual editor, eg videos, etc. Big issue with this script is it loads on every
single page. You can still use the default embed code from YouTube, Twitter etc to included content.

Remove Query Strings: If you look at the waterfall view of your page load, you will see your query strings end in something like ver=1.12.4.
These are called query strings and help determine the version of the script. The problem with query strings like these is that it isn't very efficient for caching purposes and sometimes prevents caching those assets altogether.  If you are using a CDN already, you can ignore this.

Disabling Gravatars is completely optional, advise, if you don't use them, disable as it gets rid of one more useless HTTP request.

General Performance improvements: Added support for : disable ping/trackbacks, close comments after 28 days, force pagingation after 20 posts, Disable WooCommerce scripts and CSS on non WooCommerce Pages.

<b>Have an idea ?</b><br>
<a href="https://github.com/hosting-io/wp-disable">Public repo on GitHub</a> if you would like to contribute or have any ideas to add.

<b>Docs & Support</b><br>
The <a href="https://optimisation.io/faq/">documentation is an on-going project</a>, so please bare with us as we update.  If you would like to help with the documentation, please get in touch.



== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings->WP Disable screen to configure the plugin


== Frequently Asked Questions ==

= I would like to contribute/I have an idea =

<a href="https://github.com/hosting-io/wp-disable">Public repo on GitHub</a> if you would like to contribute or have any ideas to add.

= Do I still need caching ? =

Yes, We have just release a <a href="https://wordpress.org/plugins/cache-performance/">WordPress Caching plugin</a> which is really easy to setup and includes a built in CD-rewrite rule system.<br>
Now also comes with a free cdn

= What about Minification, do I still need it? =

Yes, you absolutely do, and none come close to the awesome <a href="https://en-gb.wordpress.org/plugins/autoptimize/"> Autoptimize</a> by Frank Goossens.

= Do I still need a CDN ? =

Yes, Our Cache plugin now comes with a free CDN. <br>

= What about my Image Compression =

You can try our <a href="https://wordpress.org/plugins/wp-image-compression/">Free Image Compression plugin</a> which has really good compression ratios with little to no loss of image quality.

== Screenshots ==
1. Plugin Interface
2. Pingdom Report
4. Fast Hosting Servers make a difference to overall performance
4. Because Speed Matters (tm)


== Changelog ==
= 1.5.21 =
* New setting to delete all comments completely, great for existing sites that have a lot of old spam comments, but want to turn them off and clean up the database at the same time.
* Can be found under Admin in Remove Excess Bloat area

= 1.5.20 =
* Added a note on dashboard about what to look for to disable our plugins.  People have started leaving negative feedback cause can't remember what they installed. Hopefully, this clears it up
* Removed donation Requests
* Few other small bugs fixed

= 1.5.19 =
* IMPORTANT -- please disable and then update the plugin to enable the auto update to work. Something went wrong with the last update and it seems to be fo
* Freemius Removed

= 1.5.18 =
* Fix Auto Update  - IMPORTANT -- please disable and re-enable the plugin to enable the auto update to work.

= 1.5.17 =
* Removed Freemius
* Added better update notifications


= 1.5.16 =
* General bug fixes and prep for CDN functionality (Premium addon) coming soon

= 1.5.15 =
* Minor CSS _updates
* Bug fix with DNS-prefetch
* Fixed conflicts with other optimisation.io Plugins
* New feature - Disable Gravatars only in Comments


= 1.5.14 =
* Started on Documentation (can be found here https://optimisation.io/faq/)
* Added donation button - help us make this the best optimisation suite available on the repo.  Every $ donated helps.
* Added SEO Tab
* Added ability to remove Duplicate names in breadcrumbs
* Added Remove Yoast SEO comments
* Tested on Gutenberg
* Tested on WP 4.9
* Remove Dequeue from some functions
* Disabled Dashicons in Customizer
* Minor bug fixes as per support forum


= 1.5.13 =
* Added Settings link on main Installed Plugin view
* General code tidy up
* PHP 7.1 compatible
* WP 4.8.2 tested

= 1.5.12 =
* WooCommerce bugs fixed
* Syntax error fixed
* General improvements to GA Offload (Some cases GA code may still not work, does not appear to be a fix for this, if this happens on yours, please ignore the feature)

= 1.5.11 =
* WooCommerce tab not displaying fixed

= 1.5.1 =
* More visual clean-ups
* Removed all web fonts
* Minor bug fix on reporting on dashboard
* Plugin is now under 240kb

= 1.5.0 =
* Finished redesign of plugin
* All stats now in one central dashboard
* Removed sidebar navigation completely
* Removed Freemius
* Added check for WooCommerce, so Woo related stuff only shows if Woo is installed
* Much tighter integration between the 3 optimisation plugins
* Removed old/excess files


= 1.4.5 =
* More visual fixes/general tidy up
* Added exception to Google Maps so can be enabled per page
* Minor code fixes
* Moved Google Analytics to sidebar/addons

= 1.4.4 =
* Added ability to stop (disable) admin notices from showing
* removed the stats sub menu item, so everything is now at the top level
* "local-ga.js" file was created on activation, changed the way this works so it will work now independent of when adding the GA code

= 1.4.3 =
More dashboard visual tweaks.
No new features, but this is a stepping stone.

= 1.4.2 =
* General tidy up on dashboard

= 1.4.1 =
* removed third party errors out of our dashboard to the top of the page where they belong
* cleaned out redundant data in GA cache file

= 1.4.0 =
* New Dashboard Design (Work in progress)
* Added Average load time of pages to stats
* Remove Comments navigation when comments disabled
* Added the ability to block referrer spam (using Piwik Database)
* Updated Import/Export settings to now include settings for Image Compression and Cache plugins (if active)
* General code improvements

== Upgrade Notice ==
= 1.5.19 =
Please disable plugin, and then update, or update from the "WP Updates" area

= 1.5.17 =
Minor Update, Removed Freemius tracking
