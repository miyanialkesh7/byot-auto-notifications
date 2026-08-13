# BYOT Auto Notifications

Plugin WooCommerce pentru notificari automate prin WhatsApp si SMS, trimise clientilor pe masura ce statusul comenzii se schimba.

## Ce face

- Se leaga de fiecare schimbare de status a unei comenzi WooCommerce (plasata, in procesare, finalizata, anulata etc).
- Trimite un mesaj personalizat pe WhatsApp (Meta Cloud API) sau SMS (Twilio), in functie de gateway-ul configurat.
- Normalizeaza automat numerele de telefon la formatul international E.164, folosind tara de facturare a comenzii.
- Iti lasa control complet: alegi pentru ce statusuri se trimit notificari si scrii mesajul pentru fiecare.
- Loghează erorile (telefon lipsa, gateway neconfigurat etc) direct in WooCommerce -> Status -> Logs.

## Placeholder-uri disponibile in mesaje

`{customer_name}`, `{order_id}`, `{order_total}`, `{status}`, `{site_name}`

## Instalare

1. Descarca sau cloneaza acest repo in `wp-content/plugins/byot-auto-notifications`.
2. Activeaza pluginul din WordPress -> Plugins.
3. Mergi la **WooCommerce -> BYOT Notifications** si configureaza gateway-ul (Twilio sau WhatsApp Cloud API).
4. Bifeaza statusurile de comanda pentru care vrei notificari si personalizeaza mesajele.

## Cerinte

- WordPress 5.8+
- WooCommerce 5.0+
- PHP 7.4+
- Cont Twilio si/sau cont Meta Business cu WhatsApp Cloud API activat

## Structura proiectului

```
byot-auto-notifications/
├── byot-auto-notifications.php   # bootstrap plugin
├── includes/
│   ├── class-byot-validator.php       # normalizare numere de telefon (E.164)
│   ├── class-byot-gateway.php         # clasa de baza pentru gateway-uri
│   ├── class-byot-twilio-gateway.php  # integrare Twilio SMS
│   ├── class-byot-whatsapp-gateway.php# integrare WhatsApp Cloud API
│   ├── class-byot-order-handler.php   # asculta schimbarile de status
│   └── class-byot-admin.php           # pagina de setari din admin
└── assets/
    ├── css/admin.css
    └── js/admin.js
```

## Licenta

GPL-2.0+
