=== NextGEN Facebook ===
Contributors: jsmoriss
Tags: nextgen, facebook, featured, open graph, meta, buttons, like, send, share
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 1.4
License: GPLv2 or later

Adds Facebook HTML meta tags to webpage headers, including featured images.
Also includes optional Like and Send Facebook buttons.

== Description ==

The NextGEN Facebook plugin adds Facebook Open Graph HTML meta tags to all
webpage headers, including the artical meta tags for posts and pages. Featured
image thumbnails, from a NextGEN Gallery or Media Library, are listed in the
image meta tag. You can also, optionally, add Facebook like and send buttons
to your posts and pages.

NextGEN Facebook was specifically written to support featured images located
in a NextGEN Gallery, but works just as well with the WordPress Media Library.
<em>The NextGEN Gallery plugin is not required to use this plugin</em> - all
features work just as well without it.

The image used in the Open Graph meta tag is determined in this sequence; a
featured image from a NextGEN Gallery or WordPress Media Library, the first
NextGEN [singlepic] or IMG HTML tag in the content, a default image defined in
the plugin settings. If none of these conditions can be satisfied, then the
Open Graph image tag will be left empty.

This plugin goes well beyond any other plugins I know in handling various
archive-type webpages. It will create appropriate title and description meta
tags for category, tag, date based archive (day, month, or year), and author
webpages.

Here's an example of the Open Graph meta tags for a post:

<code>
&lt;!-- NextGEN Facebook Plugin Open Graph Tags: BEGIN --&gt;
&lt;meta property="fb:app_id" content="345251245549378" /&gt;
&lt;meta property="og:site_name" content="The Road to Myself" /&gt;
&lt;meta property="og:title" content="Odds and Ends : An Article Title" /&gt;
&lt;meta property="og:type" content="article" /&gt;
&lt;meta property="og:image" content="http://trtms.com/wp-content/gallery/cache/167_crop_200x200_filename.jpg" /&gt;
&lt;meta property="og:description" content="An excerpt from the post, up to a specified length..." /&gt;
&lt;meta property="og:url" content="http://trtms.com/2012/07/14/odds-and-ends-an-article-title/" /&gt;
&lt;meta property="article:published_time" content="2012-07-14T08:54:10+00:00" /&gt;
&lt;meta property="article:modified_time" content="2012-07-15T08:51:52+00:00" /&gt;
&lt;meta property="article:section" content="Travel" /&gt;
&lt;meta property="article:author" content="jsmoriss" /&gt;
&lt;meta property="article:tag" content="Some Tags, Given, To Your, Article" /&gt;
&lt;!-- NextGEN Facebook Plugin Open Graph Tags: END --&gt;
</code>

You can view real-life examples by visiting the following URLs on my website
and right-clicking "View Page Source". The Open Graph meta tags are clearly
marked and should be easy to spot.

<ul>

<p><a href="http://trtms.com/">The Home Page</a>: The default image is used,
the website name and tagline are listed in the title and description tags, and
finally there are no article meta tags present.</p>

<p><a
href="http://trtms.com/2012/07/14/odds-and-ends-getting-the-truck-ready/">A
Post Webpage</a>: A featured image from NextGEN Gallery is used, and the
article meta tags have been added.</p>

<p><a href="http://trtms.com/category/posting-from/basecamp/">A Category
Webpage</a>: The default image is used, and notice the title contains a parent
category name as well. The child and it's parents (if any) are listed,
delimited by pipe "|" characters, in the title meta tag. Note that if the
parent category name ends with three dots "..." (like this one does), the pipe
character will not be added. The description meta tag also includes the
wordpress category description.</p>

</ul>

That should give you a pretty good idea of what this plugin is capable of. :)

NextGEN Facebook is being actively developed and supported. Post your comments
and suggestions to the <a
href="http://wordpress.org/support/plugin/nextgen-facebook"
target="_blank">NextGEN Facebook Support Page</a>.

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

Once activated, you don't have to configure any settings for NextGEN Facebook
to automatically start adding the Open Graph meta tags to your pages.

Some plugin options are available under Settings->NextGEN Facebook to select a
default image, include Facebook buttons at the end of Posts and Pages, change
the default thumbnail image sizes, etc.

== Screenshots ==

1. WordPress v3.4.1 and NextGEN Facebook - The Settings Page

== Frequently Asked Questions ==

= Q. Why doesn't Facebook's send / share show my featured image? =

**A.** The first time Facebook accesses your webpage, it will cache the image
and text it finds. Facebook will then prefer to use that cached information
until it has expired. So, before you hit the send / share button for the first
time, make sure you've defined your featured image and (optionally) the
excerpt text. If you change your mind, and your webpage has not been liked or
shared yet, then try waiting a few hours before trying again. If your webpage
has already been liked or shared on Facebook, then there's nothing you can do
to change the image, text, or title that was used.

== Changelog ==

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

