=== Delivery Area with Google Maps ===
Contributors: gonzalesc
Tags: delivery area, google maps, delivery, area, polygon, apikey, draw, shortcode, campaign, wordpress
Donate link: https://www.paypal.me/letsgodev
Requires at least: 4.2
Tested up to: 5.0.2
Requires PHP: 5.6
Stable tag: 4.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allows you create delivery areas in Google Maps and by a shortcode put it in everywhere.

== Description ==

Delivery Area With Google Maps plugin allows you create delivery areas using Google Maps. You will draw one or several polygons in differents places of map and you can put them in your website by shortcode.

There are multiple display filters that can be combined:

* Put or avoid the Google Maps Library in the front or in the admin
* Put a location by default in Google Maps when it begin to draw of delivery area
* Put a custom handle name to the wp_enqueue_script() function when it calls to the Google Maps Library
* Put a color to each area delivery

> <strong>Woocommerce Shipping with Delivery Area</strong><br>
>
> Check the **new premium version** available in ([https://www.letsgodev.com/product/woocommerce-shipping-with-delivery-area/](https://www.letsgodev.com/product/woocommerce-shipping-with-delivery-area/?utm_source=readme%20wp&utm_medium=readme%20links&utm_campaign=Woocommerce%20Shipping%20Delivery%20Area))
>
> * It allows put a shipping price by delivery area
> * It allows put a minimum purchase price by delivery area
> * Convert the address to coordinates in Google Maps, and it verifies if is it inside of some delivery area
> * It allows put the Google Maps autocomplete to address field
> * It allows a default shipping price if the customer position is not inside of any delivery area
> * It allows avoid the purchase if the customer position is not inside of any delivery area
> * It 
> * Premium support
>

= Github =

Fork me in [https://github.com/gonzalesc/Wordpress-Delivery-Area-with-Google-Maps](https://github.com/gonzalesc/Wordpress-Delivery-Area-with-Google-Maps/?utm_source=readme%20wp&utm_medium=readme%20links&utm_campaign=Github%20Delivery%20Area)

= Available Languages =

* English
* Spanish

= Woocommerce Shipping Price by Place =
Now you can add 2 zones more additional to Country and States: provinces and districts, or Provinces and neighborhoods, or counties and townships, or more  [https://www.letsgodev.com/product/woocommerce-shipping-price-by-place/](https://www.letsgodev.com/product/woocommerce-shipping-price-by-place/?utm_source=readme%20wp&utm_medium=readme%20links&utm_campaign=Woocommerce%20Shipping%20Place%20By%20Price)

= Woocommerce Volume Offers =
Now you can build offers in your store as 2×1, 3×1, 3×2 or more. To these offers you can apply filters according to business rules.  [https://www.letsgodev.com/product/woocommerce-volume-offers/](https://www.letsgodev.com/product/woocommerce-volume-offers/?utm_source=readme%20wp&utm_medium=readme%20links&utm_campaign=Woocommerce%20Volume%20Offers)


== Installation ==
1. Unzip and Upload the directory 'wp-delivery-area-with-google-maps' to the '/wp-content/plugins/' directory

2. Activate the plugin through the 'Plugins' screen in WordPress

3. Use the Delivery Area -> Settings screen to configure the plugin

4. It is important generate a ApiKey in Google Maps to the plugin works correctly


== Shortcode ==
[areamaps id=10712 w=100% h=400px]


- d : is the id of post (required).
- w is the widht ( you must specify the units: px, %, etc )
- h is the height ( you must specify the units: px, %, etc )
- lib : yes/no , if it is "yes" then the Google Maps library is embed. Default "yes"
- handle : is the name to be embed as JS library


== Frequently Asked Questions ==

= Do I need a Google Maps ApiKey ? =

Yes. It is very important this ApipKey

= Do I need to put my credit card to activate my Google Maps ApiKey ?

Yes. Google gives you $ USD 300 per month and if you exceed the fee, they will charge you. But the amount they give you is quite high so you can pass the limit.

= What services must my ApiKey have? =

For this plugin only the Maps Javascript API service.
But, if you have Woocommerce Shipping with Delivery Maps plugin, you need besides, Geocoding API service and Places API service.

= I have a error "RefererNotAllowedMapError" in my console log =

This error appears when your Google Maps ApiKey has domain restrictions. Please check the referrer settings of your API key on the Google Cloud Platform Console.

= I have a error "You have included the Google Maps API multiple times on this page" =

Maybe your theme or other plugin is embedding the Google Maps library so we should find out the handle name how this library is loaded.

After, you need add some parameters in your shortcode so the Google Maps library doesnt be embed.

[areamaps id=10712 w=100% h=400px lib="no" handle="name_library"]


== Screenshots ==

1. Settings Delivery Area plugin. 
2. Drawing a delivery area usign Google Maps
3. Custom Post Type of Delivery Area with their shortcodes
4. Delivery Area in the front by shortcode


== Changelog ==

= 1.2.0 =
* Tested in WP 5.0.2
* Accept handle js input
* New Structure


=  1.1.1 =
* tested to Wordpress 4.8.x
* features : allow put the Google Api library automatic or manual, in the front or in admin panel


= 1.1.0 =
* tested to WordPress 4.7
* notice when there is a new version
* was added links of information in details plugin


= 1.0.1 =
* remove	: remove transient


= 1.0.0 =
* features	: Create polygons in Google Maps v3
* features	: Use a shortcode for put the delivery areas