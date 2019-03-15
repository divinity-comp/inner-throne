=== bbPress Toolkit ===
Contributors: casiepa
Donate link: http://casier.eu/wp-dev/
Tags: bbpress,mentions,subscriptions,tweak,protect forum,global options,options,settings,toolkit,manage options,close forum,hack,mention,forum subscription
Requires at least: 4.0
Tested up to: 4.9.4
Stable tag: 1.0.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Swiss knife tweaking and hacking Toolkit for bbPress. Set global options and style your forums. 

== Description ==
Swiss knife Toolkit for bbPress. Set global options and style your forums.

Swiss knife tweaking and hacking Toolkit for bbPress. Set global options and style your forums. 

Current features:

* Mentions (email someone by using the @username)
* Forum and Topic subscription management for keymasters and moderators

And also:

* Close/Protect forums to new topics (only replies on current topics allowed)
* Close/Protect profile information so non-logged-in users cannot see them
* Use TinyMCE editor for topics and replies to add basic HTML
* Remove or change informational messages, descriptions and breadcrumbs
* Auto tick the " Notify me of follow-up replies via email " when giving a reply
* Move the " Subscribe " option of a forum to the right, not directly after the breadcrums
* Do not grey out closed topics
* Inverse the replies order (most recent on top)
* Do not show the table with the list of subforums when in a forum
* Remove bbPress css stylesheet from all pages except forum pages
* Basic System Info (for support personnel)
* Translation ready ! https://translate.wordpress.org/projects/wp-plugins/bbp-toolkit
* No email sending to blocked users
* Add search on all pages
* And more ... (see changelog)

See the screenshots for all what can be done !

Consider also the following plugins:

* bbP Move Topics
* bbP API

Special thanks to Robkk for his input in the codex!
And thanks to Viktor Szépe for his svn-updater idea.

== Installation ==
Option 1:

1. On your dashboard, go to Plugins > Add new
1. search for *bbP Toolkit*
1. install and activate the plugin
1. Check your Dashboard: Tools > bbP Toolkit

Option 2:

1. Unzip the contents to the "/wp-content/plugins/" directory.
1. Activate the plugin through the "Plugins" menu in WordPress.
1. Check your Dashboard: Tools > bbP Toolkit

== Screenshots ==

1. Global Settings
2. Information Settings
3. Performance Settings
4. Close forums (no new topics, only replies) 
5. Basic system info

== Frequently Asked Questions ==
= Can I make feature requests =
Of course ! Just post something on the support tab

= I want this tool in my own language =
Great! Go to https://translate.wordpress.org/projects/wp-plugins/bbp-toolkit and start translating. When it is at 100% and accepted, the language should come automatically to your installation.

= I love your tool =
Thanks. Please leave a review or donate 1 or 2 EUR/USD for a coffee.

== Upgrade Notice ==
= 1.0.12 =
New options and fixes. Check out the ChangeLog !

== Changelog ==
= 1.0.12 =
* Added 2.5 fix: Fix page number for topics with over 1000 replies

= 1.0.11 =
* Added option: Manage topic subscriptions (action on topics in the backend)
* Added Support Info: Database charset and Toolkit version
* Fix: spelling mistake on settings page
* Fix: notices/warnings about unexisting variables
* Fix: Upgrade notice HTML characters

= 1.0.10 =
* Added option: Subscribe new users automatically to forum(s)
* Added option: Add search box also on forums and topics

= 1.0.9 =
* Added extra: Manage forum and topic subscriptions (as actions on users and forums)
* Added default: Mentions are activated by default
* Added Support option: Upgrade to the latest trunk (development) version

= 1.0.8 =
* Added default: Blocked users should not receive email, even if they are subscribed
* Added option: Remove 'Private:' in front of private forums
* Fix: TinyMCE issues when pasting HTML code
* Fix: bbP Toolkit menu item not visible in some cases.

= 1.0.7 =
* Added option: Mentions. An email is sent to @username mentionned people
* Added option: A message can be displayed when a forum is closed for topics
* Added option: Change separator between admin links
* Fix: Only administrator of the single site should be able to see the settings

= 1.0.6 =
* Ready for translation: https://translate.wordpress.org/projects/wp-plugins/bbp-toolkit
* Added option: add (featured) icon in front of forum name
* Added option: Closed forums no longer show in the dropdown of [bbp-topic-form]
* Fix: Make sure error and warning are still visible if the "remove Oh Bother! message" option is selected
* Fix: [bbp-topic-form] gives "You cannot create new topics" message after search

= 1.0.5 =
* Added option: Basic TinyMCE editor to new topics and replies to add basic HTML to your input
* Added option: Change separator between subforums on the forum index page
* Added option: Remove the topic and reply counters of the subforums on the forum index page
* Added option: Only show the last revision of a topic or reply
* Upgrade: Auto regenerate CSS upon plugin upgrade or activation
* Settings: Tab approach for the different sections in the settings
* Performance: Split the main php file into different includes
* Updated donation info

= 1.0.4 =
* Restrict non-logged-in users from seeing profile
* Change the maximum topic title length
* Shorten Freshness wording for topics and replies

= 1.0.3 =
* Close forums for new topics (only replies to current topics allowed)
* Another fix compatibility for older PHP in the support info
* CSS generation changed. All code in one file.
* Remove pagination info

= 1.0.2 =
* Fix compatibility for older PHP in the support info
* Modify breadcrumbs layout
* Indication in what version an option was added in the settings

= 1.0.1 =
* Added the long-awaited donate option
* Inverse the replies order but show lead topic still on top
* Remove the forum description box or put your own
* Remove the topic description box or put your own
* Remove breadcrumb (e.g. Home > Forums > myForum)
* Basic System Info (for support personnel)

= 1.0.0 =
* Initial release to replace the Global Settings part of bbP Manage Subscriptions.
* Remove or change the " Oh bother! No topics were found here! " message for empty forums
* Remove or change the " Your account has the ability to post unrestricted HTML content. " message.
* Auto tick the " Notify me of follow-up replies via email " when giving a reply
* Move the " Subscribe " option of a forum to the right, not directly after the breadcrums
* Do not grey out closed topics
* Inverse the replies order (most recent on top)
* Do not show the table with the list of subforums when in a forum
* Remove bbPress css stylesheet from all pages except forum pages