=== Analytify - Dashboard Widget for Google Analytics ===
Contributors: hiddenpearls, desideveloper
Donate link: https://analytify.io/details
Tags: google analytics, google analytics dashboard, google analytics 4, WordPress Analytics, analytics
Requires at least: 4.0
Tested up to: 6.9
Stable tag: 7.1.2
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Google Analytics Dashboard widget is a Free Add-on for Google Analytics by Analytify plugin to show Google Analytics widget at WordPress dashboard.

== Description ==

[Google Analytics Dashboard](http://analytify.io/details) widget by Analytify is a dashboard widget which provides Stats from Country, Referrers, Social media, General stats, Top pages and Top keywords in a widget at WordPress dashboard along with other widgets. This is a handy widget and is requested by users who wants to see their stats in dashboard as a widget. Though Analytify has its own [dashboard](http://analytify.io/details) for Google Analytics.

[Google Analytics Dashboard](http://analytify.io/details) widget by Analytify is a **FREE**  plug and play add-on. No settings needed as all, Add-on settings should have already setup from Analytify settings page.


>**Google Analytics Premium Partner** - [Read](https://www.google.com/analytics/partners/company/5179388639313920/gadp/5629499534213120/app/5707702298738688/listing/5639274879778816) what Google is saying about us.
>

###### Following are the important add-ons which empower you to setup Google Analytics like a Boss. It really helps to your clients, online stores etc

* [Google Analytics eCommerce Tracking for WooCommerce](https://analytify.io/add-ons/woocommerce/)
* [Google Analytics eCommerce Tracking for Easy Digital Downloads](https://analytify.io/add-ons/easy-digital-downloads/)
* [Automated Email Notifications](https://analytify.io/add-ons/email-notifications/)
* [Google Analytics Campaigns Tracking](https://analytify.io/add-ons/campaigns/)
* [Google Analytics Goals Dashboard](https://analytify.io/add-ons/google-analytics-goals-wordpress/)

###### Following is a Complete Hierarchy Structure of Analytify Products Suite.

* Analytify Core (`Free` and Required for all add-ons)
    * [Dashboard widget](https://analytify.io/add-ons/google-analytics-dashboard-widget-wordpress/) (`Free`)
    * Analytify Pro (*Paid*)
        * Campaigns Tracking (*Paid*)
        * Goals Tracking (*Paid*)
        * Email Notifications (*Paid*)
        * ECommerce Tracking for WooCommerce (*Paid*)
        * ECommerce Tracking for Easy Digital Downloads (*Paid*)

The plugin is fully available in French, Turkish and Hungarian languages. However Dutch, German and Russian are just about to be fully translated. We are working hard to make sure the plugin is fully available in all the mentioned languages. 

* French 100%
* Turkish 100%
* Hungarian 100%
* Dutch 96%
* German 96%
* Russian 82%
* Norwegian 79%
* Spanish 50%

**GDPR Compatible Plugins**

* CookieYes
* Cookie Notice & Compliance.

== Installation ==

This section describes how to install the plugin and get it working.

1. You should have Analytify installed before you install this plugin. If you don’t do that you will get a popup message which you can click on link and it will be installed automatically.

2. You need to configure Analytify properly to use this widget. It is plug and play add-on. No settings needed.

== Frequently Asked Questions ==

= Is this different than Google Analytics by Analytify Plugin? =
It is a separate addon that adds a Google Analytics widget at WordPress dashboard. Google Analytics by Analytify is required to use this Dashboard widget.

= Should I install a Google Analytics plugin or I can simply add Google analytics javascript code in my WordPress theme ? =
You can add Google Analytics JS code in your theme but We recommend for Analytify that you connect your Google Analytics account and Analytify together So I could pull your Analytics from Google Analytics and show it in WordPress Dashboard.

= Do I need to have an account on Google Analytics to use this plugin ? =
Yes, a Google Analytics account is necessary to use this plugin. You can create a free account on <a href="https://analytics.google.com">Google Analytics</a> site. After creating a Google analytics account. You need to setup a website profile at Google Analytics. After that you can connect Analytify plugin and Google Analytics together.

= How to Authenticate and Connect my Google Analytics Account in this plugin ? =
Go to Analytify->settings and see the Authentication tab.

= How to select Google Analytics profiles ? =
First, you need to Authenticate your Google analytics account then you can select your web profiles from Profiles tab in Analytify->settings page. click the "Log in with Google Analytics Account" button. Connect and give Analytify App access to your Google Analytics account.

= Does Google Analytics dashboard by Analytify Affects My Site's Loading Speed? =
Not at all. Analytify uses Google Analytics Universal tracking JS code to track every event. Nothing is saved to your Database. Google Analytics are shown on the fly by default. To cache them on the specific Analytify dashboard, you can enable this option. Other than that, no page will save anything to the database.

== Screenshots ==

1. [Google Analytics Dashboard Widget for WordPress](https://analytify.io/features/)

== Changelog ==

= 7.1.2 - 2026-02-27 =
* Enhancement: Improved Dashboard widget icons and updated “Add-ons” labeling for better WordPress UI consistency.

= 7.1.1 - 2026-01-06 =
* Enhancement: Improved translation function usage by ensuring all gettext functions use literal strings instead of variables.
* Compatibility: Compatible to WordPress 6.9

= 7.1.0 - 2026-01-02 =
* Security: Fixed direct file access vulnerability by adding ABSPATH checks to all PHP files.
* Security: Fixed unsafe output by properly escaping all variables and options when echoed using esc_url(), esc_attr(), esc_html(), esc_html_e(), and wp_kses_post().
* Enhancement: Fixed license declaration mismatch - standardized to GPLv3 or later in both readme.txt and plugin header.
* Enhancement: Fixed internationalization issues - corrected text domain from 'wp-analytify' to 'analytify-analytics-dashboard-widget' throughout the plugin.
* Enhancement: Replaced _e() function calls with esc_html_e() or analytify_e() wrapper for proper escaping.
* Enhancement: Removed deprecated load_plugin_textdomain() function call (WordPress 4.6+ handles translations automatically).
* Enhancement: Improved translation function usage by ensuring all gettext functions use literal strings instead of variables.
* Compatibility: Compatible to WordPress 6.9

= 7.0.0 - 2025-07-22 =
* Enhancement: Improved sorting functionality with enhanced visual indicators and more intuitive controls.
* Enhancement: Complete migration from Google Analytics 3 (UA) to Google Analytics 4 (GA4) - removed legacy GA3 and GA4 SDK libraries and implemented direct GA4 API integration for improved performance and reliability and help in resolving conflicts with other plugins that were using the same library.
* Enhancement: Enhanced pagination system with improved navigation and user experience.
* Enhancement: Added "Top Posts by Views" metric with paginated table display to the Analytics Dashboard Widget.

= 6.0.0 - 2025-04-24 =
* New Feature: Added a new `view dashboard` icon in Analytify widget header.
* New Feature: Introduces the Pie chart over doughnut chart in Analytify widget.
* New Feature: Introducing a new filter to customize the device Pie chart colors in Analytify widget: [analytify_visitor_devices_chart_colors](https://analytify.io/doc/analytify-filters/#changing-colors-of-visitors-devices-pie-chart)
* New Feature: Introducing a new filter to customize the visitor Pie chart colors in Analytify widget: [analytify_new_vs_returning_visitors_chart_colors](https://analytify.io/doc/analytify-filters/#changing-colors-of-new-vs-returning-vistors-pie-chart)
* Compatibility: Compatible to WordPress 6.8

= 5.5.1 - 2025-02-04 =
* Enhancement: Updated ECharts library to the latest version 5.5.1
* Enhancement: Filter added to show Visitors/Views Graph in General Statistics section: [analytify_widget_graph_display_filter](https://analytify.io/doc/analytify-filters/#show-visitors-views-graph-in-the-general-statistics-section-of-analytify-dashboard-widget)
* Bug Fix: Conflict with WP Mail SMTP Widget. 
* Bug Fix: Years section in the Visitors/Views Graph showing no data. 
* Compatibility: Compatible to WordPress 6.7

= 5.5.0 - 2024-11-06 =
* New Feature: Added Graph for Visitors and Views in the General Statistics section (visible when Pro is activated).
* New Feature: Introduced a section with a pie chart for Visitor Devices (visible when Pro is activated).
* New Feature: Filter Provided for Top Countries, Top Pages, Top Keywords and Top Referrals Section rows per page.
* Enhancement: Removing Google Analytics V3 functions from the widget completely.
* Compatibility: Compatible to WordPress 6.7

= 5.2.0 - 2024-01-16 =
* New Feature:: Filter Provided for Top Cities Section rows per page.
* Compatibility: Compatible with WordPress 6.4

= 5.1.0 - 2023-08-10 =
* Enhancement: Top Referrers section typo fixed.
* Compatibility: Compatible with WordPress 6.3

= 5.0.3 - 2023-06-20 =
* Enhancement: Typos fixed.
* Enhancement: Real time analytics section styling fixed.
* Enhancement: Translation files updated.
* Compatibility: Compatible with WordPress 6.2

= 5.0.2 - 2023-05-30 =
* Bug Fix: New vs returning section stats for new visitors fixed.
* Enhancement: No stats message modified.

= 5.0.1 - 2023-05-10 =
* New Feature: Yesterday option added in date picker.
* Enhancement: No stats message added for keywords.
* Compatibility: Compatible with WordPress 6.2

= 5.0.0 - 2023-04-18 =
* New Feature: Google Analytics GA4 integration is added.
* New Feature: Google Search Console (webmasters) Integrated.
* Enhancement: Pageload speed improvement.
* Enhancement: UI Improvements.

= 2.0.6 - 2022-07-06 =
* Bug Fix: use the wp_date() function to fetch the website date instead of date() in php. Props to [@generosus](https://profiles.wordpress.org/generosus/) for reporting this bug.
* Compatibility: Compatible with WordPress 6.0

= 2.0.5 - 2022-05-18 =
* Enhancement: Tooltips added for analytics dashboard widget.
* Compatibility: Compatible with WordPress 6.0

= 2.0.4 - 2022-04-14 =
* New Feature: Add custom CSS option for dashboard widget styling.
* Bug Fix: Fix stats figures.
* Bug Fix: Fix stats labels as on main dashboard.
* Compatibility: Compatible with WordPress 5.9

= 2.0.3 - 2021-12-27 =
* Enhancement: Allow Google Analytics Widget to be closed.

= 2.0.2 - 2021-11-01 =
* Bug Fix: Date range selection was not persistent.

= 2.0.1 - 2021-07-30 =
* Enhancement: Update Analytify logo image in SVG.
* Compatibility: Compatible with WordPress 5.8

= 2.0.0 - 2021-03-12 =
* Enhancement: Design Compatibility with Analytify 4.0 release.
* Compatibility: Compatible with WordPress 5.7

= 1.1.5 - 2021-01-26 =
* New Feature: Turkish translation is added.
* Bug Fix: Invalid date format issue while fetching stats from Google Analytics.
* Bug Fix: Doubling of error notices to install free version.
* Compatibility: Compatible with WordPress 5.6

= 1.1.4 - 2020-07-26 =
* New Feature: Introducing real-time stats in dashboard widget.
* Enhancement: Update widget layout.

= 1.1.3 - 2020-03-25 =
* Bug Fix: Fix fatal error in social media stats.
* Bug Fix: Fix undefined offset in general stats.
* Compatibility: Compatible with WordPress 5.4

= 1.1.2 - 2019-05-22 =
* Enhancement: Pretty NEW VS RETURNING VISITORS number.
* Compatibility: Compatible with WordPress 5.2

= 1.1.1 - 2019-01-01 =
* Enhancement: Date selection persist after page load.
* Compatibility: Compatible with WordPress 5.0

= 1.1.0 - 2018-10-26 =
* Enhancement: Optimized with Cache

= 1.0.8 - 2018-10-12 =
* Enhancement: Added French language translation.

= 1.0.7 - 2018-01-18 =
* Bug Fix: moment-js deprecation warning
* Bug Fix: undefine notice issue
* Compatibility: Compatible with WordPress 4.9.

= 1.0.6 =
* Bug Fix: text-domain update for multilingual translations at wordpress.org
* Enhancement: Compatibility with 4.8

= 1.0.5 =
* Enhancement: Dutch language added.
* Enhancement: Norwegian language updated.
* Bug Fix: Privileges bug fixed.
* Bug Fix: New vs Returning stats fixed.

= 1.0.4 =
* Enhancement: Norwegian language added.
* Enhancement: German language completed.

= 1.0.3 =
* Bug Fix: deploy script assets issue fixed.

= 1.0.2 =
* Bug Fix: Changed text-domain for Translation management.
* Bug Fix: fixed issues with Translation strings.
* Enhancement: German language.

= 1.0.1 =
* Multilingual and WPML compatible.
* Language .pot file generated.
* Changed admin notices to help users in installing core version of Analytify.

= 1.0.0 =
Initial Release.

== Upgrade Notice ==

= 7.1.2 =
* Important Release, Update carefully. Report us back if you face any issues. Thanks for using Analytify.