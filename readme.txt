=== th0th's Quotes ===
Contributors: th0th
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7D75SD2JLPCMQ
Tags: widget, sidebar
Requires at least: 3.1.2
Tested up to: 3.1.3
Stable tag: 0.6

A plugin that enables you to display a random quote from your collection on sidebar, posts and pages.

== Description ==

**th0th's Quotes** is a plugin that allows you to show quotes from your collection in custom places of your Wordpress (like sidebar -with widget-, pages and posts).

The plugin comes with a widget working out-of-the-box. Widget has no settings, it is pretty simple. After activating plugin you can add widget and it will show a random quote each time page is reloaded.

You can display your quotes also in pages or posts using shortcode of the plugin. Plugin has shortcode `[th0ths_quotes]`. You can insert this to page or post that you want to show your quote on. By default, shortcode will work just like the widget, showing a random quote for your collection. However, you can specify the quote using parameters. 

= Specify by owner =

You can narrow down your collection using `owner` parameter. Thus, a random quote will be chosen from quotes only with the specified owner. For example, `[th0ths_quotes owner=Buddha]` will show a random quote of quotes whose owner is 'Buddha'.

= Specify by id =

You also can show one specific quote on a page or post. For this you can use `id` parameter. `[th0ths_quotes id=3]` will show only one quote id of which is 3.

**NOTE:** id parameter supersedes owner parameter, that is, if an id is specified owner parameter will be ignored.

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
2. Import/Export quotes page
3. Management page of plugin (trash)
4. Screen shot of the widget itself
5. Quote on a page with shortcode.

== Changelog ==

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
