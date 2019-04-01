=== Mashshare - Floating Sidebar Add-On ===
Author: René Hermenau, Steffen Arnold
Tags: social share sidebar
Requires at least: 3.6
Tested up to: 4.9
Stable tag: 1.3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows a sticky sharing sidebar on left or right edge of your screen

== Description ==

Shows a sticky sharing sidebar on left or right edge of your screen


== Installation ==

1. Upload plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
4. Enjoy!

== Frequently asked questions ==



== Screenshots ==


== Changelog ==

= 1.3.8 =
* Fix: Shares are not requested when MashShare core buttons are disabled

= 1.3.7 =
* Fix: Undefined index

= 1.3.6 =
* Fix: Wrong url shared for WPForo
* Fix: Undefined method mashbar_get_cache_expiration()

= 1.3.5 =
* Fix: When wordpress site_url is containing subdir like hostname.com/subdir a wrong sharing url is used

= 1.3.4 =
* New: E-Mail title and body added. You can use shorcodes in the mail template


= 1.3.3 =
* Fix: Version number not counting up after update. Still says, there is a new update
* Tweak: Add Skype, Telegram, Yummly, Frype, Hackernews and flipboard button

= 1.3.2 =
* Fix: Share count not hidden when "hide share count" value is entered

= 1.3.1 =
* Fix: activation multisite compatible
* New: Compatible WordPress 3.8

= 1.3.0 =
* Fix: Activate sidebar for home page separately from the post or page condition
* Tweak: More simple mobile device check and setting option

= 1.2.9 =
* Fix: Do not activate plugin if MashShare plugin is not activated

= 1.2.8 =
* Fix: Use rel="external" in a href

= 1.2.7 =
* Fix: Urlencode string & remove html elements with html_entity_decode() in sharing titles
* New: Use translated share label. Label can be translated in Settings->Share Count Label

= 1.2.6 =
* Fix: HTML5 issue div as child element of span not allowed

= 1.2.5 =
* Fix: Pinterest button is converted to native pinit button if pinit.js is included in site


= 1.2.4 =
* Fix: Fatal error:  Can't use method return value in write context

= 1.2.3 =
* Fix: Pinterest button is missing specific pinterest image and description

= 1.2.2 =
* Fix: Add new mail icon only when array mashfs_networks is empty
* Fix: Causes duplicate plugin row meta Getting started and Add-On in MashShare plugin row meta
* Fix: Pinterest button not working

= 1.2.1 =
* Fix: New mail button only added on first time installation not when add-on is updated
* Fix: Redirect after update links to wrong settings screen
* Fix: Plugin meta settings link not working
* Fix: Use site title when sidebar is used on frontpage with multiple blog posts
* New: Allow floating sidebar on non singular pages like frontpages with multiple blog posts

= 1.2.0 =
* New: Add mail button

= 1.1.9 =
* New: Get shares from post_meta value instead requesting another time the api

= 1.1.8 =
* Fix: Exclude postID not working on is_page_template()

= 1.1.7 =
* Fix: Hide @ on twitter share when no twitter handle is used
* Fix: Show floating sidebar only on singular posts

= 1.1.6 =
* New: Use new shorturl method of MashShare 3.0

= 1.1.5 =
* New: Use MashShare's core getSharedcount() function for collecting share count
* Tweak: Do not show sidebar on 404 pages

= 1.1.4 =
* Fix: Fix: undefined vars on first time installation
* Fix: short urls for twitter not working when using https instead http

= 1.1.3 =
* New: Disable mashdebug() for compatibility reasons

= 1.1.2 =
* New: Better way of detecting mobile devices. Its even working when site is full cached

= 1.1.1 =
* Fix: CSS fix

= 1.1.0 =
* New: Use more reliable js detection if sidebar is used on mobile device.

= 1.0.9 =
* Fix: Pinterest image not shown when "use featured image" option is enabled

= 1.0.8 =
* Fix: Short urls not shown for twitter
* New: Tested up to wp 4.4.2

= 1.0.7 =
* Fix: Is enabled on all posts. Page conditional is ignored (is_page_template())
* Fix: Undefined var

= 1.0.6 =
* Fix: Twitter handle keeps on 'Mashshare'

= 1.0.5 =
* Fix: fatal error mashsb_is_admin_page() missing when Mashshare core is disabled
* Fix: Settings empty when both Mashshare core plugin and Floating add-on disabled and enabled again

= 1.0.4 =
* Fix: Exclude option not working as expected

= 1.0.3 =
* Fix: Closing aside tag missing

= 1.0.2 =
* Fix: Fatal error when deactivated
* Fix: Fake count ignored

= 1.0.1 =
* Initial version

== Upgrade notice ==

