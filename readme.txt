=== BYOT Auto Notifications ===
Contributors: Byot
Tags: woocommerce, sms, whatsapp, notifications, twilio
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automated WhatsApp and SMS notifications for WooCommerce, based on order status.

== Description ==
Sends automated WhatsApp or SMS notifications to your customers whenever a WooCommerce order status changes.
Supports Twilio for SMS and WhatsApp Cloud API (Meta) for WhatsApp.
Phone numbers are automatically normalized to the international E.164 format, based on the order's billing country.

== Installation ==
1. Upload the plugin folder to /wp-content/plugins/byot-auto-notifications/
2. Activate the plugin from WordPress -> Plugins.
3. Go to WooCommerce -> BYOT Notifications and configure the gateway you want (Twilio or WhatsApp Cloud API).
4. Check the order statuses you want to send notifications for and customize the messages.

== Frequently Asked Questions ==

= Why isn't the notification being sent? =
Check WooCommerce -> Status -> Logs (source "byot-auto-notifications") for details: unconfigured gateway, missing/invalid phone number, or empty message template.

= What placeholders can I use in messages? =
{customer_name}, {order_id}, {order_total}, {status}, {site_name}

== Changelog ==
= 1.0.1 =
* Confirmed compatibility with WordPress 7.1 and WooCommerce 11.0.
* Brought the codebase into full WordPress Coding Standards (PHPCS/WPCS) compliance.
* Added a Plugin Check-aligned PHPCS ruleset and reviewed the code for security issues; no vulnerabilities found.
* No functional changes to notification behavior.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 1.0.1 =
Coding-standards, compliance, and WordPress 7.1 compatibility update. No settings or behavior changes — safe to update.
