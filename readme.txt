=== th0th's Quotes ===
Contributors: th0th
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7D75SD2JLPCMQ
Tags: widget, sidebar, shortcode, quote
Requires at least: 3.1.2
Tested up to: 3.4.1
Stable tag: 2.02

A plugin that enables you to display a random quote from your collection on sidebar, posts and pages.

== Description ==

**th0th's Quotes** is a plugin that allows you to show quotes from your collection in custom places of your Wordpress (like sidebar -with widget-, pages and posts).

The plugin comes with a widget working out-of-the-box. Widget has no settings, it is pretty simple. After activating plugin you can add widget and it will show a random quote each time page is reloaded.

You can display your quotes also in pages or posts using shortcode of the plugin. Plugin has shortcode `[th0ths_quotes]`. You can insert this to page or post that you want to show your quote on. By default, shortcode will work just like the widget, showing a random quote for your collection. However, you can specify the quote using parameters. 

= Specify by owner =

You can narrow down your collection using `owner` parameter. Thus, a random quote will be chosen from quotes only with the specified owner. For example, `[th0ths_quotes owner=Buddha]` will show a random quote of quotes whose owner is 'Buddha'.

= Specify by id =

You also can show one specific quote on a page or post. For this you can use `id` parameter. `[th0ths_quotes id=3]` will show only one quote id of which is 3.

= Specify by tag (Implemented in v0.93) =

Now you can narrow down quotes that are used in shortcode by using tags. After adding a specific tag to quote in management menu, you can use `[th0ths_quotes tag=<tag here>]` to show quotes with the tag you specified.

**NOTE:** id parameter supersedes owner parameter, and owner parameter supersedes tag.

= Styling quote displayed by shortcode =

You can define custom style for the division on which quote displayed using shortcode to match your theme or your needs. Shortcode has `class` parameter which will set a css class for the division that quote will be displayed in. Using this class you can set a custom displaying style. For example, `[th0ths_quotes class=quote_div]` will cause the output `<div class="quote_div">...</div>` so that you can define custom width, height, etc. for your quote displaying division.

== Installation ==

1. Upload th0ths-quotes folder to your `wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Add widget to your prefered location through the 'Appearance' menu in Wordpress.
1. Manage quotes using "th0th's quotes" menu in administration panel.

== Frequently Asked Questions ==

= Although I reinstall this plugin my quotes are still there, how can completely reinstall it? =

The plugin uses its own database table to store quotes. And Wordpress doesn't have an option do take some action upon deletion of the plugin. So, plugin is not able to delete its database table itself, you should manually do it. To completely remove plugin (also for a fresh install this step is required) after removing plugin from administration section of Wordpress, you should remove table named `wp_th0ths_quotes` from your database (`wp_` part can be change depending on the database prefix you are using).

== Screenshots ==

1. Management page of plugin
2. Add new quote page
3. Edit quote page
4. Import/Export quotes page
5. Screen shot of the widget itself
6. Quote on a page with shortcode.

== Changelog ==

= 2.02 =
* Update tested-up-to WordPress version.
* Update plugin URL.

= 2.01 =
* Add donation page.

= 2.0 =
* Enable sharing quotes on twitter.

= 1.25 =
* Some styling.

= 1.2 =
* Update plugin homepage so that users can reach the blog post.

= 1.1 =
* Add Croatian language files.

= 1.0 =
* Fix displaying quotes from trash bug.

= 0.98 =
* Fix shortcode's output position.

= 0.97 =
* Implement showing latest quote feature.

= 0.96 =
* Changle maximum length of quote
* Enable usage of (") and (') characters in quote.

= 0.95 =
* Owner and tag filters now usable also in widget.

= 0.94 =
* Typofix on tags shortcode.

= 0.93 =
* Tags feature is implemented.

= 0.92 =
* Fixing quotes' source link bug.
* Update XML structure for exported quotes to support opening links in new page.

= 0.91 =
* Fix bug on source URL validation.

= 0.9 =
* Make opening source link in a new page (or tab) optional.

= 0.8 =
* Sources for quotes feature is implemented.
* Some bugfixing.
* Update Paypal donation form to be international.

= 0.7 =
* Editing quotes feature is implemented.
* 'Add new quote' link is added to page displayed after adding a quote.

= 0.6 =
* Language support is added.

= 0.5 =
* Shortcode support is added.
* Some general styling has been done.

= 0.4 =
* Empty form sends in management pages are now handled.
* Import/Export feature is added.

= 0.3 =
* 'Trash' feature is added.

= 0.2 =
* Enabling delete of multiple quotes with checkboxes.
* Check all checkbox is added.

= 0.1 =
* First release.

== More ==

* You can support development of this plugin by donations. ([Donate via Paypal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7D75SD2JLPCMQ "Click to donate using Paypal"))
* This plugin is originally hosted on github. So you can check [there](https://github.com/th0th/th0ths-quotes "th0th's Quotes on Github") as well if you want.
* You can contact me via e-mail or jabber (my address for both is th0th -at- returnfalse.net).
