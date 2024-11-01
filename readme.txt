=== UK Lottery Results ===
Contributors: Lottery Magic.co.uk
Tags: lottery results, UK lottery results, lotto, thunderball, euromillions, health lottery, widget
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 1.1

Easy to install, free and fully customisable widget which displays the latest UK lottery results on your Wordpress site.

== Description ==
Show the latest UK lottery results from Lotto, Thunderball, Euromillions, Lotto Plus 5 and Health Lottery draws on your Wordpress site. As well as just the latest results, further information can be displayed including the ball set and machine used and a full breakdown of the number of winners and prizes won. Easy to install and configure with fully customisable and documented CSS.

== Screenshots ==

1. A front end widget example.
2. Another front end widget example, this time showing integration with an alternative theme.
3. The widget back end with multiple configuration options.
4. Demonstration of the JavaScript rollover (optional) to display prize information and more for a selected lottery draw.

== Changelog ==

= 1.1 =
* Another minor fix due to Wordpress issue

= 1.0 =
* Slight upload fix, sorry

= 0.9 =
* Minor code improvements

= 0.8 =
* New lighter CSS didn't work on some themes; changed now. Hopefully last update for a while

= 0.7 =
* Super narrow version image fix

= 0.6 =
* Added new option (activated by a checkbox in the widget admin) to show a super narrow version of the widget for themes with narrow sidebars
* Improved CSS for full size version, fixing line-height issue

= 0.5 =
* CSS updated to work better with older / less well written themes that don't pass widget CSS classname

= 0.4 =
* Minor code and CSS changes

= 0.3 =
* Minor readme fixes

= 0.2 = 
* Changed some of the default CSS
* Moved some parameters from construct to activation

= 0.1 = 
* Initial version

== Installation ==

Download, Upgrading, Installation:

Upgrade

* First deactivate the Lottery Magic Results plugin
* If you have altered the CSS or JS files, download a copy of these and re-upload after installation.
* Remove the `uk-lottery-results-widget` directory

**Install**

* Unzip the `uk-lottery-results-widget.zip` file. 
* Upload the entire contents of `uk-lottery-results-widget` folder, including all sub-folders to your `wp-content/plugins` folder.
* Chmod the `dat` directory to 777 - this is where the lottery results file is cached. 

**Activate**

* In your WordPress administration, go to the Plugins page
* Activate the Lottery Magic Results Feed plugin
* Go to the Appearance > Widget page, drag the Lottery Results widget to activate it and tick the boxes for the results you would like to display.

== Frequently Asked Questions ==

= I've uploaded the widget, but no results are showing =

You need to go to your widget configuration screen and tick the boxes for the lottery results you would like to show.

= What does chmod the dat directory to 777 mean? =

Once you have uploaded the plugin, navigate to the dat directory in the uk-lottery-results-widget folder. In most FTP clients, you then right click and select something like 'File Permissions' or 'File Attributes'. You then need to tick all the read and write options or manually type 777 if there is an option to enter a numeric value.

= What JavaScript does the additional information rollover use? =

The widget uses wz_tooltip by the late Walter Zorn (file included). The reason for this is that it has no dependency on either Jquery or Prototype and also no image dependencies.

If for any reason you have hardcoded the use of wz_tooltip.js into your Wordpress installation already, there is a define clearly documented at the top of the plugin php file so you can disable the loading of it.

= Can I change the look and feel of the widget via CSS? =

Yes, simply edit style.css in the plugin directory. All CSS elements are documented and have been provided to make it easy to alter the styling of the widget if required. The widget is of course designed to fit in automatically with your chosen theme, but the option is there to tweak if required.

= How can I edit the rollover styling? =

There are several documented options in the js/wz_tooltip.js file which can be changed if you wish.

= Are there any other conditions to use this plugin? =

No, however we would appreciate it if you tick the box in the widget admin to enable a small link back to us.
