# BYOT Auto Notifications

WooCommerce plugin for automated WhatsApp and SMS notifications, sent to customers as their order status changes.

## What it does

- Hooks into every WooCommerce order status change (placed, processing, completed, cancelled, etc).
- Sends a customized message via WhatsApp (Meta Cloud API) or SMS (Twilio), depending on the configured gateway.
- Automatically normalizes phone numbers to the international E.164 format, using the order's billing country.
- Gives you full control: choose which statuses trigger a notification and write the message for each one.
- Logs errors (missing phone, unconfigured gateway, etc) directly to WooCommerce -> Status -> Logs.

## Available placeholders

`{customer_name}`, `{order_id}`, `{order_total}`, `{status}`, `{site_name}`

## Installation

1. Download or clone this repo into `wp-content/plugins/byot-auto-notifications`.
2. Activate the plugin from WordPress -> Plugins.
3. Go to **WooCommerce -> BYOT Notifications** and configure your gateway (Twilio or WhatsApp Cloud API).
4. Check the order statuses you want notifications for and customize the messages.

## Requirements

- WordPress 5.8+
- WooCommerce 5.0+
- PHP 7.4+
- A Twilio account and/or a Meta Business account with WhatsApp Cloud API enabled

## Project structure

```
byot-auto-notifications/
├── byot-auto-notifications.php   # plugin bootstrap
├── includes/
│   ├── class-byot-validator.php       # phone number normalization (E.164)
│   ├── class-byot-gateway.php         # base gateway class
│   ├── class-byot-twilio-gateway.php  # Twilio SMS integration
│   ├── class-byot-whatsapp-gateway.php# WhatsApp Cloud API integration
│   ├── class-byot-order-handler.php   # listens for status changes
│   └── class-byot-admin.php           # admin settings page
└── assets/
    ├── css/admin.css
    └── js/admin.js
```

## License

GPL-2.0+
