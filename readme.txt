=== Flexible Contact Information Block ===

Contributors:      WordPress Telex
Tags:              block, contact, information, phone, email, address
Tested up to:      6.8
Stable tag:        0.1.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
A plugin that stores contact information globally and provides a Gutenberg block to display selected fields on any page.

== Description ==

This plugin provides a centralized way to manage your site's contact information. Enter your details once in the settings page, then use the Gutenberg block to display exactly the fields you need on each page.

**How it works:**

1. Go to **Settings > Contact Info** and enter all your contact details once.
2. Drop the "Contact Info" block onto any page, post, or widget area.
3. Toggle which fields should appear — one page might show only the phone number, another might show the full address, email, and phone.

**Available Contact Fields:**
- Phone number (rendered as a clickable tel: link)
- Email address (rendered as a clickable mailto: link)
- Street address
- Town / City
- State / Province
- Zip / Postal code

**Off-Screen / Developer Fields:**
- Google Analytics tracking code (automatically output in page head)
- Custom JavaScript code (automatically output before closing body tag)

**PHP Helper Functions for Theme Developers:**

Access any stored field from PHP templates:

`telex_contact_get( 'phone' )` — returns the stored phone number.
`telex_contact_get( 'email' )` — returns the stored email.
`telex_contact_get( 'street_address' )` — returns the street address.
`telex_contact_get( 'town' )` — returns the town/city.
`telex_contact_get( 'state' )` — returns the state/province.
`telex_contact_get( 'zip_code' )` — returns the zip/postal code.
`telex_contact_get( 'google_analytics' )` — returns the GA code.
`telex_contact_get( 'custom_js' )` — returns the custom JS.
`telex_contact_get_all()` — returns all fields as an array.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ingeni-contact-info-block` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **Settings > Contact Info** and fill in your contact details.
4. Add the "Contact Info" block to any page, post, or widget area.
5. Toggle which fields to display for that particular block instance.

== Frequently Asked Questions ==

= Where is the data stored? =

All contact information is stored as a single WordPress option. This means you enter it once and it is available site-wide.

= Can I use the block in multiple places? =

Yes. Each block instance lets you choose which fields to show. A footer widget might show the address and phone, while a sidebar widget shows only the email.

= How do I access contact data from my theme templates? =

Use `telex_contact_get( 'phone' )` or any other field key. Use `telex_contact_get_all()` for all fields.

== Changelog ==

= 0.1.0 =
* Initial release
