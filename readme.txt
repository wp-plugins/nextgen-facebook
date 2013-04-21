=== NextGEN Facebook Open Graph ===
Contributors: jsmoriss
Tags: nextgen, featured, attachment, open graph, meta, buttons, like, send, share, image, wp-wikibox, wikipedia, facebook, google, google plus, g+, twitter, linkedin, social, seo, search engine optimization, exclude pages, pinterest, tumblr, stumbleupon, widget, cdn linker, language, multilingual, shortcode, object, cache, transient, wp_cache
Requires at least: 3.0
Tested up to: 3.5.1
Stable tag: 4.0.5
License: GPLv3 or later

Adds complete Open Graph meta tags for Facebook, Google+, Twitter, LinkedIn, etc., plus optional social sharing buttons in content or widget.

== Description ==

*The [Open Graph](http://ogp.me/) protocol allows any webpage to become a rich object in a social setting. The Open Graph meta property tags are used by Facebook to allow any webpage to have the same functionality as other objects on Facebook. The tags are read by almost all social websites, including Facebook, Google (Search and Google+), and LinkedIn.*

**The NextGEN Facebook Open Graph (NGFB) plugin adds Open Graph meta property tags to all webpage headers**, including the article object type for Posts and Pages. This plugin goes well beyond other plugins I know in handling various archive-type webpages. It will create appropriate title and description meta tags for category, tag, date based archive (day, month, or year), author webpages, search results. NGFB detects images of varying sizes and embedded videos -- and includes one or more of each in your Open Graph property tags (see the [FAQ](http://wordpress.org/extend/plugins/nextgen-facebook/faq/) for an example of Open Graph property tags).

**You can also add multilingual social sharing buttons above or bellow content, as a widget, shortcode, or even use a function from your templates**. NGFB includes the following social sharing buttons (see the [Other Notes](http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/) tab for shortcode and CSS styling examples):

* Facebook
* Google+
* LinkedIn
* Pinterest
* StumbleUpon
* Tumblr
* Twitter

**NGFB was specifically written to support images from NextGEN Galleries, but works just as well with the built-in WordPress Media Library**. *The NextGEN Gallery plugin is not required to use this plugin* -- all options and features work just as well without it. 

**NextGEN Facebook Open Graph (NGFB) is tuned for performance and makes full use of various caching techniques**:

* Optional file / disk based caching for javascript and images from social websites.
* Non-persistent ([WP Object Cache](http://codex.wordpress.org/Class_Reference/WP_Object_Cache)) object caching for rendered (filtered) Post and Page content.
* Persitent ([Transient API](http://codex.wordpress.org/Transients_API)) object caching for the Open Graph meta tags, social buttons widget, shortcodes and content social buttons.

NGFB is being actively developed and supported. You can review the [FAQ](http://wordpress.org/extend/plugins/nextgen-facebook/faq/) and [Other Notes](http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/) pages for additional setup information. If you have questions or suggestions, post them on the NGFB [Support Page](http://wordpress.org/support/plugin/nextgen-facebook).

== Installation ==

*Using the WordPress Dashboard*

1. Login to your weblog
1. Go to Plugins
1. Select Add New
1. Search for *NextGEN Facebook Open Graph*
1. Select Install
1. Select Install Now
1. Select Activate Plugin

*Manual*

1. Download and unzip the plugin
1. Upload the entire nextgen-facebook/ folder to the /wp-content/plugins/ directory
1. Activate the plugin through the Plugins menu in WordPress

Once activated, you don't have to configure any settings for NGFB to automatically start adding Open Graph meta tags to your pages.

Some plugin options are available under the *Settings / NextGEN Facebook* admin menu to select a default image, include social buttons in your content, change the shared thumbnail image size, etc.

== Frequently Asked Questions ==

= Q. What do the Open Graph property tags look like? =

[Open Graph](http://ogp.me/) property tags are added to the `<head>` section of webpages. Here's an example of the Open Graph meta property tags for a Post on [Surnia Ulula](http://surniaulula.com/) titled [WordPress Caching and Plugins for Performance](http://surniaulula.com/2012/12/01/wordpress-caching-and-plugins-for-performance/).

`
<!-- NextGEN Facebook Open Graph (NGFB) meta tags BEGIN -->
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
<!-- NextGEN Facebook Open Graph (NGFB) meta tags END -->
`

And another for a gallery Page on [Underwater Focus](http://underwaterfocus.com/) of [underwater images from Bonaire, Netherland Antilles](http://underwaterfocus.com/photographs/locations/oceans-and-islands/atlantic/caribbean/netherlands-antilles/bonaire/).

`
<!-- NextGEN Facebook Open Graph (NGFB) meta tags BEGIN -->
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
<!-- NextGEN Facebook Open Graph (NGFB) meta tags END -->
`

= Q. Why doesn't Facebook show my (current) Open Graph image? =

**A.** The first time Facebook accesses your webpage, it will cache the image and text it finds. Facebook then prefers to use the cached information until it has expired. So, before you hit the send / share button for the first time, make sure you've chosen your *featured* image and (optionally) entered an excerpt text. If you change your mind, and your webpage has not been liked or shared yet, then try using [Facebook's Open Graph debugging tool](https://developers.facebook.com/tools/debug) to refresh the Facebook cache. If your webpage has already been liked or shared on Facebook, then there's nothing you can do to change the image, text, or title that was used.

= Q. How can I see what Facebook sees? =

**A.** Facebook has an [Open Graph debugging tool](https://developers.facebook.com/tools/debug) where you can enter a URL and view a report of it's findings. Try it with your posts, pages, archive pages, author pages, search results, etc. to see how NGFB presents your content. If there are Open Graph warnings, read them carefully -- usually they explain that the information they *already have* for this webpage is in conflict with the Open Graph information now being presented. This might be just the published and modified times, or (if the webpage has already been liked or shared) the title and image Facebook has saved previously.

= Q. What about Google Search and Google Plus? =

**A.** Google reads the Open Graph meta tags as well, along with other "structured data markup" on your webpage. You can see what Google picks up from your webpages by using it's [Rich Snippets Testing Tool](http://www.google.com/webmasters/tools/richsnippets). Use the "Author Link URL" and "Publisher Link URL" options on the NGFB settings page to have Google associate author profiles with your search results.

= Q. Does LinkedIn read the Open Graph tags? =

**A.** According to LinkedIn's [Setting Display Tags for Shares](https://developer.linkedin.com/documents/setting-display-tags-shares) information page, they use three of the Open Graph tags (title, description, and url).

= Q. The W3C Markup Validation Service says "there is no attribute '<em>property</em>'". =

**A.** The Facebook / Open Graph meta *property* attribute is not part of the HTML5 standard, so the [W3C Markup Validator](http://validator.w3.org/) is correct in throwing up an error. In practice though, this incorrect attribute is completely harmless -- social sites (Facebook, Google+, etc.) look for it and don't care if it's part of the standard or not. If you want to address the W3C validator error, you'll have to change the DOCTYPE of your website to XHTML+RDFa (an example follows). The DOCTYPE definition is usually located in the header.php file of your theme.

`
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
`
= Q. Why does the Facebook "Like" button flyout get clipped? =

**A.** This is a known issue with the JavaScript code Facebook uses. If the "Like" button is placed near the edge of an HTML element with the overflow property set to hidden, the flyout may be clipped or completely hidden when the button is clicked. This can be remedied by setting the overflow property to a value other than hidden, such as visible, scroll, or auto. For example:

<pre>
#page { overflow:visible; }
</pre>

There is also a known issue with Facebook's "Like" button flyout and the WP Twenty Eleven and Twenty Twelve based themes. Including the following CSS in your stylesheet should fix the problem:

`
.ngfb-buttons iframe { max-width:none; }
`

= Q. Why are there duplicate Facebook / Google fields on the user profile page? =

**A.** NextGEN Facebook Open Graph (NGFB) adds a "Facebook URL" and "Google URL" field to the profile page. If you already have another plugin that adds these fields to the profile page (under different names), you can tell NGFB to use these other field names instead. You can also remove or change the description of these additional fields (changing "Google URL" to "Google Link" for example). See the "Rename or Add Profile URL Fields" section in the [Other Notes](http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/) tab for additional information.

= Q. How does NGFB find images to include in the Open Graph meta tags? =

**A.** The images used in the Open Graph meta property tags for Posts and Pages are chosen in this sequence:

1. A *featured* or *attached* image from a NextGEN Gallery or WordPress Media Library.
1. Images from NextGEN Gallery `[singlepic]`, `[nggallery]` or `[nggtags]` shortcodes.
1. Images from `<img/>` HTML tags in the Post or Page content text.
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

= Q. Does NextGEN Facebook Open Graph use functions from other plugins? =

**A.** Yes, NGFB can detect and use the following plugins:

* [WP-WikiBox](http://wordpress.org/extend/plugins/wp-wikibox/) : If the WP-WikiBox plugin is active, an option will be added to the settings page to use WP-WikiBox for the Open Graph description field (for pages, not posts).

* [Exclude Pages](http://wordpress.org/extend/plugins/exclude-pages/) : If the Exclude Pages plugin is active, social buttons will not be added to excluded pages. An additional option will be available on the settings page to toggle this default behavior on/off.

* [CDN Linker](https://github.com/wmark/CDN-Linker/downloads) : If the CDN Linker plugin is active, the *featured* image URL will be rewritten by CDN Linker before it's encoded into the sharing URLs for Pinterest and Tumblr.

== Shortcodes ==

You can add one or more social sharing buttons to your content by using the `[ngfb]` shortcode. For example:

`
[ngfb buttons="facebook, gplus, linkedin, pinterest, stumbleupon, tumblr, twitter"]
`

Note: **The "Enable Shortcode" option must be enabled on the NGFB settings page**, and like all other methods used to add NGFB social buttons (enabled from the settings page, widget, etc.), the **Pinterest button will only show on posts with a *featured* or *attached* image**.

== Stylesheets ==

= Social Buttons Style =

NextGEN Facebook Open Graph (NGFB) uses the "ngfb-buttons" class name to wrap all social buttons, and each button has it's own individual class name as well. NGFB does not come with it's own CSS stylesheet -- you must add CSS styling information to your theme's pre-existing stylesheet or use a plugin like <a href="http://wordpress.org/extend/plugins/lazyest-stylesheet/">Lazyest Stylesheet</a> (for example) to create an additional stylesheet. 

Here's a fairly complete example of CSS styling for the NGFB social buttons. Note that I've specified the width (and height) for each button's `<div>`. This takes a little more work to get right, but *pre-defining the height and width of each button area helps the page rendering speed significantly*. The `.ngfb-buttons` class is included within one of three other classes; `.ngfb-content-buttons` for buttons enabled on the NGFB settings page, `.ngfb-widget-buttons` for buttons enabled from the NGFB widget, and `.ngfb-shortcode-buttons` for buttons added in the content using the `[ngfb]` shortcode.

<pre>
/* make sure the Facebook flyout does not get clipped */
#page { overflow:visible; }

/* buttons in content: create a shadowed box for the buttons */
.ngfb-content-buttons { 
	display:block;
	padding:2px;
	margin:20px auto 20px auto;
	text-align:center;
	background-color:#eee;
	-moz-box-shadow:0 0 5px #aaa;
	-webkit-box-shadow:0 0 5px #aaa;
	box-shadow:0 0 5px #aaa;
}

/* buttons in widget: align vertically in 150px columns */
.ngfb-widget-buttons { text-align:left; }
.ngfb-widget-buttons .ngfb-buttons > div { width:150px; }

/* buttons in shortcode: display inline with the text */
.ngfb-shortcode-buttons { 
	display:inline-block;
	text-align:left;
}

/* defaults for the block of buttons */
.ngfb-buttons { }
.ngfb-buttons img { border:none; }
.ngfb-buttons img:hover { border:none; }

/* fix the FB "Like" flyout width on 2012 based themes */
.ngfb-buttons iframe { max-width:none; }

/* defaults for each button */
.ngfb-buttons > div { 
	display:inline-block;
	vertical-align:bottom;
	text-align:left;
	width:100px;	/* default */
	height:20px;
	padding:0;
	margin:2px;
}

div.facebook-button { width:149px; }
div.gplus-button { width:75px; }
div.twitter-button { width:89px; }
div.linkedin-button { width:109px; }
div.pinterest-button { width:80px; }
div.stumbleupon-button { width:84px; }
div.tumblr-button { width:80px; }
</pre>

You can also create a subtle "highlighting" effect using the <em>opacity</em> CSS property.

<pre>
.ngfb-buttons > div { opacity:0.9; }
.ngfb-buttons > div:hover { opacity:1.0; }
</pre>

= Hide Social Buttons =

You can also hide the social buttons (or pretty much any object) in a webpage or post by using `display:none` in your stylesheet. As an example, if you use the "Inspect Element" feature of Firefox (right-click on the object to inspect) -- or use "View Source" to see the webpage's HTML -- you should find your content wrapped in a `<div>` HTML tag similar to this one:

`
<div class="post-123 post type-post status-publish format-standard hentry category-test category-wordpress tag-css tag-html" id="post-123">
	The Post Content Text...
</div>
`

You could use any of these class names to hide one or more NGFB social buttons enabled on the settings page. For example, the following stylesheet hides the social buttons on post #123, any page in category "test", and posts using the Aside and Status formats:

`
.post-123 .ngfb-content-buttons,
.category-test .ngfb-content-buttons,
.format-aside .ngfb-content-buttons,
.format-status .ngfb-content-buttons { display:none; }
`

== Performance Tuning ==

The code for NGFB is highly optimized -- the plugin will not load or execute code it does not have to. You may consider the following option settings to fine-tune the plugin for optimal performance.

* If your website content does not have any embedded videos, or you prefer not to include information on embedded videos in your Open Graph meta property tags, you can set the "Maximum Number of Videos" to "0". This will prevent the plugin from searching your content text for embedded videos.

* If you generally have a *featured* image for your posts and pages, you may set the "Maximum Number of Images" to "1". This will prevent the plugin from searching your content for additional images (the *featured* image counts as "1" and the plugin will stop there).

* For posts and pages, if no excerpt text has been entered, the content text is used to define the Open Graph description meta property value. If you generally don't use excerpts, and your content does not rely on shortcodes or plugins to render its text, you may uncheck the "Apply Content Filters" option.

* If you don't use the `[ngfb]` shortcode, you can uncheck the "Enable Shortcode" option (the default is unchecked).

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
	$url = $_SERVER['HTTPS'] ? 'https://' : 'http://';
	$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	echo ngfb_get_social_buttons( array( 'pinterest', 'tumblr'),
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

= PHP Constants =

**To address very specific needs**, the following PHP constants may be defined in your `wp-config.php` or template files (generally before the `wp_head()` function call).

* `NGFB_DEBUG` : Set this constant to `true` to turn on hidden debug messages, and use "View Source" on any webpage to view the debug messages. An informational message box will also be displayed in admin pages as reminder that debug mode is on.

* `NGFB_RESET` : Set this contant to `true` to reset all options to their defaults *when the plugin is activated*.

* `NGFB_OPEN_GRAPH_DISABLE` : Set this contant to `true` to prevent the plugin from adding Open Graph meta tags in the webpage head section. See "Disable Open Graph Meta Tags" above for an example of it's use.

* `NGFB_MIN_IMG_SIZE_DISABLE` : Set this contant to `true` to disable the minimum width and height checks for the `<img/>` attributes in the content. All images, no matter their size, will be added to the Open Graph meta tags. See "Why does NGFB ignore the &lt;img/&gt; HTML tags in my content?" on the FAQ page for additional information.

* `NGFB_OPTIONS_NAME` : The options field name in the database for NGFB. The default value is `ngfb_options`.

* `NGFB_HEAD_PRIORITY` : Change the execution priority for the `add_header()` method, which adds JavaScript to the head section. The default value is 10.

* `NGFB_OG_PRIORITY`: Change the execution priority for the `add_open_graph()` method, which adds Open Graph meta tags to the head section. The default value is 20.

* `NGFB_CONTENT_PRIORITY` : Change the execution priority for the `add_content()` method, which adds social buttons to the content. The default value is 20.

* `NGFB_FOOTER_PRIORITY` : Change the execution priority for the `add_footer()` method, which adds JavaScript to the footer section. The default value is 10.

* `NGFB_MIN_DESC_LEN` : The minimum allowed description length value. The default is 160. A *maximum* description length value is configurable on the settings page, but any value entered bellow `NGFB_MIN_DESC_LEN` will be changed to `NGFB_MIN_DESC_LEN` when saved.

* `NGFB_MIN_IMG_WIDTH` : The minimum image width *suggested* on the settings page. The default value is 200.

* `NGFB_MIN_IMG_HEIGHT` : The minimum image height *suggested* on the settings page. The default value is 200.

* `NGFB_MAX_IMG_OG` : The maximum range shown in the "Maximum Number of Images" drop-down on the settings page. The default value is 20.

* `NGFB_MAX_VID_OG` : The maximum range shown in the "Maximum Number of Videos" drop-down on the settings page. The default value is 20.

* `NGFB_MAX_CACHE` : The maximum range shown in the "Cache Expiry in Hours" drop-down on the settings page. The default value is 24.

* `NGFB_CONTACT_FIELDS` : The field names and labels for the additional user profile fields. The default value is "facebook:Facebook URL,gplus:Google+ URL". See the "Rename or Add Profile URL Fields" section in the readme for additional information.

* `NGFB_USER_AGENT` : Used by the remote content caching feature for social button images and JavaScript. The Google+ JavaScript is different for (what Google considers) invalid user agents. Since crawlers and robots might refresh the cached files, the NGFB_USER_AGENT defines a default user agent string. You may define a NGFB_USER_AGENT constant in your wp-config.php file to change the default NGFB uses.

* `NGFB_PEM_FILE` : When the "Verify SSL Certificates" option is checked, PHP's curl function needs a certificate authority file. Define the NGFB_PEM_FILE constant in your wp-config.php file to change the default location used by NGFB.

== Screenshots ==

1. An Example Settings Page from [Underwater Focus (Underwater Photography by Jean-Sebastien Morisset)](http://underwaterfocus.com/).

== Changelog ==

= Version 4.0.5 =

* Changed the default "Image Size Name" value, used for images in the Open Graph meta tags, from "thumbnail" to "medium".
* Added `ngfbAdmin::check_options()` to validate that the chosen "Image Size Name" is larger than 200x200. If the width or height is less than 200, a notice message will be displayed in the admin section.
* Moved `ngfbPlugin::check_wp_version()` to `ngfbAdmin::check_wp_version()`.
* Moved `ngfbPlugin::show_admin_messages()` to `ngfbAdmin::admin_notices()`.
* Fixed an `$atts['url']` variable typo in the Facebook button method.
* Fixed the `ngfbDebug()` object that was being created after a possible call to `ngfbDebug::show()`.

= Version 4.0.4 =

* Added a few more debug status msgs to the `add_header()` method.
* Renamed `strip_all_tags()` to `cleanup_html_tags()` and added code to remove `<script>` tags as well.

= Version 4.0.3 =

* Added a check for the `mb_decode_numericentity()` function (available since PHP v4.0.6), which is required to decode UTF8 entities. If the function is missing, an error message will be displayed in the admin section.

= Version 4.0.2 =

* Updated the donation options. NGFB has taken many, many months to develop and fine-tune -- please say thank you by donating a few dollars.

= Version 4.0.1 =

* Added the missing `debug.php` library file to the SVN repository.

= Version 4.0 =

Version 4.0 includes many changes to use the WordPress [Object Cache](http://codex.wordpress.org/Class_Reference/WP_Object_Cache) and [Transients API](http://codex.wordpress.org/Transients_API) functions to improve performance. The performance gains will be most noticeable for logged-in users and websites that aren't using full-webpage caching plugins.

* Added the "Object Cache Expiry" option with a default value of 60 seconds.
* Many changes to the NGFB caching class to use "file" (disk based), "wp_cache" (non-persistent), or "transient" (persistent) caches.
* Added *non-persistent* ([WP Object Cache](http://codex.wordpress.org/Class_Reference/WP_Object_Cache)) object caching for rendered (filtered) Post and Page content.
* Added *persistent* ([Transients API](http://codex.wordpress.org/Transients_API)) object caching for the Open Graph meta tags, social buttons widget, shortcodes and content social buttons.
* Added code to fetch the preview image URL for videos using Vimeo's API.
* Fixed the URL used in Open Graph meta tags to keep the query string (minus tracking arguments) for the search results webpage.

= Version 3.6.3 =

* Removed the general "Buttons Language" option and replaced it with three additional language options for Facebook, Google+ and Twitter. Each social button supports a very different set of languages (and language acronyms), so combining them into a single option wasn't very functional.
* Fixed (added) the missing "ngfb-content-buttons" CSS class for social buttons enabled on the settings page.
* Added a "ngfb-shortcode-buttons" CSS class for social buttons included using the `[ngfb]` shortcode.
* Improved the "Social Buttons Style" example in the [Other Notes](http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/) section.

= Version 3.6.2 =

* Fixed YouTube embeded URLs to make them more compatible with Facebook.
* Changed the behavior used to determine the sharing URL for index (non-singular) type webpages, from stripping only tracking query arguments to stripping all query arguments.
* Added the Facebook "Markup Language" option to select between HTML5 or XFBML (defaults to XFBML).

= Version 3.6.1 =

* Fixed JavaScript for enabled widget buttons on index webpages. That section of code still contained a check for `is_singular()`.
* Added "Enable Shortcode" option to enable/disable the `[ngfb]` shortcode filter (default is unchecked).

= Version 3.6 =

* Added an `[ngfb]` shortcode that can be used to add one or more social buttons to content text. For example, `[ngfb buttons="facebook, gplus"]`.
* Added missing Open Graph og:image tags for *attachment* template webpages.
* Added the first *attached* image to the Pinterest and Tumblr buttons (if no *featured* image is present).
* Added the list of *attached* images to the Open Graph og:image tags.
* Added support for index webpages for the NGFB social sharing widget.
* Added support for YouTube videos from the youtube-nocookie.com domain as well.

= Version 3.5.3 =

* Added several debug messages to NGFB's `get_title()` method.
* Added the "Apply Title Filters" option to enable/disable the standard WordPress filters on the title text (default is checked).
* Added a check for defined video images to skip fallback to the default image if possible.
* Added a work-around for a (known) WordPress taxonomy table problem with category parents.

= Version 3.5.2 =

* Added support for `<embed src="">` videos from youtube.
* Changed the "Include Empty og:* Meta Tags" option default from checked to unchecked.
* Improved CSS styling information text on the settings and readme pages.
* Added a "Fallback to Author Index" option to allow/prevent fallback to the author index URL (when an invalid URL is entered in the chosen author's profile field).
* Fixed the "Default Image on Indexes" and "Default Image on Search Results" options to work as intended.
* Fixed the og:type code to determine if a webpage is an 'article' or 'website', and use a default author ID when those options are checked.
* Fixed the Pinterest and Tumblr caption to use the post text when the buttons are added to posts on an index page.
* Fixed Twitter tweet text when the button is used on an index page. Twitter executes JavaScript to define the tweet text, instead of using the Open Graph meta tags, so the tweet text must be defined explicitly for each Twitter button.
* Fixed video URLs with query strings that where not getting stripped of their query data.
* Fixed URL of current pages with query strings. get_permalink() is now used when possible, otherwise the current URL is used.
* Renamed and moved some sections on the NGFB admin page to help separate the Meta and Link options.

= Version 3.5.1 =

* Improved the code to create the WordPress author index URL by using `get_author_posts_url()` instead.
* Added a list of NGFB and WP constants to the debug output, and additional debug messages to the get_content_images_og() function.
* Worked around a NextGEN Gallery "bug" for album shortcodes. NGG sets the 'subalbum' and 'nggShowGallery' GLOBALS when album shortcodes are first filtered, thus breaking subsequent runs through the same shortcode filter.
* Added the "Title Separator" option, which is used to separate values (category parent names, page numbers, etc.) within the Open Graph title string (default is '|').

= Version 3.5 =

* Added reading of correct/accurate width and height information for NGG cached images using PHP's `getimagesize()` function.
* Added the Facebook button "Default Width" option (though I don't really see a use for it).
* Added "Cache Expiry in Hours" option to save social button images and JavaScript to a cache folder, and provide URLs to these cached files instead of the originals. Note: This option is disabled (0 hours) by default. **Caching should only be enabled if your infrastructure can deliver these files faster and more reliably than the original websites**.
* Added the "Verify SSL Certificates" option to verify the peer SSL certificate when fetching cache content by HTTPS (default is unchecked).
* Added the `NGFB_MAX_CACHE`, `NGFB_USER_AGENT`, and `NGFB_PEM_FILE` constants to modify some settings for the content caching feature.
* Changed the "Maximum Description Length" default from 300 to 280 characters.
* Added `$this->ngg_options` variable to read NextGEN Gallery options.
* Changed the `NGFB_CONTENT_PRIORITY` from 20 to 100 for the Crayon plugin (a priority less than 100 breaks it's rendering).
* Slight improvements to the async JavaScript function used to retrieve social button JavaScript files.
* Changed Facebook like/send button from XFBML to HTML5 code.
* Improved the social button CSS stylesheet example in the [Other Notes](http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/) tab for faster rendering speed.

= Version 3.4 =

* Added a test for the existence of `curl_init()` function before shortening URLs for twitter.
* Added the "Add a Meta Description Tag" option (default is checked) to include a description meta tag. The description meta tag value is identical to the Open Graph "og:description" tag.

= Version 3.3 =

* Improved `og:image:width` and `og:image:height` accuracy by getting their values from `wp_get_attachment_image_src()` and `get_ngg_image_src()` instead of relying on the image size name.
* Added the "Language" option for social buttons. Language support varies; Facebook and Google support all languages, and twitter supports a few.
* Added the "Show Zero in Counter" option for the LinkedIn button.
* Added the "JavaScript in" (Header/Footer) option for all buttons.
* Added the "Default Author on Indexes" and "Default Author on Search Results" options.
* Moved the `get_singlepics_og()` method into `get_content_images_og()` to avoid duplicate OG images.
* Moved the `utf8_entity_decode()` function into a class.
* Removed the unused `get_ngg_xmp()` method.

= Version 3.2.1 =

* **Fixed** the `update_options()` method that wasn't adding missing option array keys as it should.
* Added the "Include Empty Open Graph Meta Tags" option (checked by default).
* Allowed the "Maximum Description Length" value to be 0 (the `NGFB_MIN_DESC_LEN` constant must be 0 as well).

= Version 3.2 =

* **Fixed** the social buttons URL when used on index webpages (was linking to index webpage instead of post).
* Improved the `sanitize_options()` method and settings / options handling code.
* Added a check for NextGEN Gallery "ngg-image" stylesheet ids in the content.
* Added extra checks for empty `$post` objects before using them.
* Added a `get_wiki_summary()` method to improve code segmentation.
* Added a `hidden()` method to save options for inactive plugins.
* Moved `load_options()` from `__construct()` to `init_plugin()`.
* Added `load_is_active()` in `init_plugin()` to check (one time) for 3rd party plugins and functions.
* Replaced many `function_exists/method_exists/class_exists()` calls by `$is_active` array value check.
* Renamed the main plugin class from NGFB to ngfbPlugin.
* Added an informational message on the admin pages when missing options are added.

= Version 3.1.1 =

* **Fixed** a variable reference in the widget.
* Added the (optional) `NGFB_DEBUG` and `NGFB_RESET` constants. Defining `NGFB_RESET` will reset the options to their default values when activating the plugin.
* Added the `NGFB_OPTIONS_NAME`, `NGFB_CLASSNAME`, and `NGFB_FULLNAME`.
* Added an informational message box in the admin pages when options are updated and need to be saved.
* Added an `$opts_version` variable to check if database options need to be updated (as opposed to just using the plugin version string).

= Version 3.1 =

* **Fixed** a small oversight where `apply_content_filter()` was being run on the excerpt by mistake. The fix is in keeping with improving performance as much as possible.
* Added a warning message (in the admin pages) for missing plugin options in the database. This fixes an error where the plugin has been installed and activated, but it's options have disappeared from the database at some point. The plugin will now recognize this condition, generate a warning message, and reset the options to their defaults.
* Added a JavaScript function to load button JavaScript files asynchronously -- all except for Tumblr, which must be loaded from the footer. :-p This should further help to improve page load speeds - always an important consideration.
* Added a `ngfbGoogl()` class (from https://github.com/sebi/googl-php) to shorten URLs for Twitter.
* Added the "Apply Excerpt Filter" option (default is unchecked).
* Added the "Shorten URLs" option for Twitter (checked by default) and the (optional) "Goo.gl API Key" field.
* Improved the output from the debug print-out function.

= Version 3.0 =

* Major code revision finished - all functions have been moved to object-oriented classes. NGFB is currently 2,350 lines of code in 4 classes and 77 functions.
* NextGEN Facebook Open Graph now finds and uses all images from the content to include in the Open Graph meta tags.
* Added the ngfbButtons class in lib/buttons.php.
* Added a version string in the database options to skip option updates if the database options are current.
* Added the "Maximum Number of Images" and "Maximum Number of Videos" options to limit the number of images and videos listed in the Open Graph meta tags.
* Added the `NGFB_MAX_IMG_OG` and `NGFB_MAX_VID_OG` constants to define the maximum range in the option selects.
* Added the `NGFB_CONTACT_FIELDS` constant to define the profile field names for Facebook and Google+ URLs.
* Added the "Add Page Ancestor Tags" and "Add Page Titles as Tags" options.
* Added a Head Link Settings section on the settings page with the "Author Link URL" and "Publisher Link URL" options.
* Added a "Performance Tuning" section in the readme.txt.
* Added a Donate button on the options page. Please show your appreciation for NGFB by donating a few dollars. Thank you.
* Improved debugging output with a debug function and dump of the OG array.

= Version 2.4.1 =

* Minor update to fix the "0 is a protected WP option and may not be modified" error.

= Version 2.4 =

* Improved the admin page code by moving all select and checkbox options to functions. The drop-down selects and checkboxes now show their default values.
* Moved the `$options` variable related functions into the `NGFB` class.
* Added the "Author URL" option to allow for the Website value in the profile, and the GPAISR plugin for Google+.
* Added the "Default Author" option to add an author meta tag on indexes without content.
* Added the "Ignore Small Images in Content" to disable the default behavior of NGFB to ignore smaller images in the content.

= Version 2.3.1 =

* **Fixed** variable name to have apply_filters('the_content') applied to the OG description as it should.
* Added `apply_filters('the_excerpt')` on the OG description when text is from excerpt.
* Added `apply_filters('the_title')` on the OG title.
* Added the `ngfb_linkedin_footer()` function to move the LinkedIn JavaScript to the footer.
* Sanitized the "Facebook Admin(s)" option by stipping off any leading URLs (leaving just the account names).
* Temporarily removed NGFB as a filter to the_content when using `apply_filters('the_content')` to prevent recursion.
* Added `NGFB_HEAD_PRIORITY`, `NGFB_CONTENT_PRIORITY`, and `NGFB_FOOTER_PRIORITY` constants.

= Version 2.3 =

* Renamed `DISABLE_NGFB_OPEN_GRAPH_DISABLE` constant to `NGFB_OPEN_GRAPH_DISABLE` (though both are allowed).
* Added the `NGFB_MIN_IMG_SIZE_DISABLE` constant to disable minimum width and height checks for `<img/>` src attributes.
* Added the StumbleUpon social sharing button.
* Added a "Preferred Order" option to control the order in which buttons appear.
* Moved the JavaScript used by all buttons into the footer section (filter on `wp_footer()` function) to improve page rendering speed.
* Moved the admin settings page code into plugins/nextgen-facebook/lib/admin.php.
* Moved the widget code into plugins/nextgen-facebook/lib/widgets.php.
* Added the `NGFB` class and started moving functions into it. 

= Version 2.2 =

* Added `ngfb_get_options()` function to validate and upgrade options without having to visit the options page.
* Enhanced the code where the plugin looks for an image in the content: relative URLs will be completed, images smaller than the 'Image Size Name' defined on the options page will be ignored, and a "share" attribute in the `<img/>` tag will take precedence over the "src" attribute.
* Added the "Filter Content for Meta Tags" option (checked by default). When NGFB generates the Open Graph meta tags, it applies Wordpress filters on the content to expand shortcodes etc. In most cases this is fine, even desirable, but in a few rare cases, it may break another plugin. You can prevent NGFB from applying the Wordpress filters by un-checking this option. If you do, NGFB may not have access to the complete content text (if your content includes some shortcodes, for example), and may generate inaccurate Open Graph description or image meta tags.

= Version 2.1.3 =

* Added apply_filters('the_content') before searching for an `<img/>` in the content.

= Version 2.1.2 =

* Changed the priority of `ngfb_add_meta_tags()` from 10 (the default) to 20, so other plugins might run before NGFB and render additional content.
* Added a `ngfb_get_meta_tag()` function to sanitize and encode all Open Graph meta tag values.
* **Fixed** the 'Content Begins at First Paragraph' option to make the regex "un-greedy" and work as intended. ;-)

= Version 2.1.1 =

* Optimized code by adding `ngfb_get_size_values()` to return size info based on image size name.
* Renamed the `cdn_linker()` function to `ngfb_cdn_linker()`.
* Added a "Stylesheet" and "Advanced Usage" section in the readme.

= Version 2.1 =

* Added an option for Google+ to select either the "G +1" or "G+ Share" button.
* Added sharing of WordPress "quote" format posts to Tumblr. 
* Added the Pinterest sharing button for posts and pages with *featured* images.
* Added a check for the "Exclude Pages" plugin in the widget section.
* Added a call to CDN Linker (if it's installed) for image URLs shared to Tumblr and Pinterest.
* Added a check for the `DISABLE_NGFB_OPEN_GRAPH` constant before adding Open Graph meta tags.
* Added a 'Max Title Length' setting (default is 100 characters).

= Version 2.0 =

* The NextGEN Facebook Open Graph options page has been re-worked to make it more compact.
* Added the Tumblr social sharing button, including support for posting *featured* images, embedded video, or links to posts and pages.
* Added a `ngfb_get_social_buttons()` function to use in your theme templates. See the FAQ for additional information on it's use.
* Added an optional "NGFB Social Buttons" widget to include social buttons in any post or page widget area.

You can enable social buttons in the content, use the social buttons widget, and call the `ngfb_get_social_buttons()` function from your template(s) -- all at the same time -- but all social buttons share the same settings from the admin options page (the layout of each can differ by using the available CSS class names - see the [Other Notes](http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/) tab for additional information).

= Version 1.7.2 =

* **Fixed** the missing "data-annotation" field to the Google+ social button.
* **Fixed** `</p>` to a space before stripping out all html tags from og:description.

= Version 1.7.1 =

* Changed the plugin name from "NextGEN Facebook" to "NextGEN Facebook OG" to better describe it's function (adding Open Graph meta tags).

= Version 1.7 =

* Added LinkedIn social button options.
* Added a setting to include hidden debug info above the Open Graph tags.
* If the Exclude Pages plugin is installed, a new option will be available on the settings page to turn on/off social buttons on excluded pages (by default, social buttons are not added to excluded pages).
* Added the og:video meta tags (including width, height, type, etc.) for youtube iframe embedded videos.
* Cleaned-up some PHP code to consolidate the OG variables within a single array.

= Version 1.6.1 =

* **Fixed** a bug where some checked options -- those that should be ON by default -- would always stay checked.
* Stripped JavaScript from the_content text so it doesn't make it to the og:description meta tag.

= Version 1.6 =

* Added the Google+ and Twitter button options.
* Added the "Open Graph HTML Meta Tags" options to exclude one or more Facebook and Open Graph HTML meta tags from the webpage headers.

= Version 1.5.1 =

* Added the "Default Image on Search Page" option.
* Added the "WP-WikiBox Tag Prefix" option to identify the WordPress tag names used to retrieve Wikipedia content.
* The two WP-WikiBox options ("Use WP-WikiBox for Pages" and "WP-WikiBox Tag Prefix") will not appear on the settings page unless the WP-WikiBox plugin is installed and activated.
* Updated the readme's Description and FAQ sections with more information on Open Graph and it's use by Google and LinkedIn.

= Version 1.5 =

* Added the "Add NextGEN Gallery Tags" option to include the *featured* (or default) image tags from the NextGEN Gallery.
* Added the "Content Begins at a Paragraph" option to ignore all text before the first `<p>` paragraph in the content (not the excerpt). This might be useful for photography oriented websites that have thumbnails, captions, etc. preceeding their content.
* Added the "Use WP-WikiBox for Pages" option. This is an option very few, if any, besides myself will find useful. If you use the WP-WikiBox plugin's function in your theme's templates (and not the shortcode), then you can use the page's tags or title to retrieve content from Wikipedia. This only aplies to pages, not posts. Here's how it works; the plugin will check for the page's tags, and use their names to retrieve content from Wikipedia. If no tags are defined, then the page title will be used. If Wikipedia does not return a summary for your tags or title, then the content of your page will be used.

= Version 1.4.1 =

* Small fixes to the article:tag and article:author after finding additional information on these meta tags.
* Changed article:tag by including it once for every WordPress tag, instead only once with an array of values.
* Changed article:author to a URL instead of the author's name.

= Version 1.4 =

* Added a website topic setting to choose a value for the article:section meta tag. If defined, the article:section meta tag will be added to all posts and pages. You can leave it blank, if for example, your website covers a wide variety of subjects.
* When viewing a tag webpage, added the tag description (if there is one) to the Open Graph description meta tag.
* When viewing a author webpage, added the author biographical info (if there is one) to the Open Graph description meta tag.

= Version 1.3 =

* Moved some code out of the meta HTML section to make it cleaner. There's no functional difference, it just looks nicer. ;-)
* When viewing a category webpage, added the category description (if there is one) to the Open Graph description meta tag.
* When viewing a category webpage, added the category parents (if any) to the Open Graph title meta tag.
* Added the following meta tags: article:published_time, article:modified_time, article:author, article:tag.

= Version 1.2 =

* Added the Default Image ID option, in addition to the existing Default Image URL option. Since the plugin can find images by ID number, might as well use it for the default image as well. :)

= Version 1.1 =

* Improved the description and installation texts.
* Used a single screenshot image of the settings page, instead of two.
* Removed the "NextGEN Gallery Image Size" setting - it was a bit redundant. Instead, I've suggested using an existing Size Name from the Media Library, and if necessary, to create an additional Size Name specifically for NextGEN Facebook. Since NextGEN Gallery doesn't understand these Size Names, I've added some code to get the width, height, and crop from the Size Name, before calling the necessary NextGEN Gallery functions.
* Added a "Use Default on Multi-Entry Pages" checkbox to force the default image to be used on the homepage, category page, author page, etc. (instead of the *featured* image from the first post, for example).
* Added extra parsing for author pages, tag pages, category pages, etc., to refine the og:description text.
* Also improved the og:title text for archive pages, category pages, etc. 

= Version 1.0 =

* Initial release.

== Upgrade Notice ==

= Version 4.0.5 =

Fixed a variable typo in the Facebook method, changed the "Image Size Name" option default value to "medium", added a warning for image sizes that are too small (less than 200x200).

= Version 4.0.4 =

Added code to remove `<script>` tags from content text for descriptions, etc.

= Version 4.0.3 =

Added a check for the `mb_decode_numericentity()` function (available since PHP v4.0.6), which is required to decode UTF8 entities.

= Version 4.0.2 =

Updated the donation options. NGFB has taken many, many months to develop and fine-tune -- please say thank you by donating a few dollars.

= Version 4.0.1 =

Added the missing `debug.php` library file to the SVN repository.

= Version 4.0 =

Added persistent and non-persistent caching code to improve speed (especially for logged-in users), added support for video preview images from Vimeo.

= Version 3.6.3 =

Added additional social button language options, fixed the missing "ngfb-content-buttons" CSS class, and added the "ngfb-shortcode-buttons" CSS class.

= Version 3.6.2 =

Improved the sharing URL for index webpages and YouTube embeded videos.

= Version 3.6.1 =

Fixed JavaScript for enabled widget buttons on index webpages and added "Enable Shortcode" option.

= Version 3.6 =

Added an `[ngfb]` shortcode to add social buttons to content text, better support for *attached* images, and improved widget code for index webpages.

= Version 3.5.3 =

A few minor fixes for special / infrequent situations. Upgrade at the earliest opportunity if you're not running at least v3.5.2.

= Version 3.5.2 =

Many small but important fixes (see the Changelog for the complete list). Please update NGFB at your earliest convenience.

= Version 3.5.1 =

Improved code to create WP author index URL, added list of NGFB and WP constants to debug output, bypassed NextGEN Gallery shortcode bug, added "Title Separator" option.

= Version 3.5 =

Added reading of accurate width and height for NGG cached images, a caching feature for social button images and JavaScript (disabled by default), Facebook button changed from XFBML to HTML5.

= Version 3.4 =

Added the "Add a Meta Description Tag" option (default is checked) and a test for the existence of `curl_init()` function before shortening URLs for twitter.

= Version 3.3 =

Improved `og:image:width` and `og:image:height` accuracy. Added Language support for button text. Configurable location for each button JavaScript (header or footer). Additional default author options.

= Version 3.2.1 =

**Fixed** `update_options()` method that wasn't adding missing options as it should. Added the "Include Empty Open Graph Meta Tags" option.

= Version 3.2 =

**Fixed** social button links on index webpages, improved the sanitation and options handling code, added a check for NextGEN Gallery image IDs in the content.

= Version 3.1.1 =

**Fixed** variable reference in widget. Added informational box when upgrading options.

= Version 3.1 =

Added JavaScript function to load button JavaScript files asynchronously. Added goo.gl URL shortener for Twitter. Added warning message for missing options in database.

= Version 3.0 =

Major revision and several new features. List several images/videos and add Page ancestor tags in the OG meta tags. Head Link options for Google Search. "Performance Tuning" section in [Other Notes](http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/).

= Version 2.4 =

Added the "Author URL", "Default Author", and "Ignore Small Images in Content" options. Continued code optimization/overhaul. Please report any issues to the NGFB support page.

= Version 2.3.1 =

**Fixed** variable name when using applying 'the_content' filter on OG description. Prevented recursion when calling `apply_filters()` function on 'the_content'.

= Version 2.3 =

Added StumbleUpon button, `NGFB_MIN_IMG_SIZE_DISABLE` constant, moved some functions into classes and library files, added "Preferred Order" for buttons, move button JavaScript to footer.

= Version 2.2 =

Improved validation of option values, enhanced code where plugin looks for an image in the content, and added new "Filter Content for Meta Tags" option.

= Version 2.1.3 =

Added `apply_filters()` function before searching for an `<img/>` in the content.

= Version 2.1.2 =

Added sanitation and HTML entity encoding of all Open Graph meta tag values.

= Version 2.1.1 =

Minor code optimization and improved readme file.

= Version 2.1 =

Added Pinterest button, 'Max Title Length' option, and `DISABLE_NGFB_OPEN_GRAPH` constant for templates.

= Version 2.0 =

More compact options page, added Tumblr button, social buttons widget, and `ngfb_get_social_buttons()` function for templates.

= Version 1.7.2 =

Added missing data-annotation field to Google+ social button.

= Version 1.7.1 =

Changed plugin name to NextGEN Facebook OG.

= Version 1.7 =

Added LinkedIn button and og:video Open Graph meta tag.

= Version 1.6.1 =

**Fixed** some checked option boxes that could not be unchecked.

= Version 1.4.1 =

**Fixed** article:tag and article:author Open Graph meta tags.

