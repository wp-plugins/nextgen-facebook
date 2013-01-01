=== NextGEN Facebook OG ===
Contributors: jsmoriss
Tags: nextgen, featured, open graph, meta, buttons, like, send, share, image, wp-wikibox, wikipedia, facebook, google, google plus, g+, twitter, linkedin, social, seo, search engine optimization, exclude pages, pinterest, tumblr, widget, cdn linker
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 2.2
License: GPLv2 or later

Adds Open Graph meta tags for Facebook, G+, LinkedIn, etc., plus sharing buttons for FB, G+, Twitter, LinkedIn, Pinterest, tumblr.

== Description ==

The NextGEN Facebook OG plugin adds <a href="http://ogp.me/" target="_blank">Open Graph</a> meta tags to all webpage headers, including the "artical" object type for posts and pages. The featured image thumbnails, from a <a href="http://wordpress.org/extend/plugins/nextgen-gallery/" target="_blank">NextGEN Gallery</a> or WordPress Media Library, are also correctly listed in the "image" meta tag. This plugin goes well beyond any other plugins I know in handling various archive-type webpages. It will create appropriate title and description meta tags for category, tag, date based archive (day, month, or year), author webpages and search results. <em>You can also, optionally, add Facebook, Google+, Twitter, LinkedIn, Pinterest and tumblr sharing buttons to post and page content (above or bellow), as a widget, or even use a function from your templates</em>.

The Open Graph protocol enables any web page to become a rich object in a social graph. For instance, this is used on Facebook to allow any web page to have the same functionality as any other object on Facebook. The Open Graph meta tags are read by almost all social websites, including Facebook, Google (Search and Google+), and LinkedIn.

NextGEN Facebook OG was specifically written to support featured images located in NextGEN Galleries, but also works just as well with the WordPress Media Library. <strong>The NextGEN Gallery plugin is not required to use this plugin - all features work just as well without it.</strong> The image used in the Open Graph meta tag is chosen in this sequence; a featured image from a NextGEN Gallery or WordPress Media Library, the first NextGEN [singlepic] or IMG HTML tag in the content, a default image defined in the plugin settings.  If none of these conditions can be satisfied, then the Open Graph image tag will be left empty.

Here's an example of Open Graph meta tags for a post on my website titled "<a href="http://surniaulula.com/2012/12/01/wordpress-caching-and-plugins-for-performance/">Wordpress Caching and Plugins for Performance</a>":

<code>
&lt;!-- NextGEN Facebook OG Meta Tags BEGIN --&gt;
&lt;meta property="article:author" content="http://surniaulula.com/author/jsmoriss/" /&gt;
&lt;meta property="article:modified_time" content="2012-12-03T12:33:18+00:00" /&gt;
&lt;meta property="article:published_time" content="2012-12-01T15:34:56+00:00" /&gt;
&lt;meta property="article:section" content="Technology" /&gt;
&lt;meta property="article:tag" content="apache" /&gt;
&lt;meta property="article:tag" content="apc" /&gt;
&lt;meta property="article:tag" content="bandwidth" /&gt;
&lt;meta property="article:tag" content="cache" /&gt;
&lt;meta property="article:tag" content="caching" /&gt;
&lt;meta property="article:tag" content="cdn" /&gt;
&lt;meta property="article:tag" content="content delivery network" /&gt;
&lt;meta property="article:tag" content="httpd" /&gt;
&lt;meta property="article:tag" content="linux" /&gt;
&lt;meta property="article:tag" content="memcached" /&gt;
&lt;meta property="article:tag" content="opcode" /&gt;
&lt;meta property="article:tag" content="performance" /&gt;
&lt;meta property="article:tag" content="php" /&gt;
&lt;meta property="article:tag" content="plugins" /&gt;
&lt;meta property="article:tag" content="rewrite" /&gt;
&lt;meta property="article:tag" content="static content" /&gt;
&lt;meta property="article:tag" content="wordpress" /&gt;
&lt;meta property="fb:app_id" content="525239184171769" /&gt;
&lt;meta property="og:description" content="Over the past few weeks I&#039;ve been looking at different solutions to improve the speed of my Wordpress websites. The first step was to mirror and redirect the static content to another server (aka Content Delivery Network or CDN). In the case of PHP and Wordpress, there are several additional" /&gt;
&lt;meta property="og:image" content="http://cdn1.static.surniaulula.com/wp-content/gallery/cache/5_crop_200x200_20120814-114043-sbellive-0078.jpg" /&gt;
&lt;meta property="og:site_name" content="Surnia Ulula" /&gt;
&lt;meta property="og:title" content="Wordpress Caching and Plugins for Performance" /&gt;
&lt;meta property="og:type" content="article" /&gt;
&lt;meta property="og:url" content="http://surniaulula.com/2012/12/01/wordpress-caching-and-plugins-for-performance/" /&gt;
&lt;!-- NextGEN Facebook OG Meta Tags END --&gt;
</code>

NextGEN Facebook OG is being actively developed and supported. You can review the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/">FAQ Page</a> for additional setup notes, and if you have questions or suggestions, post them on the NextGEN Facebook OG <a href="http://wordpress.org/support/plugin/nextgen-facebook" target="_blank">Support Page</a>. Your comment or suggestion will be answered in a timely manner.

== Installation ==

*Using the WordPress Dashboard*

1. Login to your weblog
1. Go to Plugins
1. Select Add New
1. Search for *NextGEN Facebook OG*
1. Select Install
1. Select Install Now
1. Select Activate Plugin

*Manual*

1. Download and unzip the plugin
1. Upload the entire nextgen-facebook/ folder to the /wp-content/plugins/ directory
1. Activate the plugin through the Plugins menu in WordPress

Once activated, you don't have to configure any settings for NextGEN Facebook OG to automatically start adding Open Graph meta tags to your pages.

Some plugin options are available under the "Settings -&gt; NextGEN Facebook" admin menu, to select a default image, include social buttons at the end of posts and pages, change the shared thumbnail image size, etc.

== Frequently Asked Questions ==

= Q. Why doesn't Facebook show my (current) Open Graph image? =

**A.** The first time Facebook accesses your webpage, it will cache the image and text it finds. Facebook then prefers to use that cached information until it has expired. So, before you hit the send / share button for the first time, make sure you've chosen your featured image and (optionally) entered an excerpt text. If you change your mind, and your webpage has not been liked or shared yet, then try using <a href="https://developers.facebook.com/tools/debug" target="_blank">Facebook's Open Graph debugging tool</a>. If your webpage has already been liked or shared on Facebook, then there's nothing you can do to change the image, text, or title that was used.

= Q. How can I see what Facebook sees? =

**A.** Facebook has an <a href="https://developers.facebook.com/tools/debug" target="_blank">Open Graph debugging tool</a> where you can enter a URL and view a report of it's findings. Try it with your posts, pages, archive pages, author pages, search results, etc. to see how NextGEN Facebook OG presents your content.

If there are Open Graph Warnings, read them carefully -- usually they explain that the information they *already have* for this webpage is in conflict with the Open Graph information now being presented. This might be just the published and modified times, or (if the webpage has already been liked or shared) the title and image Facebook has saved previously.

= Q. What about Google Search and Google Plus? =

**A.** Google reads the Open Graph meta tags as well, along with other "structured data markup" on your webpage. You can see what Google picks up from your webpages by using it's <a href="http://www.google.com/webmasters/tools/richsnippets" target="_blank">Rich Snippets Testing Tool</a>. You may also want to link your WordPress authors with their Google+ profiles by using one of the available plugins, like <a href="http://wordpress.org/extend/plugins/google-author-information-in-search-results-wordpress-plugin/" target="_blank">Google Plus Author Information in Search Result (GPAISR)</a> or others like it.

= Q. Does LinkedIn read the Open Graph tags? =

**A.** According to LinkedIn's <a href="https://developer.linkedin.com/documents/setting-display-tags-shares" target="_blank">Setting Display Tags for Shares</a> information page, they use three of the Open Graph tags (title, description, and url).

= Q. The <a href="http://validator.w3.org/">W3C Markup Validation Service</a> says 'there is no attribute "property"'. =

**A.** The Facebook / Open Graph &lt;meta property="" /&gt; attribute is not part of the HTML5 standard, so the W3C validator is correct in throwing up an error. In practice though, this incorrect attribute is completely harmless -- social sites (Facebook, Google+, etc.) look for it and don't care if it's part of the standard or not.

If you want to address the W3C validator error, you'll have to change the DOCTYPE of your website to XHTML+RDFa (an example follows). The DOCTYPE definition is usually located in the header.php file of your theme.

<code>
&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"&gt;
</code>

= Q. Does NextGEN Facebook OG use functions from other plugins? =

**A.** Yes, NextGEN Facebook OG can detect and use the following plugins:

<ul>
<li><a href="http://wordpress.org/extend/plugins/wp-wikibox/" target="_blank">WP-WikiBox</a> -- If the WP-WikiBox plugin is active, an option will be added to the settings page to use WP-WikiBox for the Open Graph description field (for pages, not posts).</li>

<li><a href="http://wordpress.org/extend/plugins/exclude-pages/" target="_blank">Exclude Pages</a> -- If the Exclude Pages plugin is active, social buttons will not be added to excluded pages. An additional option will be available on the settings page to toggle this default behavior on/off.</li>

<li><a href="https://github.com/wmark/CDN-Linker/downloads" target="_blank">CDN Linker</a> -- If the CDN Linker plugin is active, the featured image URL will be rewritten by CDN Linker before it's encoded into the sharing URL for Pinterest and tumblr.</li>
</ul>

= Q. Why does NextGEN Facebook OG ignore the IMG HTML tag in my content? =

**A.**  The image used in the Open Graph meta tag is chosen in this sequence; a featured image from a NextGEN Gallery or WordPress Media Library, the first NextGEN [singlepic] or IMG HTML tag in the content, a default image defined in the plugin settings. If the IMG HTML tag is being ignored, it's probably because the <strong>image "width" and "height" attribute values are smaller than the 'Image Size Name' you've chosen on the settings page</strong>. NextGEN Facebook OG will only share an image equal or larger than the 'Image Size Name' you've chosen.

If you want to display smaller IMG thumbnails in your content (on index webpages, for example), but also want NextGEN Facebook OG to detect and use a larger version of the first thumbnail it finds, then you can add a "share" attribute with a URL to the larger image. For example:

<code>
&lt;img
    share="http://underwaterfocus.com/wp-content/gallery/cache/40_crop_200x200_20080514-152313-mevallee-2951.jpg"
    src="http://underwaterfocus.com/wp-content/gallery/2008-05-bonaire-na/thumbs/thumbs_20080514-152313-mevallee-2951.jpg"
    width="150" height="150" /&gt;
</code>

Note: The order in which the attributes are listed is important -- place the "share" attribute first to give it higher priority.

== Screenshots ==

1. NextGEN Facebook OG - The Settings Page

== Changelog ==

= Version 2.2 =
* Added ngfb_get_options() function to validate and upgrade options without having to visit the options page.
* Enhanced the code where the plugin looks for an image in the content: relative URLs will be completed, images smaller than the 'Image Size Name' defined on the options page will be ignored, and a "share" attribute in the &lt;img&gt; tag will take precedence over the "src" attribute.
* Added the "Filter Content for Meta Tags" option (checked by default). When NextGEN Facebook OG generates the Open Graph meta tags, it applies Wordpress filters on the content to expand shortcodes etc. In most cases this is fine, even desirable, but in a few rare cases, it may break another plugin. You can prevent NextGEN Facebook OG from applying the Wordpress filters by un-checking this option. If you do, NextGEN Facebook OG may not have access to the complete content text (if your content includes some shortcodes, for example), and may generate inaccurate Open Graph description or image meta tags.

= Version 2.1.3 =
* Added apply_filters('the_content') before searching for an &lt;img&gt; in the content.

= Version 2.1.2 =
* Changed the priority of ngfb_add_meta_tags() from 10 (the default) to 20, so other plugins might run before NGFB and render additional content.
* Added a ngfb_get_meta_tag() function to sanitize and encode all Open Graph meta tag values.
* Fixed the 'Content Begins at First Paragraph' option to make the regex "un-greedy" and work as intended. ;-)

= Version 2.1.1 =
* Optimized code by adding ngfb_get_size_values() to return size info based on image size name.
* Renamed the cdn_linker() function to ngfb_cdn_linker().
* Added a "Stylesheet" and "Advanced Usage" section in the readme.

= Version 2.1 =
* Added an option for Google+ to select either the "G +1" or "G+ Share" button.
* Added sharing of WordPress "quote" format posts to tumblr. 
* Added the Pinterest sharing button for posts and pages with featured images.
* Added a check for the "Exclude Pages" plugin in the widget section.
* Added a call to CDN Linker (if it's installed) for image URLs shared to tumblr and Pinterest.
* Added a check for the DISABLE_NGFB_OPEN_GRAPH constant before adding Open Graph meta tags.
* Added a 'Max Title Length' setting (default is 100 characters).

= Version 2.0 =
* The NextGEN Facebook OG options page has been re-worked to make it more compact.
* Added the tumblr social sharing button, including support for posting featured images, embeded video, or links to posts and pages.
* Added a ngfb_get_social_buttons() function to use in your theme templates. <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/" target="_blank">See the FAQ</a> for additional information on it's use.
* Added an optional "NGFB Social Buttons" widget to include social buttons in any post or page widget area.

You can enable social buttons in the content, use the social buttons widget, and call the ngfb_get_social_buttons() function from your template(s) -- all at the same time -- but all social buttons share the same settings from the admin options page (the layout of each can differ by using the available CSS class names - <a href="http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/" target="_blank">see the Other Notes tab</a> for additional information).

= Version 1.7.2 =
* Fixed: Added the missing "data-annotation" field to the Google+ social button.
* Fixed: Changed "&lt;/p&gt;" to a space before stripping out all html tags from og:description.

= Version 1.7.1 =
* Changed the plugin name from "NextGEN Facebook" to "NextGEN Facebook OG" to better describe it's function (adding Open Graph meta tags).

= Version 1.7 =
* Added LinkedIn social button options.
* Added a setting to include hidden debug info above the Open Graph tags.
* If the Exclude Pages plugin is installed, a new option will be available on the settings page to turn on/off social buttons on excluded pages (by default, social buttons are not added to excluded pages).
* Added the og:video meta tags (including width, height, type, etc.) for youtube iframe embeded videos.
* Cleaned-up some PHP code to consolidate the OG variables within a single array.

= Version 1.6.1 =
* Fixed a bug where some checked options -- those that should be ON by default -- would always stay checked. Thanks to chrisjborg for reporting this one.
* Stripped javascript from the_content text so it doesn't make it to the og:description meta tag.

= Version 1.6 =
* Added the Google+ and Twitter button options.
* Added the "Open Graph HTML Meta Tags" options to exclude one or more Facebook and Open Graph HTML meta tags from the webpage headers.

= Version 1.5.1 =
* Added the "Default Image on Search Page" option.
* Added the "WP-WikiBox Tag Prefix" option to identify the WordPress tag names used to retrieve Wikipedia content.
* The two WP-WikiBox options ("Use WP-WikiBox for Pages" and "WP-WikiBox Tag Prefix") will not appear on the settings page unless the WP-WikiBox plugin is installed and activated.
* Updated the readme's Description and FAQ sections with more information on Open Graph and it's use by Google and LinkedIn.

= Version 1.5 =
* Added the "Add NextGEN Gallery Tags" option to include the featured (or default) image tags from the NextGEN Gallery.
* Added the "Content Begins at a Paragraph" option to ignore all text before the first &lt;p&gt; paragraph in the content (not the excerpt). This might be useful for photography oriented websites that have thumbnails, captions, etc. preceeding their content.
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
* Added a "Use Default on Multi-Entry Pages" checkbox to force the default image to be used on the homepage, category page, author page, etc. (instead of the featured image from the first post, for example).
* Added extra parsing for author pages, tag pages, category pages, etc., to refine the og:description text.
* Also improved the og:title text for archive pages, category pages, etc. 
* No bugs were reported or fixed from the previous version, which is good news I guess. ;-)

= Version 1.0 =
* Initial release.

== Upgrade Notice ==

= Version 2.2 =
Improved validation of option values, enhanced code where plugin looks for an image in the content, and added new "Filter Content for Meta Tags" option.

= Version 2.1.3 =
Added apply_filters() function before searching for an &lt;img&gt; in the content.

= Version 2.1.2 =
Added sanitation and HTML entity encoding of all Open Graph meta tag values.

= Version 2.1.1 =
Minor code optimization and improved readme file.

= Version 2.1 =
Added Pinterest button, 'Max Title Length' option, and DISABLE_NGFB_OPEN_GRAPH constant for templates.

= Version 2.0 =
More compact options page, added tumblr button, social buttons widget, and ngfb_get_social_buttons() function for templates.

= Version 1.7.2 =
Added missing data-annotation field to Google+ social button.

= Version 1.7.1 =
Changed plugin name to NextGEN Facebook OG.

= Version 1.7 =
Added LinkedIn button and og:video Open Graph meta tag.

= Version 1.6.1 =
Fixed some checked option boxes that could not be unchecked.

= Version 1.4.1 =
Fixed article:tag and article:author Open Graph meta tags.

== Stylesheets ==

= Social Buttons Style =

NextGEN Facebook OG uses the "ngfb-buttons" class name to wrap all social buttons, and each button has it's own individual class name as well. Here's an example of the stylesheet I'ved used on <a href="http://surniaulula.com/" target="_blank">Surnia Ulula</a>:

<code>
.ngfb-buttons { 
	display:block;
	text-align:center; 
	margin:20px 0 20px 0;
}
.ngfb-buttons img {
	padding:0;
	margin:0;
	border:none;
}
.ngfb-buttons img:hover {
	border:none;
}
.facebook-button, .gplus-button, .twitter-button, .linkedin-button, .pinterest-button, tumblr-button {
	display:inline-block;
	vertical-align:bottom;
	height:20px;
	padding:0;
	margin:0;
}
.facebook-button { margin-right:15px; }
.gplus-button { margin-right:-20px; }
.twitter-button { margin-right:-20px; }
.linkedin-button { margin-right:10px; }
.pinterest-button { margin-right:30px; }
.tumblr-button { margin-right:0; }	/* last button on the right */
</code>

The "NGFB Social Buttons" widget adds an extra class name that you can use to create a different layout for the widget. For example, here are different styles for social buttons in a widget and added to the content.

<code>
.nbfg-widget-buttons .ngfb-buttons { 
	display:inline-block;
	text-align:right; 
	margin:5px;
}
.ngfb-content-buttons .ngfb-buttons { 
        padding:5px;
        margin:20px auto 20px auto;
        background-color:#eee;
        box-shadow:0 0 5px #aaa;
}
</code>

= Hide Social Buttons =

You can hide the social buttons, or pretty much any object, in a page or post by using "display:none" in your stylesheet. For example, if you use the "Inspect Element" feature of Firefox (right-click on the object to inspect) -- or use "View Source" to see the page's HTML -- your content should be wrapped in a &lt;div&gt; HTML tag similar to this one:

<code>
&lt;div class="post-123 post type-post status-publish format-standard hentry category-test category-wordpress tag-css tag-html" id="post-123"&gt;
	The Content Text
&lt;/div&gt;
</code>

You could use any of these class names to hide one or more NextGEN Facebook OG social buttons. For example, the following stylesheet hides social buttons for post #123, any page in category "test", and posts using the Aside and Status formats:

<code>
.post-123 .ngfb-buttons,
.category-test .ngfb-buttons,
.format-aside .ngfb-buttons,
.format-status .ngfb-buttons { 
	display:none;
}
</code>

== Advanced Usage ==

= Include Social Buttons from Template File(s) =

The ngfb_get_social_buttons() function can be used to include social buttons anywhere in your template files. For example, the following includes the Facebook, Google+, and Twitter social buttons from within a loop, post, or page (the $post-&gt;ID must be available):

<code>
&lt;?php if ( function_exists( 'ngfb_get_social_buttons' ) ) 
	echo ngfb_get_social_buttons( array( 'facebook', 'gplus', 'twitter' ) ); ?&gt;
</code>

Social button names for the array can be "facebook", "gplus", "twitter", "linkedin", "linkedin", "pinterest" and "tumblr".

You can also use the ngfb_get_social_buttons() function <em>outside</em> of a loop, post, or page, but you will have to provide additional information to the function. Since the $post variable is not available to get the permalink, at a minimum you will have to provide the webpage URL. Here's an example from a custom NextGEN Gallery template (plugins/nextgen-gallery/view/): 

<code>
if ( function_exists( 'ngfb_get_social_buttons' ) ) { 
	$url = $_SERVER['HTTPS'] ? 'https://' : 'http://';
	$url .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	echo ngfb_get_social_buttons( array( 'pinterest', 'tumblr'),
		array ( 'pid' =&gt; 'ngg-'.$image-&gt;pid, 'url' =&gt; $url, 'caption' =&gt; $image-&gt;caption ) );
}
</code>

This creates a Pinterest and tumblr button to share a picture from a NextGEN Gallery, sets the URL to the current webpage address, and uses the picture's caption as well. All social buttons, besides Pinterest and tumblr, only need the URL defined.

= Disable Open Graph Meta Tags =

You can exclude the Open Graph meta tags from being added to certain webpages. You must set the DISABLE_NGFB_OPEN_GRAPH constant to true in your theme's header.php before the wp_head() function. Here's an example that disables NextGEN Facebook OG's meta tags for image search results (a custom 'meta' template is called to define the Open Graph tags):

<code>
if ( is_search() && function_exists( 'ngg_images_results' ) && have_images() ) {
	global $nggSearch;
	define( 'DISABLE_NGFB_OPEN_GRAPH', true );
	echo $nggSearch-&gt;return_result( 'meta' );
}
wp_head();
</code>

