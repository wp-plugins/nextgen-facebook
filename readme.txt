=== NextGEN Facebook ===
Contributors: jsmoriss
Tags: nextgen, featured, open graph, meta, buttons, like, send, share, image, wp-wikibox, wikipedia, facebook, google, google plus, g+, twitter, linkedin, social, seo, search engine optimization, exclude pages
Requires at least: 3.0
Tested up to: 3.4.2
Stable tag: 1.7
License: GPLv2 or later

Adds Open Graph HTML meta tags for Facebook, G+, LinkedIn, etc. Includes optional FB, G+, Twitter and LinkedIn sharing buttons.

== Description ==

The NextGEN Facebook plugin adds <a href="http://ogp.me/" target="_blank">Open Graph</a> meta tags to all webpage headers, including the "artical" object type for posts and pages. The featured image thumbnails, from a NextGEN Gallery or Media Library, are also correctly listed in the "image" meta tag.  This plugin goes well beyond any other plugins I know in handling various archive-type webpages. It will create appropriate title and description meta tags for category, tag, date based archive (day, month, or year), author webpages and search results. You can also, optionally, add Facebook, Google+, Twitter and LinkedIn sharing buttons to post and page content.

The Open Graph protocol enables any web page to become a rich object in a social graph. For instance, this is used on Facebook to allow any web page to have the same functionality as any other object on Facebook. The Open Graph meta tags are read by almost all social websites, including Facebook, Google (Search and Google+), and LinkedIn.

NextGEN Facebook was specifically written to support featured images located in a NextGEN Gallery, but also works just as well with the WordPress Media Library. <strong>The NextGEN Gallery plugin is not required to use this plugin - all features work just as well without it</strong>. The image used in the Open Graph meta tag is chosen in this sequence; a featured image from a NextGEN Gallery or WordPress Media Library, the first NextGEN [singlepic] or IMG HTML tag in the content, a default image defined in the plugin settings. If none of these conditions can be satisfied, then the Open Graph image tag will be left empty.

Here's an example of Open Graph meta tags <a href="http://surniaulula.com/2012/10/02/span-filepaths-in-wordpress-content/">for a post on my website</a>:

<code>
&lt;!-- NextGEN Facebook Meta Tags BEGIN --&gt;
&lt;meta property="article:author" content="http://surniaulula.com/author/jsmoriss/" /&gt;
&lt;meta property="article:modified_time" content="2012-10-16T12:20:00+00:00" /&gt;
&lt;meta property="article:published_time" content="2012-10-02T22:30:15+00:00" /&gt;
&lt;meta property="article:section" content="Technology" /&gt;
&lt;meta property="article:tag" content="filepath" /&gt;
&lt;meta property="article:tag" content="pcre" /&gt;
&lt;meta property="article:tag" content="php" /&gt;
&lt;meta property="article:tag" content="preg_replace" /&gt;
&lt;meta property="article:tag" content="replace" /&gt;
&lt;meta property="article:tag" content="span" /&gt;
&lt;meta property="fb:admins" content="jsmoriss" /&gt;
&lt;meta property="og:description" content="I wanted filepaths and filenames in WordPress post/page content to be displayed with a monospace font, so I wrote the following PHP plugin to wrap filepaths with a &lt;span&gt; HTML and CSS style tag. The code could be added to a theme&#039;s functions.php file, or used as a plugin (as shown" /&gt;
&lt;meta property="og:image" content="http://surniaulula.com/wp-content/gallery/cache/5_crop_200x200_20120814-114043-sbellive-0078.jpg" /&gt;
&lt;meta property="og:site_name" content="Surnia Ulula" /&gt;
&lt;meta property="og:title" content="Span Filepaths in WordPress Content" /&gt;
&lt;meta property="og:type" content="article" /&gt;
&lt;meta property="og:url" content="http://surniaulula.com/2012/10/02/span-filepaths-in-wordpress-content/" /&gt;
&lt;!-- NextGEN Facebook Meta Tags END --&gt;
</code>

NextGEN Facebook is being actively developed and supported. Post your comments and suggestions to the <a href="http://wordpress.org/support/plugin/nextgen-facebook" target="_blank">NextGEN Facebook Support Page</a>, and don't forget to review the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/">NextGEN Facebook FAQ Page</a> for additional notes on using the plugin.

== Installation ==

*Using the WordPress Dashboard*

1. Login to your weblog
1. Go to Plugins
1. Select Add New
1. Search for *NextGEN Facebook*
1. Select Install
1. Select Install Now
1. Select Activate Plugin

*Manual*

1. Download and unzip the plugin
1. Upload the entire nextgen-facebook/ folder to the /wp-content/plugins/ directory
1. Activate the plugin through the Plugins menu in WordPress

Once activated, you don't have to configure any settings for NextGEN Facebook to automatically start adding the Open Graph meta tags to your pages.

Some plugin options are available under Settings -&gt; NextGEN Facebook to select a default image, include Facebook buttons at the end of Posts and Pages, change the default thumbnail image sizes, etc.

== Screenshots ==

1. WordPress v3.4.2 and NextGEN Facebook v1.7 - The Settings Page

== Frequently Asked Questions ==

= Q. Why doesn't Facebook show my featured image? =

**A.** The first time Facebook accesses your webpage, it will cache the image and text it finds. Facebook then prefers to use that cached information until it has expired. So, before you hit the send / share button for the first time, make sure you've chosen your featured image and (optionally) entered an excerpt text. If you change your mind, and your webpage has not been liked or shared yet, then try using <a href="https://developers.facebook.com/tools/debug" target="_blank">Facebook's Open Graph debugging tool</a>. If your webpage has already been liked or shared on Facebook, then there's nothing you can do to change the image, text, or title that was used.

= Q. How can I see what Facebook sees? =

**A.** Facebook has an <a href="https://developers.facebook.com/tools/debug" target="_blank">Open Graph debugging tool</a> where you can enter a URL and view a report of it's findings. Try it with your posts, pages, archive pages, author pages, search results, etc. to see how NextGEN Facebook presents your content.

If there are Open Graph Warnings, read them carefully -- usually they explain that the information they *already have* for this webpage is in conflict with the Open Graph information now being presented. This might be just the published and modified times, or (if the webpage has already been liked or shared) the title and image Facebook has saved previously.

= Q. What about Google Search and Google Plus? =

**A.** Google reads the Open Graph meta tags, along with other "structured data markup" on your webpage. You can see what Google picks up from your webpages by using it's <a href="http://www.google.com/webmasters/tools/richsnippets" target="_blank">Rich Snippets Testing Tool</a>. You may also want to link your WordPress authors with their Google+ profiles by using one of the available plugins, like <a href="http://wordpress.org/extend/plugins/google-author-information-in-search-results-wordpress-plugin/" target="_blank">Google Plus Author Information in Search Result (GPAISR)</a> or others like it.

= Q. Does LinkedIn read the Open Graph tags? =

**A.** According to LinkedIn's <a href="https://developer.linkedin.com/documents/setting-display-tags-shares" target="_blank">Setting Display Tags for Shares</a> information page, they use three of the Open Graph tags (title, description, and url).

= Q. How can I control the social button layouts? =

**A.** NextGEN Facebook uses the 'ngfb-buttons' class name to wrap all buttons, and each social button has it's own individual class name as well. Here's an example of the CSS I use for <a href="http://trtms.com/">trtms.com</a>:

<code>
.ngfb-buttons { text-align:center; }
.facebook-button,
.g-plusone-button,
.twitter-share-button, 
.linkedin-button {
    display:inline-block;
    vertical-align:bottom;
    height:20px;
    padding:0;
    margin:0;
}
.facebook-button { margin-right:22px; }
.g-plusone-button { margin-right:-10px; }
.twitter-share-button { margin-right:-10px; }
.linkedin-button { margin-right:22px; }
</code>

= Q. The <a hreh="http://validator.w3.org/">W3C Markup Validation Service</a> complains that 'there is no attribute "property"'. =

**A.** The Facebook / Open Graph &lt;meta property="" /&gt; attribute is not part of the HTML5 standard, so the W3C validator is correct in throwing up an error. In practice though, this incorrect attribute is completely harmless -- social sites (Facebook, Google+, etc.) look for it and don't care if it's part of the standard or not.

If you want to address the W3C validator error, you'll have to change the DOCTYPE of your website to XHTML+RDFa (an example follows). The DOCTYPE definition is usually located in the header.php file of your theme.

<code>
&lt;!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd"&gt;
</code>

== Changelog ==

= v.1.7 =
* Added LinkedIn social button options.
* Added a setting to include hidden debug info above the Open Graph tags.
* If the Exclude Pages plugin is installed, a new option will be available on the settings page to turn on/off social buttons on excluded pages (by default, social buttons are not added to excluded pages).
* Added the og:video meta tags (including width, height, type, etc.) for youtube iframe embeded videos.
* Cleaned-up some PHP code to consolidate the OG variables within a single array.

= v.1.6.1 =
* Fixed a bug where some checked options -- those that should be ON by default -- would always stay checked. Thanks to chrisjborg for reporting this one.
* Stripped javascript from the_content text so it doesn't make it to the og:description meta tag.

= v.1.6 =
* Added the Google+ and Twitter button options.
* Added the "Open Graph HTML Meta Tags" options to exclude one or more Facebook and Open Graph HTML meta tags from the webpage headers.

= v.1.5.1 =
* Added the "Default Image on Search Page" option.
* Added the "WP-WikiBox Tag Prefix" option to identify the WordPress tag names used to retrieve Wikipedia content.
* The two WP-WikiBox options ("Use WP-WikiBox for Pages" and "WP-WikiBox Tag Prefix") will not appear on the settings page unless the WP-WikiBox plugin is installed and activated.
* Updated the readme's Description and FAQ sections with more information on Open Graph and it's use by Google and LinkedIn.

= v.1.5 =
* Added the "Add NextGEN Gallery Tags" option to include the featured (or default) image tags from the NextGEN Gallery.
* Added the "Content Begins at a Paragraph" option to ignore all text before the first &lt;p&gt; paragraph in the content (not the excerpt). This might be useful for photography oriented websites that have thumbnails, captions, etc. preceeding their content.
* Added the "Use WP-WikiBox for Pages" option. This is an option very few, if any, besides myself will find useful. If you use the WP-WikiBox plugin's function in your theme's templates (and not the shortcode), then you can use the page's tags or title to retrieve content from Wikipedia. This only aplies to pages, not posts. Here's how it works; the plugin will check for the page's tags, and use their names to retrieve content from Wikipedia. If no tags are defined, then the page title will be used. If Wikipedia does not return a summary for your tags or title, then the content of your page will be used.

= v1.4.1 =
* Small fixes to the article:tag and article:author after finding additional information on these meta tags.
* Changed article:tag by including it once for every WordPress tag, instead only once with an array of values.
* Changed article:author to a URL instead of the author's name.

= v1.4 =
* Added a website topic setting to choose a value for the article:section meta tag. If defined, the article:section meta tag will be added to all posts and pages. You can leave it blank, if for example, your website covers a wide variety of subjects.
* When viewing a tag webpage, added the tag description (if there is one) to the Open Graph description meta tag.
* When viewing a author webpage, added the author biographical info (if there is one) to the Open Graph description meta tag.

= v1.3 =
* Moved some code out of the meta HTML section to make it cleaner. There's no functional difference, it just looks nicer. ;-)
* When viewing a category webpage, added the category description (if there is one) to the Open Graph description meta tag.
* When viewing a category webpage, added the category parents (if any) to the Open Graph title meta tag.
* Added the following meta tags: article:published_time, article:modified_time, article:author, article:tag.

= v1.2 =
* Added the Default Image ID option, in addition to the existing Default Image URL option. Since the plugin can find images by ID number, might as well use it for the default image as well. :)

= v1.1 =
* Improved the description and installation texts.
* Used a single screenshot image of the settings page, instead of two.
* Removed the "NextGEN Gallery Image Size" setting - it was a bit redundant. Instead, I've suggested using an existing Size Name from the Media Library, and if necessary, to create an additional Size Name specifically for NextGEN Facebook. Since NextGEN Gallery doesn't understand these Size Names, I've added some code to get the width, height, and crop from the Size Name, before calling the necessary NextGEN Gallery functions.
* Added a "Use Default on Multi-Entry Pages" checkbox to force the default image to be used on the homepage, category page, author page, etc. (instead of the featured image from the first post, for example).
* Added extra parsing for author pages, tag pages, category pages, etc., to refine the og:description text.
* Also improved the og:title text for archive pages, category pages, etc. 
* No bugs were reported or fixed from the previous version, which is good news I guess. ;-)

= v1.0 =
* Initial release.

