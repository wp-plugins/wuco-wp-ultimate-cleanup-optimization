=== WUCO - WP Ultimate Cleanup & Optimization ===
Contributors: pranacoder
Tags: cleanup, clean up, database, optimize, database size, MySQL, delete revision, performance, cleaner, optimizer, optimization tool, DB, unused, free
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0-standalone.html
Requires at least: 4.0
Tested up to: 4.2.2
Stable tag: 2.0

WUCO aka WP Ultimate Cleanup & Optimization, a free easy to use yet effective plugin designed to help you keep your MySQL database clean.

== Description ==
WordPress stores a lot of excess information in your DB (like post revisions, auto drafts, spam and trashed comments, transient site options, etc.).
Of course there's a reason for that and some of this data is needed once in a while, but most of it is not critical. It just sits there taking up space and never being used.
Over the time as your site grows the volume of this unused data grows with it making your database unreasonably huge thus effecting the site's overall performance.

Using WUCO you can easily and safely clean up your database from those useless entries. With a single click you can remove revisions, drafts, auto drafts, orphaned post and comment meta, transient data and much more. You can also schedule the cleanups for later and they will be run automatically keeping your WordPress site clean and shiny.

And as all the best things in life WUCO is and always will be absolutely free.

== Installation ==
1. Log in to your WordPress Admin Panel.
2. Go to Plugins and activate Wuco - WordPress Ultimate Cleanup & Optimization.
3. Then go to WUCO Settings page.

== Screenshots ==
1. WUCO Admin Panel
2. Either run cleanup or schedule it for later
3. Monitor the plugin activity

== Changelog ==

= 2.0: May 7, 2015 =

* New: Ability to run cleanups automatically at predefined intervals
* New: Plugin activity log
* Tweak: Major code optimization

= 1.2: April 21, 2015 =

* Fixed: Leaving orphaned meta when deleting posts and comments

= 1.1: April 17, 2015 =

* Fixed: Bug with deleting pingbacks and trackbacks
* Tweak: Slightly modified query for displaying and deleting transient data
* Tweak: Minor CSS changes