=== BYOT Auto Notifications ===
Contributors: Byot
Tags: woocommerce, sms, whatsapp, notifications, twilio
Requires at least: 5.8
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Notificari automate WhatsApp si SMS pentru WooCommerce, in functie de statusul comenzii.

== Description ==
Trimite notificari automate prin WhatsApp sau SMS clientilor tai atunci cand statusul unei comenzi WooCommerce se schimba.
Suporta Twilio pentru SMS si WhatsApp Cloud API (Meta) pentru WhatsApp.
Numerele de telefon sunt normalizate automat la formatul international E.164, in functie de tara de facturare a comenzii.

== Installation ==
1. Incarca folderul pluginului in /wp-content/plugins/byot-auto-notifications/
2. Activeaza pluginul din WordPress -> Plugins.
3. Mergi la WooCommerce -> BYOT Notifications si configureaza gateway-ul dorit (Twilio sau WhatsApp Cloud API).
4. Bifeaza statusurile de comanda pentru care vrei sa trimiti notificari si personalizeaza mesajele.

== Frequently Asked Questions ==

= De ce nu se trimite notificarea? =
Verifica in WooCommerce -> Status -> Logs (sursa "byot-auto-notifications") pentru detalii: gateway neconfigurat, numar de telefon lipsa/invalid sau template de mesaj gol.

= Ce placeholder-uri pot folosi in mesaje? =
{customer_name}, {order_id}, {order_total}, {status}, {site_name}

== Changelog ==
= 1.0.0 =
* Lansare initiala.
