=== NoorTemplates ===
Contributors: noorealam
Tags: woocommerce, product page, page builder, blocks, gutenberg
Requires at least: 6.6
Tested up to: 7.0
Requires PHP: 7.4
Requires Plugins: woocommerce
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Build WooCommerce single product pages from premade templates, using blocks that wrap WooCommerce's own rendering.

== Description ==

NoorTemplates lets you redesign the WooCommerce single product page without touching a theme file:

* Product Title, Rating, Price, Short Description, Add to Cart, Meta, Gallery, Tabs and Breadcrumbs blocks — each a thin wrapper around WooCommerce's own `woocommerce_template_single_*()` functions, so variations, stock handling and reviews keep working exactly as WooCommerce implements them
* Product Grid and Related Products blocks for latest/related/upsell product listings
* Container, Heading, Button, Accordion and Tabs layout blocks for arranging everything
* Product Layouts — full page arrangements built in the standard block editor, so it works on any theme, not just block themes
* Assign a Layout per product, per product category, or as the site-wide default; unconfigured products keep rendering exactly as vanilla WooCommerce always has
* An editor Template Library with premade full layouts and drop-in sections (FAQ accordion, trust badges, feature table)

== Installation ==

1. Make sure WooCommerce is installed and active.
2. Upload the plugin to `/wp-content/plugins/noortemplates`.
3. Run `npm install && npm run build` inside the plugin directory (development installs only).
4. Activate the plugin through the Plugins screen.
5. Go to Products → Add New Product Layout, build a layout, then assign it to a product from the "Product Page Template" box on the product edit screen.

== Changelog ==

= 1.0.0 =
* Initial release: WooCommerce product blocks, Product Layouts, per-product/category/site-wide template assignment, starter layouts and sections.
