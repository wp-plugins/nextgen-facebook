=== NextGEN Facebook Open Graph ===
Contributors: jsmoriss
Donate Link: http://surniaulula.com/extend/plugins/nextgen-facebook/
Tags: nextgen, featured, attachment, open graph, meta, buttons, like, send, share, image, facebook, google, google plus, g+, twitter, linkedin, social, seo, search engine optimization, pinterest, tumblr, stumbleupon, widget, language, multilingual, shortcode, object cache, transient cache, wp_cache, nggalbum, nggallery, singlepic, imagebrowser, nextgen gallery, gallery, premium, pro
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Requires At Least: 3.0
Tested Up To: 3.5.1
Stable Tag: 5.3

Improve webpage HTML for better Google Search results, ranking, social shares with Facebook, G+, Twitter, LinkedIn, and much more.

== Description ==

**An essential plugin for *every* WordPress website.**

= Adds SEO and Open Graph =

Search Engine Optimization (SEO) and [Open Graph](http://ogp.me/) meta property tags are added to the head section of your webpages, to describe the content for Google Search, Google+, Facebook, and many other social websites. When someone shares your webpages, the title, description, images, videos, author profiles, etc. will be presented properly to the social websites. Open Graph meta property tags are read by almost all social websites, including Facebook, Google (Search and Google+), Twitter and LinkedIn. 

NextGEN Facebook Open Graph (aka NGFB Open Graph) goes beyond other plugins, offering a mature, stable, high-performance platform, with support for various media sources and archive-types. NGFB Open Graph will create appropriate title and description meta tags for category, tag, date based archive (day, month, or year), author webpages, and search results. It also detects and uses images from a variety of sources (featured, attached, preview, shortcodes, etc.), and embedded videos in the content -- including one or more in your Open Graph property tags (see the [FAQ](http://surniaulula.com/extend/plugins/nextgen-facebook/faq/) for an example of Open Graph property tags).

**Once installed and activated, NGFB Open Graph adds all the essential Open Graph meta tags to your webpages immediately -- there's no need to configure any additional settings**. Although if you have at least one image in your Media Library (or NextGEN Gallery) that represents your website, you may want to select it as your Default Image on the General Settings page.

All options are carefully explained on each settings page -- there's no need to purchase extra documentation, how-to videos, etc.

= Provides Social Sharing =

NGFB Open Graph also comes with several multilingual social sharing buttons, that you can add above / below your content or excerpt, as a widget, shortcode, or even call as a function from your theme template(s). NGFB Open Graph includes the following social sharing buttons (see [Other Notes](http://surniaulula.com/extend/plugins/nextgen-facebook/other_notes/) for shortcode and CSS styling examples):

* Facebook
* Google+
* LinkedIn
* Pinterest
* StumbleUpon 
* Tumblr (Links, Quotes, Images, Videos)
* Twitter

= Images and Videos =

All references to images and videos, associated or included in the webpage content, can be used in the Open Graph meta property tags -- and are used by the social websites in a variety of ways. NGFB Open Graph was specifically written to support images from NextGEN Galleries, but works just as well with just the built-in WordPress Media Library. **The NextGEN Gallery plugin is not required to use NGFB Open Graph** -- all options and features work just as well with the WordPress Media Library. Images used in the Open Graph meta property tags (for Posts and Pages) are chosen in this sequence:

1. A *featured* or *attached* image from NextGEN Gallery or the WordPress Media Library.
1. An image from the NextGEN Gallery *ImageBrowser* (in combination with an `&#91;nggalbum&#93;` or `&#91;nggallery&#93;` shortcode).
1. A *preview* image from a NextGEN Gallery `&#91;nggalbum&#93;` or `&#91;nggallery&#93;` shortcode.
1. Image(s) from expanded NextGEN Gallery `&#91;singlepic&#93;`, `&#91;nggallery&#93;` or `&#91;nggtags&#93;` shortcodes.
1. Image(s) from HTML `<img/>` tags in the Post or Page content text.
1. A default image defined in the NGFB plugin settings.

NGFB Open Graph also detects and uses embedded videos from YouTube and Vimeo in your content, including their preview images.

= Best Performance =

Unlike other plugins, NGFB Open Graph is tuned for performance, and makes full use of available caching techniques:

* Using an optional file / disk based cache for javascript and images from social websites (Pro version).
* Persitent ([Transient API](http://codex.wordpress.org/Transients_API)) object caching for the Open Graph meta tags, social buttons widget, shortcodes and content social buttons.
* Non-persistent ([WP Object Cache](http://codex.wordpress.org/Class_Reference/WP_Object_Cache)) object caching for rendered (filtered) Post and Page content.

= Great Support =

NGFB Open Graph is being actively developed and supported. You can review the [FAQ](http://surniaulula.com/extend/plugins/nextgen-facebook/faq/) and [Other Notes](http://surniaulula.com/extend/plugins/nextgen-facebook/other_notes/) pages for additional setup information. If you have questions or suggestions, post them to the WordPress [NGFB Open Graph Support Forum](http://wordpress.org/support/plugin/nextgen-facebook).

= Developer Friendly =

NGFB Open Graph offers a wide variety of filter hooks, functions, methods and constants to customize the output and behavior of the plugin (see the [Other Notes](http://surniaulula.com/extend/plugins/nextgen-facebook/other_notes/) and the included `constants.txt` file for a complete list).

= Pro Version =

**[A Pro version of NGFB Open Graph is also available](http://surniaulula.com/extend/plugins/nextgen-facebook/)**. The Pro version allows you to customize the Open Graph title, description, image, number of images / videos included, and enable / disable social buttons *for each individual Post and Page*. You can also enable a file cache to save social sharing images and JavaScripts locally, and provide URLs to these cached files instead of the originals (improving the page load times in most cases). If you use a CDN or dedicated server to handle static content, the Pro version also includes a URL rewriting feature.

Updating to the Pro version is simple and easy -- click "Purchase the Pro Version" on any NGFB Open Graph settings page, receive an email with your unique Transaction ID, enter the Transaction ID on the Advanced settings page, and update the plugin from within WordPress (like a regular plugin update).

**The *Free* version of NGFB Open Graph is not really "Free"** -- it has taken many months to develop, test, fine-tune and support. The *Free* version is a complete, stable, well supported, and feature rich plugin. If you appreciate the work and effort I've put into this plugin, please [purchase the Pro version](http://surniaulula.com/extend/plugins/nextgen-facebook/) or [contribute a small amount](http://surniaulula.com/extend/plugins/contribution-form/), as a way to help with the continued development and support of NGFB Open Graph.

<a href="http://wordpress.org/support/view/plugin-reviews/nextgen-facebook">A short "Thank You" note and rating on WordPress.org</a> is also *very* much appreciated. ;-)

Thank you,

js.

== Installation ==

= Install Methods =

To install the *Free* version from WordPress.org, follow the *Automated Install* method described bellow.

If you have purchased the Pro version, an email will be sent to you with installation instructions and a unique Transaction ID. Enter your unique Transaction ID on the Advanced settings page of the *Free* version, and after saving the changes, an update will appear on the WordPress Updates page. Update the NGFB Open Graph plugin from within WordPress, as you would any other plugin, to download and activate the new Pro version.

If you downloaded the NGFB Open Graph plugin as an archive file -- either [the Pro version](http://surniaulula.com/extend/plugins/nextgen-facebook/) or [from WordPress.org](http://wordpress.org/plugins/nextgen-facebook/developers/) -- follow the *Automated Removal* (if NGFB Open Graph is already installed) and the *Semi-Automated Install* methods to remove, install, and activate the new plugin. Please take care to check the "Preserve on Uninstall" option on the Advanced settings page, before removing the plugin (as explained in the *Automated Removal* procedure).

If you need to roll-back and re-install an older *Free* version, you can find them all on the [WordPress Developers](http://wordpress.org/plugins/nextgen-facebook/developers/) page.

**Automated Install** (*Free* version)

1. Go to the wp-admin/ section of your website
1. Select the *Plugins* menu item
1. Select the *Add New* sub-menu item
1. In the *Search* box, enter "NextGEN Facebook Open Graph"
1. Click the *Search Plugins* button
1. Click the *Install Now* link for "NextGEN Facebook Open Graph"
1. Click the *Activate Plugin* link

**Semi-Automated Install** (*Free* and *Pro* versions)

Note that if you already have NGFB Open Graph installed, you will have to remove it first, before you can re-install it using this method.

1. Download the plugin archive file (either [the Pro version](http://surniaulula.com/extend/plugins/nextgen-facebook/) or [from WordPress.org](http://wordpress.org/plugins/nextgen-facebook/developers/))
1. Go to the wp-admin/ section of your website
1. Select the *Plugins* menu item
1. Select the *Add New* sub-menu item
1. Click on *Upload* link (just under the Install Plugins page title)
1. Click the *Browser...* button
1. Navigate your local folders / directories and choose the zip file you downloaded previously
1. Click on the *Install Now* button
1. Click the *Activate Plugin* link

**Manual Install** (*Free* and *Pro* versions)

1. Download and unzip the plugin archive file (either [the Pro version](http://surniaulula.com/extend/plugins/nextgen-facebook/) or [from WordPress.org](http://wordpress.org/plugins/nextgen-facebook/developers/))
1. Upload the entire nextgen-facebook/ folder to your website's wordpress/wp-content/plugins/ directory
1. Go to the wp-admin/ section of your website
1. Select the *Plugins* menu item
1. Select the *Installed Plugins* sub-menu
1. Scroll down to the "NGFB Open Graph" plugin, and click its *Activate* link

Once installed and activated, the NGFB Open Graph plugin will start adding all the essential Open Graph meta tags to your webpages immediately -- there's no need to configure any additional settings. Although if you have at least one image in your Media Library or NextGEN Gallery that represents your website, you may want to define that one as your Default Image on the General Settings page.

The plugin settings are available under an *Open Graph* admin menu, where you can select a default image, include social buttons in your content, change the shared thumbnail image size, and much, much more.

= Uninstall Methods =

**Automated Removal**

1. Go to the wp-admin/ section of your website
1. Select the *Open Graph* menu item
1. Select the *Advanced* sub-menu item
1. Check the "Preserve on Uninstall" option if you would like to keep NGFB settings in the database
1. Select the *Plugins* menu item
1. Select the *Installed Plugins* sub-menu
1. Scroll down to the "NGFB Open Graph" plugin, and click its *Deactivate* link
1. Scroll down to the "NGFB Open Graph" plugin, and click its *Delete* link
1. Click on the *Yes, Delete these files and data* button

Note: Unless you checked the "Preserve on Uninstall" option on the Advanced settings page, deleting the plugin will also remove all of its settings from the database.

**Manual Removal**

1. Remove the wordpress/wp-content/plugins/nextgen-facebook/ folder from your website

Note: Removing the plugin folder manually will not remove its settings from the database. This may be desirable if you want to upload a new plugin archive, without loosing the existing settings.

== Frequently Asked Questions ==

= Q. What is the difference between the Pro and <em>Free</em> versions? =

**A.** [The Pro version of NGFB Open Graph](http://surniaulula.com/extend/plugins/nextgen-facebook/) allows you to customize the Open Graph title, description, image, number of images / videos included, and enable / disable social buttons *for each individual Post and Page*. You can also enable a file cache to save social sharing images and JavaScripts locally, and provide URLs to these cached files instead of the originals (improving the page load times in most cases). If you use a CDN or dedicated server to handle static content, the Pro version also includes a URL rewriting feature. 

**The *Free* version of NGFB Open Graph is not really "Free"** -- it has taken many months to develop, test, fine-tune and support. The *Free* version is a complete, stable, well supported, and feature rich plugin. If you appreciate the work and effort I've put into this plugin, please [purchase the Pro version](http://surniaulula.com/extend/plugins/nextgen-facebook/) as a way to help with the continued development and support of NGFB Open Graph.

= Q. How do I install the Pro version? =

Updating to the Pro version is simple and easy -- click on the "Purchase the Pro Version" button on any NGFB Open Graph settings page. After purchasing the Pro version, an email will be sent to you with installation instructions and a unique Transaction ID. Enter your unique Transaction ID on the Advanced settings page, and after saving the changes, an update for NGFB Open Graph will appear on the WordPress Updates page. Update the NGFB Open Graph plugin from within WordPress, as you would any other plugin, to download and activate the new Pro version.

= Q. Can I use the Pro version on multiple websites? =

NGFB Open Graph Pro is [licensed for a Single Website](http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/pro.txt), and each purchase is assigned a unique Transaction ID. You must purchase a new license for each additional website using the NGFB Open Graph Pro plugin. [Contact me by email](mailto:jsm@surniaulula.com) for volume discounts (in multiples of 5 licenses).

= Q. What do the SEO and Open Graph meta tags look like? =

**A.** SEO and [Open Graph](http://ogp.me/) meta property tags are added to the `<head>` section of webpages. Here's an example of some head tags for a Post on [Surnia Ulula](http://surniaulula.com/) titled [WordPress Caching and Plugins for Performance](http://surniaulula.com/2012/12/01/wordpress-caching-and-plugins-for-performance/).

`
<!-- NGFB Open Graph meta tags BEGIN -->
<link rel="publisher" href="https://plus.google.com/b/100429778043098222378/100429778043098222378/posts" />
<link rel="author" href="https://plus.google.com/104808665690163182693/posts" />
<meta property="article:author" content="https://plus.google.com/104808665690163182693/posts" />
<meta property="article:modified_time" content="2013-01-04T08:11:02+00:00" />
<meta property="article:published_time" content="2012-12-01T15:34:56+00:00" />
<meta property="article:section" content="Technology" />
<!-- article:tag:1 --><meta property="article:tag" content="apache" />
<!-- article:tag:2 --><meta property="article:tag" content="apc" />
<!-- article:tag:3 --><meta property="article:tag" content="cache" />
<!-- article:tag:4 --><meta property="article:tag" content="cdn" />
<!-- article:tag:5 --><meta property="article:tag" content="httpd" />
<!-- article:tag:6 --><meta property="article:tag" content="opcode" />
<!-- article:tag:7 --><meta property="article:tag" content="performance" />
<!-- article:tag:8 --><meta property="article:tag" content="php" />
<!-- article:tag:9 --><meta property="article:tag" content="plugins" />
<!-- article:tag:10 --><meta property="article:tag" content="rewrite" />
<!-- article:tag:11 --><meta property="article:tag" content="static content" />
<!-- article:tag:12 --><meta property="article:tag" content="wordpress" />
<meta property="fb:app_id" content="525239184171769" />
<meta property="og:description" content="Over the past few weeks I&#8217;ve been looking at different solutions to improve the speed of my WordPress websites. The first step was to mirror and redirect the static content to another server (aka Content Delivery Network or CDN). In the case of PHP and WordPress, there are several..." />
<!-- og:image:1 --><meta property="og:image" content="http://surniaulula.com/wp-content/gallery/cache/5_crop_300x300_20120814-114043-sbellive-0078.jpg" />
<meta property="og:site_name" content="Surnia Ulula" />
<meta property="og:title" content="WordPress Caching and Plugins for Performance" />
<meta property="og:type" content="article" />
<meta property="og:url" content="http://surniaulula.com/2012/12/01/wordpress-caching-and-plugins-for-performance/" />
<!-- NGFB Open Graph meta tags END -->
`

And another for a gallery Page on [Underwater Focus](http://underwaterfocus.com/) of [underwater images from Bonaire, Netherland Antilles](http://underwaterfocus.com/photographs/locations/oceans-and-islands/atlantic/caribbean/netherlands-antilles/bonaire/).

`
<!-- NGFB Open Graph meta tags BEGIN -->
<link rel="publisher" href="https://plus.google.com/b/103439907158081755387/103439907158081755387/posts" />
<link rel="author" href="https://plus.google.com/104808665690163182693/posts" />
<meta name="description" content="Bonaire (Papiamentu: Boneiru) is a Caribbean island that, with the uninhabited islet of Klein Bonaire nestled in its western crescent, forms a special municipality (officially public body) of the Netherlands. Together with Aruba and Curaçao it forms a group referred to..." />
<meta property="article:author" content="https://www.facebook.com/pages/Underwater-Focus/427082117363463" />
<meta property="article:modified_time" content="2013-01-09T15:55:14+00:00" />
<meta property="article:published_time" content="2012-07-30T15:07:13+00:00" />
<meta property="article:section" content="Photography" />
<!-- article:tag:1 --><meta property="article:tag" content="bonaire" />
<!-- article:tag:2 --><meta property="article:tag" content="netherlands" />
<meta property="fb:app_id" content="125425797600886" />
<meta property="og:description" content="Bonaire (Papiamentu: Boneiru) is a Caribbean island that, with the uninhabited islet of Klein Bonaire nestled in its western crescent, forms a special municipality (officially public body) of the Netherlands. Together with Aruba and Cura&ccedil;ao it forms a group referred to..." />
<!-- og:image:1 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/192_crop_300x300_20070430-153247-jsmoriss-9364.jpg" />
<!-- og:image:1 --><meta property="og:image:height" content="300" />
<!-- og:image:1 --><meta property="og:image:width" content="300" />
<!-- og:image:2 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/191_crop_300x300_20070430-111635-jsmoriss-9316.jpg" />
<!-- og:image:2 --><meta property="og:image:height" content="300" />
<!-- og:image:2 --><meta property="og:image:width" content="300" />
<!-- og:image:3 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/190_crop_300x300_20080514-100511-mevallee-2928.jpg" />
<!-- og:image:3 --><meta property="og:image:height" content="300" />
<!-- og:image:3 --><meta property="og:image:width" content="300" />
<!-- og:image:4 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/189_crop_300x300_20051019-112651-jsmoriss-3661.jpg" />
<!-- og:image:4 --><meta property="og:image:height" content="300" />
<!-- og:image:4 --><meta property="og:image:width" content="300" />
<!-- og:image:5 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/188_crop_300x300_20080514-095134-jsmoriss-0636.jpg" />
<!-- og:image:5 --><meta property="og:image:height" content="300" />
<!-- og:image:5 --><meta property="og:image:width" content="300" />
<!-- og:image:6 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/187_crop_300x300_20080513-145720-jsmoriss-0620.jpg" />
<!-- og:image:6 --><meta property="og:image:height" content="300" />
<!-- og:image:6 --><meta property="og:image:width" content="300" />
<!-- og:image:7 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/186_crop_300x300_20080521-094013-jsmoriss-0918.jpg" />
<!-- og:image:7 --><meta property="og:image:height" content="300" />
<!-- og:image:7 --><meta property="og:image:width" content="300" />
<!-- og:image:8 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/185_crop_300x300_20051031-145248-jsmoriss-4345.jpg" />
<!-- og:image:8 --><meta property="og:image:height" content="300" />
<!-- og:image:8 --><meta property="og:image:width" content="300" />
<!-- og:image:9 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/184_crop_300x300_20050509-150718-jsmoriss-2667.jpg" />
<!-- og:image:9 --><meta property="og:image:height" content="300" />
<!-- og:image:9 --><meta property="og:image:width" content="300" />
<!-- og:image:10 --><meta property="og:image" content="http://underwaterfocus.com/wp-content/gallery/cache/183_crop_300x300_20051102-090501-jsmoriss-4458.jpg" />
<!-- og:image:10 --><meta property="og:image:height" content="300" />
<!-- og:image:10 --><meta property="og:image:width" content="300" />
<meta property="og:site_name" content="Underwater Focus" />
<meta property="og:title" content="Bonaire" />
<meta property="og:type" content="article" />
<meta property="og:url" content="http://underwaterfocus.com/photographs/locations/oceans-and-islands/atlantic/caribbean/netherlands-antilles/bonaire/" />
<!-- NGFB Open Graph meta tags END -->
`

= Q. Why doesn't Facebook show the correct Open Graph image? =

**A.** The first time Facebook accesses your webpage, it will cache the image and text it finds. Facebook then prefers to use the cached information until it has expired. So, before you hit the Facebook send / share button for the first time, make sure you're satisfied with your Post or Page images and text. If you change your mind, *and your webpage has not been liked or shared yet*, you can use [Facebook's Open Graph debugging tool](https://developers.facebook.com/tools/debug) to refresh Facebook's cache. If your webpage has already been liked or shared on Facebook, then there's nothing you can do to change the title, descriptive text, or image that was used.

= Q. How can I see what Facebook sees? =

**A.** Facebook has an [Open Graph debugging tool](https://developers.facebook.com/tools/debug) where you can enter a URL and view a report of it's findings. Try it with your Posts, Pages, archive pages, author pages, search results, etc. to see how NGFB presents your content. If there are Open Graph warnings, read them carefully -- usually they explain that the information they *already have* for this webpage is in conflict with the Open Graph information now being presented. This might be just the published and modified times, or (if the webpage has already been liked or shared) the title and image Facebook has saved previously.

= Q. Why does Facebook play videos instead of linking them to my webpage? =

**A.** The NGFB Open Graph plugin generates information about the current webpage and its content - what social websites like Facebook do with that information is beyond our control. When Facebook is given information on videos, it embeds and plays them directly, instead of linking the preview image (for example) to the source website. There are two possible solutions:

1. Turn off video discovery completely by setting "Maximum Number of Videos" to "0" on the NGFB settings page.
1. Uncheck the `og:video`, `og:video:width`, `og:video:height`, and `og:video:type` meta tags. This will leave the video preview images, but exclude information on the videos themselves.

= Q. What about Google Search and Google Plus? =

**A.** Google reads the Open Graph meta tags as well, along with other "structured data markup" on your webpage. You can see what Google picks up from your webpages by using it's [Rich Snippets Testing Tool](http://www.google.com/webmasters/tools/richsnippets). *Use the "Author Link URL" and "Publisher Link URL" options on the NGFB settings page to have Google associate author profiles with your search results.

= Q. Does LinkedIn read the Open Graph tags? =

**A.** According to LinkedIn's [Setting Display Tags for Shares](https://developer.linkedin.com/documents/setting-display-tags-shares) information page, they use three of the Open Graph tags (title, description, and url).

= Q. The W3C Markup Validation Service says "there is no attribute '<em>property</em>'". =

**A.** The Facebook / Open Graph meta *property* attribute is not part of the HTML5 standard, so the [W3C Markup Validator](http://validator.w3.org/) is correct in throwing up an error. In practice though, this incorrect attribute is completely harmless -- social sites (Facebook, Google+, etc.) look for it and don't care if it's part of the standard or not. If you want to address the W3C validator error, you'll have to change the DOCTYPE of your website to XHTML+RDFa (an example follows). The DOCTYPE definition is usually located in the `header.php` file of your theme.

`
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
`

= Q. Why does the Facebook "Like" button flyout get clipped? =

**A.** This is a known issue with the JavaScript code Facebook uses. If the "Like" button is placed near the edge of an HTML element with the overflow property set to hidden, the flyout may be clipped or completely hidden when the button is clicked. This can be remedied by setting the overflow property to a value other than hidden, such as visible, scroll, or auto. For example:

<pre>
#page { overflow:visible; }
</pre>

There is also a known issue with Facebook's "Like" button flyout and the WP *Twenty Eleven* and *Twenty Twelve* based themes. Including the following CSS in your stylesheet should fix the problem:

`
.ngfb-buttons iframe { max-width:none; }
`

= Q. Why are there duplicate Facebook / Google fields on the user profile page? =

**A.** NGFB Open Graph adds a "Facebook URL" and "Google URL" field to the profile page. If you already have another plugin that adds these fields to the profile page (under different names), you can tell NGFB to use these other field names instead. You can also remove or change the description of these additional fields (changing "Google URL" to "Google Link" for example). See the "Rename or Add Profile URL Fields" section in the [Other Notes](http://surniaulula.com/extend/plugins/nextgen-facebook/other_notes/) tab for additional information.

= Q. How does NGFB find images to include in the Open Graph meta tags? =

**A.** The images used in the Open Graph meta property tags for Posts and Pages are chosen in this sequence:

1. A *featured* or *attached* image from NextGEN Gallery or the WordPress Media Library.
1. An image from the NextGEN Gallery *ImageBrowser* (in combination with an `&#91;nggalbum&#93;` or `&#91;nggallery&#93;` shortcode).
1. A *preview* image from a NextGEN Gallery `&#91;nggalbum&#93;` or `&#91;nggallery&#93;` shortcode.
1. Image(s) from expanded NextGEN Gallery `&#91;singlepic&#93;`, `&#91;nggallery&#93;` or `&#91;nggtags&#93;` shortcodes.
1. Image(s) from HTML `<img/>` tags in the Post or Page content text.
1. A default image defined in the NGFB plugin settings.

= Q. Why does NGFB ignore the &lt;img/&gt; HTML tags in my content? =

If one or more `<img/>` HTML tags is being ignored, it's probably because the **image width and height attributes are missing, or their values are less than the 'Image Size Name' you've chosen on the settings page**. NGFB will only use an image equal to, or larger than, the 'Image Size Name' you've chosen.

If you want to display smaller image thumbnails in your content (on index webpages, for example), and still have NGFB use the larger versions of those thumbnails, you can add a "share" attribute with a URL to the larger image. For example:

`
<img
    share="http://underwaterfocus.com/wp-content/gallery/cache/40_crop_200x200_20080514-152313-mevallee-2951.jpg"
    src="http://underwaterfocus.com/wp-content/gallery/2008-05-bonaire-na/thumbs/thumbs_20080514-152313-mevallee-2951.jpg"
    width="150" height="150" />
`

The order in which the attributes are listed is important -- place the "share" attribute before the "src" attribute to give it a higher priority. If you do not want (or cannot add) a "share" attribute to the `<img/>` HTML tag, and would like NGFB to share smaller image thumbnails, you can uncheck the 'Ignore Small Images' option on the plugin settings page. You can also disable the feature by using the following constant in your wp-config.php or template files (before the `wp_head()` function call).

`
define( 'NGFB_MIN_IMG_SIZE_DISABLE', true );
`

= Q. How can I share a single NextGEN Gallery image? =

**A.** You could create a Page with the `&#91;singlepic&#93;` shortcode, or select the "Show ImageBrowser" option in the Gallery settings. When using the "ImageBrowser" option, images will be displayed on their own, with a unique URL that can be shared (instead of layering an effect over the current browser window).

= Q. How can I exclude certain parts of the content text? =

**A.** By default, NGFB will use the excerpt for the Open Graph description value. If an excerpt isn't available, the content text will be used instead. If there are parts of your content text that you don't want NGFB to pickup, you can wrap those sections between `<!--no-text-->` and `<!--no-text-->` comment tags.

== Resources ==

[Follow this RSS feed from Surnia Ulula](http://surniaulula.com/category/application/wordpress/wp-plugins/ngfb/feed/) for news about the NGFB Open Graph plugin.

Need help? See the plugin [FAQ](http://surniaulula.com/extend/plugins/nextgen-facebook/faq/), [Other Notes](http://surniaulula.com/extend/plugins/nextgen-facebook/other_notes/) or visit the [Support Forum](http://wordpress.org/support/plugin/nextgen-facebook) on WordPress.org.

== Shortcodes ==

You can add one or more social sharing buttons to your content by using the `&#91;ngfb&#93;` shortcode. For example:

`
&#91;ngfb buttons="facebook, gplus, linkedin, pinterest, stumbleupon, tumblr, twitter"&#93;
`

Note: **The "Enable Shortcode" option must be enabled on the NGFB settings page**, and like all other methods used to add NGFB social buttons (enabled from the settings page, widget, etc.), the **Pinterest button will only show on posts with a *featured* or *attached* image**.

== Stylesheets ==

= Social Buttons Style =

NGFB Open Graph uses several layers of classes to wrap all its ocial buttons, and each button has it's own individual class name as well. You may add styling for social buttons to an existing stylesheet (from your theme, for example), or use the built-in stylesheet found on the Social Sharing settings page. The stylesheet is saved as `wp-content/uploads/ngfb-social-buttons.css` -- outside of the plugin folder, so it won't be removed when the plugin is updated.

The default CSS file, located in `wp-content/plugins/nextgen-facebook/css/social-buttons.css`, contains a fairly complete example of CSS styling for the NGFB social buttons. You should note that I've specified the width (and height) for each button's `<div>`. This takes a little more work to get right, but *pre-defining the height and width of each button area helps the page rendering speed significantly*. The `.ngfb-buttons` class is included within one of four other classes; `.ngfb-content-buttons` and `.ngfb-excerpt-buttons` for buttons enabled on the Social Sharing settings page, `.ngfb-widget-buttons` for buttons enabled from the widget, and `.ngfb-shortcode-buttons` for buttons added using the `&#91;ngfb&#93;` shortcode.

= Hide Social Buttons =

You can also hide the social buttons (or pretty much any object) in a webpage or post by using `display:none` in your stylesheet. As an example, if you use the "Inspect Element" feature of Firefox (right-click on the object to inspect) -- or use "View Source" to see the webpage's HTML -- you should find your content wrapped in a `<div>` HTML tag similar to this one:

`
<div class="postid-123 post type-post status-publish format-standard 
	hentry category-test category-wordpress tag-css tag-html">
		The post content text...
</div>
`

You could use any of these class names to hide one or more NGFB social buttons enabled on the settings page. For example, the following stylesheet hides the social buttons on Post <em>123</em>, any page in category <em>Test</em>, and posts using the Aside and Status formats:

`
.post-123 .ngfb-buttons,
.category-test .ngfb-buttons,
.format-aside .ngfb-buttons,
.format-status .ngfb-buttons { display:none; }
`

[The Pro version of NGFB Open Graph](http://surniaulula.com/extend/plugins/nextgen-facebook/) includes customized settings for each Post and Page, which allows you to enable / disable social buttons for each particular Post and Page without the use of CSS.

== Performance Tuning ==

The code for NGFB is highly optimized -- the plugin will not load or execute code it does not have to. You may consider the following option settings to fine-tune the plugin for optimal performance.

* If your website content does not have any embedded videos, or you prefer not to include information on embedded videos in your Open Graph meta property tags, you can set the "Maximum Number of Videos" to "0". This will prevent the plugin from searching your content text for embedded videos.

* If you generally have a *featured* image for your posts and pages, you may set the "Maximum Number of Images" to "1". This will prevent the plugin from searching your content for additional images (the *featured* image counts as "1" and the plugin will stop there).

* For posts and pages, if no excerpt text has been entered, the content text is used to define the Open Graph description meta property value. If you generally don't use excerpts, and your content does not rely on shortcodes or plugins to render its text, you may uncheck the "Apply Content Filters" option.

* If you don't use the `&#91;ngfb&#93;` shortcode, you can uncheck the "Enable Shortcode" option if it has been enabled (the default is unchecked).

* If your infrastructure can serve JavaScript and image files faster and more reliably than Facebook, Google+, etc., you can set the "File Cache Expiry" option to several hours (the default of "0" hours disables this option).

* If the featured image, excerpt (or content text), etc., is not generally revised after publishing, you can increase the "Object Cache Expiry" option from 60 seconds to several minutes.

== Advanced Usage ==

= Include Social Buttons from Template File(s) =

The `ngfb_get_social_buttons()` function can be used to include social buttons anywhere in your template files. As an example, the following PHP code includes the Facebook, Google+, and Twitter social buttons from within a loop, post, or page (the `$post->ID` must be available):

`
<?php if ( function_exists( 'ngfb_get_social_buttons' ) ) 
	echo ngfb_get_social_buttons( array( 'facebook', 'gplus', 'twitter' ) ); ?>
`

The social button names for the array can be "facebook", "gplus", "linkedin", "pinterest", "stumbleupon", "tumblr", and "twitter".

You can also use the `ngfb_get_social_buttons()` function *outside* of a loop, post, or page, but you will have to provide additional information to the function. Since the `$post` variable is not available outside of a loop (to get the permalink), at a minimum you will have to provide the webpage URL. Here's an example from a custom NextGEN Gallery template (plugins/nextgen-gallery/view/): 

`
if ( function_exists( 'ngfb_get_social_buttons' ) ) { 
	$url = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
	$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	echo ngfb_get_social_buttons( array( 'pinterest', 'tumblr' ),
		array ( 'pid' => 'ngg-'.$image->pid, 'url' => $url, 'caption' => $image->caption ) );
}
`

This creates a Pinterest and Tumblr button to share a picture from a NextGEN Gallery, sets the URL to the current webpage address, and uses the picture's caption as well. All social buttons, besides Pinterest and Tumblr, only need the URL defined.

= Disable Open Graph Meta Tags =

You can exclude the Open Graph meta tags from being added to certain webpages. You must set the `NGFB_OPEN_GRAPH_DISABLE` constant to true in your theme's header.php before the `wp_head()` function. Here's an example that disables NGFB's meta tags for image search results (a custom 'meta' template is called to define the Open Graph tags):

`
global $nggSearch
if ( is_search() && $nggSearch->found_images ) {
	define( 'NGFB_OPEN_GRAPH_DISABLE', true );
	echo $nggSearch->return_result( 'meta' );
}
wp_head();
`

= Rename or Add Profile URL Fields =

By default, NGFB adds two new URL fields to the user profiles -- the Facebook URL with a field name of "facebook" and the Google+ URL with a field name of "gplus". This is in keeping with the standard field names I've observed. If you need to change the field names, or their description, you can define the NGFB_CONTACT_FIELDS constant in your wp-config.php file. The default value for NGFB_CONTACT_FIELDS is:

`
define( 'NGFB_CONTACT_FIELDS', 'facebook:Facebook URL,gplus:Google+ URL' );
`

A comma separates the the different fields, and a colon seperates each field name from it's descriptive text. You may redefine the existing fields, remove them by leaving an empty string, or add to the existing list.

If you already have another plugin that adds Facebook and Google+ fields to the profile page (under different names), you can define this variable with those names. For example, if another plugin uses a "gplus_link" field, you can define the NGFB_CONTACT_FIELDS as shown above, changing the "gplus" field name to "gplus_link". This way, it will avoid having duplicate fields on the profile page, and that field will appear in the NGFB settings page.

= NGFB Filter Hooks =

Several [filter hooks](http://codex.wordpress.org/Function_Reference/add_filter) are available within the [NGFB Open Graph Pro](http://surniaulula.com/extend/plugins/nextgen-facebook/) plugin to manipulate text (title, description, content, etc.) and arrays (tags, open graph, etc.). For example, here is a filter I use on [UnderwaterFocus](http://underwaterfocus.com/) to remove the 'Wiki-' prefix from WordPress tags. The following code adds the `uwf_filter_ngfb_tags()` function to the 'ngfb_tags' filter. The function receives an array of tags, which it can transform and return.

`
add_filter( 'ngfb_tags', 'uwf_filter_ngfb_tags', 10, 1 );

function uwf_filter_ngfb_tags( $tags = array() ) {
        foreach ( $tags as $num => $tag_name ) {
                $tag_name = preg_replace( "/^wiki-/", '', $tag_name );
                $tags[$num] = $tag_name;
        }
        return $tags;
}
`

Filter functions in NGFB are called with a single argument. Using the WordPress example from http://codex.wordpress.org/Function_Reference/add_filter, the `$accepted_args` parameter should always be "1" for NGFB filters.

`
<?php add_filter( $tag, $function_to_add, $priority, $accepted_args ); ?> 
`

The following list of NGFB filters receive, and must return, a standard text string.

* ngfb_section : The section text string used in the article:section meta tag.
* ngfb_title : The title string used in the og:title meta tag. If the page contains multiple pages, the page numbers will be included in the title text string.
* ngfb_description : The description string used in the og:description meta tag.
* ngfb_content : The filtered (shortcode resolved, etc.) or un-filtered (depending on settings) content of the Post or Page.
* ngfb_caption : The caption used by Tumblr, Pinterest, and Twitter sharing buttons.
* ngfb_quote : The quote text used by the Tumblr sharing button.

The following list of NGFB filters receive an array, and must return an array.

* ngfb_tags : An array of WordPress and NextGEN Gallery tags (if applicable and allowed by settings) used in the article:tag meta tags.
* ngfb_wp_tags : An array of WordPress Post and Page tags used in the article:tag meta tags.
* ngfb_ngg_tags : An array of NextGEN Gallery image tags used in the article:tag meta tags.
* ngfb_og : A complete, multi-dimensional array of all Open Graph meta tags.

= PHP Constants =

To address very specific needs, some PHP constants for NGFB may be defined in your `wp-config.php` or template files (generally before the `wp_head()` function call). The complete list of constants, and a description of their intended use, can be found in the `constants.txt` file located in the `wp-content/plugins/nextgen-facebook/` plugin folder.

== Screenshots ==

1. Screenshot 1 : About Page (Pro Version 5.3)
2. Screenshot 2 : General Settings Page (Pro Version 5.3)
3. Screenshot 3 : Social Sharing Settings Page (Pro Version 5.3)
4. Screenshot 4 : Advanced Settings Page (Pro Version 5.3)
6. Screenshot 5 : Custom Post and Page Settings (Pro Version 5.3)

== Changelog ==

= Version 5.4 DEV =

* Added licence files for the *Free* and Pro versions in the nextgen-facebook/licence/ folder.
* Added a 'Social StyleSheet' metabox on the Social Sharing settings page. In the past, NGFB did not come with a stylesheet, but now as an option, you can edit and include one from the Social Sharing settings page.

= Version 5.3 =

* Fixed saving of custom Post and Page settings in the Pro version.
* Added the ability for the *Free* version to self-update to the Pro version, after entering a valid Transaction ID on the Advanced settings page. 

= Version 5.2.3 =

* Added a check for PHP's safe_mode and/or open_basedir before setting CURLOPT_FOLLOWLOCATION (which is not compatible with these PHP options).
* Added a check for NGFB_CURL_DISABLE before using PHP's curl to shorten URLs for Twitter.
* Minor changes to the markdown class (used to parses the readme.txt) to avoid a 503 error reported by some people.

= Version 5.2.2 =

* Fixed caching method to return no data when URL has failed (instead of returning the error message).
* Improved caching method to ignore failed URLs for 300 seconds.
* Improved readme.txt parsing to use the (older) local copy, if the readme.txt from WordPress.org is not available.
* Added the NGFB_CURL_DISABLE, NGFB_CURL_PROXY, and NGFB_CURL_PROXYUSERPWD constants.
* Renamed the NGFB_USER_AGENT constant to NGFB_CURL_USERAGENT.
* Renamed the NGFB_PEM_FILE constant to NGFB_CURL_CAINFO.

= Version 5.2.1 =

* Fixed a small typo in the goo.gl URL shortening class.
* Removed a few unused functions in the third-party markdown class.
* Added file caching to Tumblr and StumbleUpon images on the Social Sharing settings page.

= Version 5.2 =

* Added a "Clear All Cache" button on the *Plugin Information* metabox.
* Added a "Check for Updates" button on the *Plugin Information* metabox in the Pro version.
* Added a daily schedule (as part of 'wp_scheduled_delete') to remove expired cached files and transient object cache items.
* The Pro version update check schedule is adjusted (if necessary) to match the desired frequency (every 12 hours).
* Optimized the library class requirements and class object creation for admin and non-admin webpages.

= Version 5.1.1 =

* Fixed the `uninstall()` method to read plugin options as a stand-alone method.
* Improved the plugin `activate()` method to terminate sooner (and hand-off to the `init_plugin()` method that follows it).
* Added a `deactivate()` method to remove the Pro version "update check" cronjob (if any) upon deactivation.
* Added an NGFB_WP_DEBUG constant to send status / debug messages to WP's debug log as well. Add `define('NGFB_WP_DEBUG', true);` to the `wp-config.php` file to enable.

= Version 5.1 =

* The social website configurations (on the "Social Sharing" settings page) have been moved into their own individual setting boxes. The new layout is quite slick -- the social website boxes can be moved, re-arranged, removed, etc., all within a two column layout.
* Added a "Preserve on Uninstall" option on the Advanced settings page (default is unchecked). Checking this option preserves NGFB Open Graph settings when uninstalling the plugin (useful when upgrading to [the Pro version](http://surniaulula.com/extend/plugins/nextgen-facebook/)).  
* Added static content rewriting options for CDNs on the Advanced Settings page. The new Rewrite Settings allow you to enter CDN URLs, choose folders to include / exclude, etc.
* Removed the "Use WP-WikiBox for Pages" and "WP-WikiBox Tag Prefix" options. Customized content and tags can now be managed by with the new 'ngfb_description' and 'ngfb_tags' filter hooks (among many others). See the [Other Notes](http://surniaulula.com/extend/plugins/nextgen-facebook/other_notes/) for more information on NGFB filter hooks.
* Fixed the missing Pinterest button in the widget, when using both the widget and content social sharing buttons (the widget would detect the featured image as a duplicate, and not include the Pinterest button).

= Version 5.0.1 =

* Added a check to verify that the cache directory/files is writable/readable, preempting a possible PHP write/read error.
* Improved the option sanitation method to re-create missing checkbox options (checkboxes are not submitted by HTML forms when un-checked). This should fix the problem where checked options could not be unchecked.

= Version 5.0 =

Complete code review with an improved object-oriented design and several new classes.

* Added the ability to include social sharing buttons in the excerpt as well.
* Added a new "Custom Post/Page Settings" metabox for each individual Post and Page (enabled by purchasing the Pro version).
* Added transient caching to the url shortening method (reducing the number of requests to goo.gl).
* Streamlined the image discovery methods to improve performance (methods check the Maximum Images limit more often).
* Removed support for the "Exclude Pages" plugin (in the past, social sharing buttons were not added to excluded pages) -- social sharing buttons can now be disabled for individual Posts and Pages on the new "Custom Post/Page Settings" options box.
* Complete over-haul of the (too long) settings page, breaking it up into several pages under the new "Open Graph" menu item.
* This new version also allows you to unlock some Pro features, giving you the option to fine-tune the Title, Description, Images, etc., on individual Pages and Posts. The standard version of NextGEN Facebook Open Graph remains a complete, mature and full-featured plugin -- if you would like to thank me for my efforts, please consider purchasing the Pro version. Thanks.

== Upgrade Notice ==

= 5.4 DEV =

Added licence files for the *Free* and Pro versions, added a 'Social StyleSheet' metabox on the Social Sharing settings page.

= 5.3 =

Fixed saving of custom Post and Page settings in the Pro version, and added the ability for the *Free* version to self-update to the Pro version. 

= 5.2.3 =

Added a check for PHP's safe_mode and/or open_basedir before setting CURLOPT_FOLLOWLOCATION, and added a check for NGFB_CURL_DISABLE constant before using PHP's curl to shorten URLs, minor fixes to markdown class to avoid a 503 error.

= 5.2.2 =

Improved caching method to ignore failed URLs for 300 seconds, added PHP curl related constants, and added local fallback for readme.txt.

= 5.2.1 =

Fixed a small typo in the goo.gl URL shortening class and removed a few un-used functions in the third-party markdown class.

= 5.2 =

Added a "Clear All Cache" and "Check for Updates" button on the NGFB settings pages, a daily scheduled job to remove expired transient cache objects, and optimized the library class requirements / class object creation.

= 5.1.1 =

Minor fix for the `uninstall()` method, minor improvement to the `activate()` method, added a new `deactivate()` method, and added support for WP's debug log. See the [Changelog](http://surniaulula.com/extend/plugins/nextgen-facebook/changelog/) for more details.

= 5.1 =

Improved the social website configuration layout, added a new "Preserve on Uninstall" option, added new Static Content rewriting options (Pro version), and new filter hooks for Open Graph data and meta tags (Pro version).

= 5.0.1 =

Fixed problem where checked options could not be unchecked. Added verification for directory/file permissions when caching content.

= 5.0 =

Complete code review with an improved object-oriented design. New features include social buttons in excerpts, transient caching of shortened urls, streamlined image discovery, complete over-haul of the settings page, and a Pro version with support for individual Post and Page settings.

