=== Plugin Name ===
Contributors: houkedekwant
Donate link: http://nl.linkedin.com/in/houkedekwant
Tags: color palette, color palette generator, image, images, color, colour, colour palette, palette, attachments
Requires at least: 2.9
Tested up to: 4.6.1
Stable tag: trunk
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: cpg
Domain Path: /languages

This plugin generates color palettes for your WordPress images and appends them to your images inside your content.

== Description ==

This plugin is ideal for blog owners who like to do something more with their images, or for webshop owners who like to filter products by color. When inserting images you have the option to show the palette for that image. You can also use the following shortcode to show a specific image with palette: `[colorpalette attachment="56" dominant="false" colors="10" size="large"]`

= Features =

* Generate color palettes per image
* Bulk-generate color palettes for all images
* Allows configuration of number of unique colors per image
* Allows configuration of auto appending palettes on attachment pages
* See stats of generated palettes

= Developers =
If you don't like the way the plugin outputs the palettes, or you want to create custom color queries yourself, use the wordpress taxonomy functions. There are 2 new taxonomies added when the plugin is activated: <code>cpg_dominant_color</code> and <code>cpg_palette</code>. The first one holds one color per image, the main color. The second one holds the palette, and the number of colors is based on what the site owner has entered (default = 10).

= Special thanks =
* Lokesh Dhakar, for the [color thief library](https://github.com/lokesh/color-thief)
* Projekod, for the [Round Color class](https://github.com/Projekod/RoundColor)

== Installation ==

Automatic Installation:

1. Go to Admin - Plugins - Add New and search for "color palette generator"
2. Click the Install Button
3. Click 'Activate'
4. Go to Media > Color Palette Generator and start generating

Manual Installation:

1. Download color-palette-generator.zip
2. Unzip and upload the 'color-palette-generator' folder to your '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to Media > Color Palette Generator and start generating

== Screenshots ==

1. The Color palette generator settings page
2. A new media library column will be available
3. When inserting images into your content, new options will be available
4. The palette in use

= TODO =

* Make browser compatible (Promises don't work in IE)
* Add option to show/ hide dominant color by default
* Include color search functions
* Add color search query var (or offer a custom page template)
* Add developer functions
* Rewrite code to use PHP Classes
* Create smarter function for inserting palettes in post content (shortcodes stop working when plugin is deactivated).

== Upgrade Notice ==

== Changelog ==

= 0.7 =
* Beta release
