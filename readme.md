# Color Palette Generator

Generate color palettes for your WordPress images, append them to your images inside your content and make searching images by color available to your visitors.

## Description

This plugin is ideal for blog owners who like to do something more with their images, or for webshop owners who like to filter products by color. When inserting images you have the option to show the palette for that image. You can also use the following shortcode to show a specific image with palette: `[colorpalette attachment="56" dominant="false" colors="10" size="large" random="true"]`

## Features

* Generate color palettes per image
* Bulk-generate color palettes for all images
* Allows configuration of number of unique colors per image
* Allows configuration of auto appending palettes on attachment pages
* See stats of generated palettes
* Filter attachments by color
* Add color search widget to your sidebar
* Use a shortcode to show an image with a palette in your content.

## Developers

If you don't like the way the plugin outputs the palettes, or you want to create custom color queries yourself, use the wordpress taxonomy functions. There are 2 new taxonomies added when the plugin is activated: `cpg_dominant_color` and `cpg_palette`. The first one holds one color per image, the dominant color (this color is used to link the image to one of the main colors used in the color search functions). The second one holds the palette, and the number of colors is based on what the site owner has entered (default = 10).

If you want to create your own search template, add a templates folder in your theme and add a search.php file in there. If that file is found it will be used to show the searchresults. Else the template from the plugin will be used.

## Special thanks

* Lokesh Dhakar, for the [color thief library](https://github.com/lokesh/color-thief)
* Projekod, for the [Round Color class](https://github.com/Projekod/RoundColor)

## Installation

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

## Screenshots

[Available here](https://github.com/houke/color-palette-generator/tree/master/screenshots)

1. The Color palette generator settings page
2. A new media library column will be available
3. When inserting images into your content, new options will be available
4. Add a widget to your sidebar
5. The palette in use
6. A list of generated palettes

## Todo

* Better uninstall functions (attachment meta needs to be removed)
* Generate palette on upload

## Changelog

### 1.6.2

* Fixed php warning
* Fixed missing color palettes if no dominant colors are set

### 1.6.1

* Fixed php warning

### 1.6

* Changed the dasboard layout
* Added a remove button to remove all generated palettes
* Added an option to control how the palette colors are shown
* The bulk generation is now around 10 times faster (especially noticeable with more than 10.000 images in your library)

### 1.5.2

* Fixed loading of front-end styles

### 1.5.1

* Removed 'Dominant: ' title before color codes when hovering over palette colors.

### 1.5

* Added 2 new settings to allow clicking the dominant & palette colors. Each color will link to the search page of the related dominant color.

### 1.4

* Added new shortcode to show all generate palettes (without the images)

### 1.3

* Validate, sanetize & escape

### 1.2

* Removed TAH references

### 1.1

* Fix WordPress warnings

### 1.0

* First official release

### 0.9

* Final beta release

### 0.8

* Beta release

### 0.7

* Beta release
