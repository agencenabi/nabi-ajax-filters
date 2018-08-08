# Nabi Ajax Filters
Tags: ajax, filter, posts, pagination
Requires at least: 4.0
Tested up to: 4.9.8
Stable tag: 1.0

A WordPress Plugin allowing to filter posts by taxonomies and add a dynamic pagination.

## Description
Use shortcode ```[NabiFilter]``` to create a nice filter posts feature.

Attributes for this shortcode include:
* ```posttypes``` - Comma separated post types that you want to display. Default is “post”.
* ```taxonomies``` - Comma separated taxonomies that you want to be able to filter by. Default is none.
* ```showcount``` - Set to 0 to show post count. Set to 1 hide post count. Default is 0.
* ```pagination``` - Accepts values "top,bottom", "top", "bottom" depend where you want your pagination. Default is top,bottom.
* ```posts_per_page``` - Set the amount of posts per page. Default is 8.
* ```shownav``` - Set to 0 to show navigation. Set to 1 hide the navigation. Default is 0.

**Example:**
```[NabiFilter posttypes="portfolio" taxonomies="type" posts_per_page="6"]```


## Installation
Install from wordpress plugins directory.

Else, to install manually:

1. Upload unzipped plugin folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


## TODO
* Add a parameter in the shortcode to enable/disable multiple filters selections: ajax-filter.php (line 80), nbf-script.js (line 129).
* Add a loading animation: ajax.php (line 246).
* Add a parameter in teh shortcode to chose a content display option (Ex.: Masonry, list, etc.).
* Add basic CSS styling: style.css
* Concatenate JS. Compress with Gulp.


## Changelog

= 1.0 =
* Version 1.
