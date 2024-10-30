=== Comments Users Mentions ===
Contributors: baxeico
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=8RJ6HS3XQ4EPW&lc=IT&item_name=%22Comments%20Users%20Mentions%22%20wp%20plugin&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: comments, mentions, email
Requires at least: 3.1
Tested up to: 3.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: trunk

Allows to mention Wordpress users in a comment. The mentioned users will receive a notification email.

== Description ==
This plugin allows to mention Wordpress users in a comment by writing usernames preceded by a character (default @), e.g. **@guguweb**.
The mentioned users will receive a notification email with a link to the relevant post.

The mention character (default @) can be customized by a filter hook: `cum-mention-char`. Put the following code in your theme `functions.php` file:

`add_filter( 'cum-mention-char', 'cum_custom_char' );
function cum_custom_char( $content ) {
    return '+';
}`

And the mention character will be +, so you can mention with **+guguweb**.

This plugin was inspired by [Mention comment's Authors by Wabeo](http://wordpress.org/plugins/mention-comments-authors/). Many thanks to the [author](http://wabeo.fr)!

== Installation ==

1. Upload the plugin's folder into `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. All done !

== Changelog ==

= version 0.2 =

* users mentions can be done by using only a preceding character (default @);
* mention char can be customized by means of a filter hook: `cum-mention-char`.
