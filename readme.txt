=== NextGEN Facebook ===
Contributors: jsmoriss
Tags: nextgen, facebook, featured, open graph, meta, buttons, like, send, share
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 1.0
License: GPLv2 or later

Adds Facebook HTML meta tags to webpage headers, including featured images. Also includes optional Like and Send Facebook buttons.

== Description ==

The NextGEN Facebook plugin adds Facebook Open Graph HTML meta tags (admins, app_id, title, type, image, site_name, description, and url) to all webpage headers. The featured image, from a NextGEN Gallery or Media Library, in a Post or Page will be used in it's meta tags. The plugin also includes an option to add Like and Send Facebook buttons to your Posts and Pages.

Although this plugin was written to retrieve featured image information from a NextGEN gallery, it also works just as well without it.

The image used in the Open Graph meta tag will be determined in this sequence; a featured image from a NextGEN Gallery or WordPress Media Library, the first NextGEN [singlepic] or IMG HTML tag in the content, a default image URL defined in the plugin settings. If none of these conditions can be satisfied, then the Open Graph image tag will be left empty.

<!-- NextGEN Facebook plugin open graph tags BEGIN -->
<meta property="fb:admins" content="" />
<meta property="fb:app_id" content="" />
<meta property="og:title" content="Title of a WordPress Post" />
<meta property="og:type" content="article" />
<meta property="og:image" content="http://trtms.com/wp-content/gallery/cache/136_crop_200x200_featured-image-filename.jpg" />
<meta property="og:site_name" content="The Road to Myself" />
<meta property="og:description" content="A short amount of text, taken from the excerpt or content, that's used by Facebook when displaying the like or share information box." />
<meta property="og:url" content="http://trtms.com/2012/06/24/title-of-a-wordpress-post/" />
<!-- NextGEN Facebook plugin open graph tags END -->

== Installation ==

Activate the plugin. You don't have to configure any settings for NextGEN Facebook to add the Open Graph meta tags. Some options are available under Settings->NextGEN Facebook to define a default image, include facebook buttons at the end of Posts and Pages, change the thumbnail image sizes, etc.

== Upgrade Notice ==

None

== Screenshots ==

1. WP 3.4.1 - The first-half of the settings page
2. WP 3.4.1 - The second-half of the settings page

== Frequently Asked Questions ==

None

== Changelog ==

= v1.0 =

* Initial release.

