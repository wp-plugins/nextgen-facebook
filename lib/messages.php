<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbMessages' ) ) {

	class NgfbMessages {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get( $idx = '', $text = '', $tooltip_class = '' ) {
			$idx = sanitize_title_with_dashes( $idx );
			if ( empty( $tooltip_class ) )
				$tooltip_class = $this->p->cf['form']['tooltip_class'];
			switch ( $idx ) {
				case 'pro-feature-msg':
					if ( $this->p->is_avail['aop'] == true )
						$text = '<p class="pro-feature-msg"><a href="'.$this->p->cf['url']['purchase'].'" target="_blank">Purchase 
						additional licence(s) to enable Pro version features</p>';
					else
						$text = '<p class="pro-feature-msg"><a href="'.$this->p->cf['url']['purchase'].'" target="_blank">Upgrade 
						to the Pro version to enable the following features</a></p>';
					break;
				case 'pro-activate-nag':
					// in multisite, only show the activation message on our own plugin pages
					if ( ! is_multisite() || ( is_multisite() && preg_match( '/^.*\?page='.$this->p->cf['lca'].'-/', $_SERVER['REQUEST_URI'] ) ) ) {
						$url = $this->p->util->get_admin_url( 'advanced' );
						$text = '<p>The '.$this->p->cf['full'].' Authentication ID option value is empty.<br/>
						To activate Pro version features, and allow the plugin to authenticate itself for updates,<br/>
						<a href="'.$url.'">enter the unique Authenticaton ID you receive following your purchase
						on the Advanced Settings page</a>.</p>';
					}
					break;
				case 'pro-advert-nag':
					$text .= '
					<style type="text/css">.sucom-update-nag p { font-size:1.05em; }</style>
					<p>Have you considered encouraging the continued development and support of '.$this->p->cf['full'].
					' by purchasing the Pro version?</p>
					<p>'.$this->p->cf['full_pro'].' supports several types of Twitter Cards, including the <em>Gallery</em>, 
					<em>Player</em> and <em>Product</em> Twitter Cards, allows you to customize individual Post / Page meta tags, 
					and integrates with popular 3rd party plugins to improve Open Graph, Rich Pin, and Twitter Card meta tags.</p>
					<p><strong>Improve your social presence on Facebook, Twitter and Pinterest by providing your users with 
					better looking, more accurate and tailored posts</strong>.</p>
					<p>Upgrading to the Pro version is simple and takes just one or two minutes - <br/>
					<a href="'.$this->p->cf['url']['purchase'].'" target="_blank">purchase an '.$this->p->cf['full_pro'].' license right now</a>.</p>
					';
					break;
				case 'side-purchase':
					$text = '<p>Developing and supporting the '.$this->p->cf['full'].' plugin takes most of my work days (and week-ends).
					If you compare this plugin with others, I hope you\'ll agree that the result was worth all the effort and long hours.
					If you would like to show your appreciation, and access the full range of features this plugin has to offer, please purchase ';
					if ( $this->p->is_avail['aop'] == true )
						$text .= 'a Pro version license.</p>';
					else $text .= 'the Pro version.</p>';
					break;
				case 'side-thankyou':
					$text = '<p>Thank you for your purchase. I hope '.$this->p->cf['full'].' will exceed all of your expectations for many years to come.</p>';
					break;
				case 'side-help':
					$text = '<p>Individual option boxes (like this one) can be opened / closed by clicking on their title bar, 
					moved and re-ordered by dragging them, and removed / added from the <em>Screen Options</em> tab (top-right).
					Values in multiple tabs can be edited before clicking the \'Save All Changes\' button.</p>';
					if ( $this->p->is_avail['aop'] == true )
						$text .= '<p><strong>Need help with the Pro version?</strong>
						Review the <a href="'.$this->p->cf['url']['pro_codex'].'" target="_blank">Plugin Codex</a>
						and / or <a href="'.$this->p->cf['url']['pro_ticket'].'" target="_blank">Submit a new Support Ticket</a>.</p>';
					else
						$text .= '<p><strong>Need help with the GPL version?</strong>
						Review the <a href="'.$this->p->cf['url']['faq'].'" target="_blank">Frequently Asked Questions</a>, 
						the <a href="'.$this->p->cf['url']['notes'].'" target="_blank">Other Notes</a>, and / or visit the 
						<a href="'.$this->p->cf['url']['support'].'" target="_blank">Support Forum</a> on WordPress.org.</p>';
					break;
				/*
				 * Open Graph 'Image and Video' settings tab
				 */
				case 'tooltip-og_img_resize':
					$text = 'The dimension of images used in the Open Graph / Rich Pin meta tags. The width and height must be 
					greater than '.$this->p->cf['head']['min_img_width'].'x'.$this->p->cf['head']['min_img_height'].', 
					and preferably smaller than 1500x1500
					(the defaults is '.$this->p->opt->get_defaults( 'og_img_width' ).'x'.$this->p->opt->get_defaults( 'og_img_height' ).', '.
					( $this->p->opt->get_defaults( 'og_img_crop' ) == 0 ? 'not ' : '' ).'cropped). 
					<strong>Facebook recommends an image size of 1200x630, 600x315 as a minimum, and will ignore any images less than 200x200</strong>.
					If the original image is smaller than the dimensions entered here, then the full-size image will be used instead.';
					break;
				case 'tooltip-og_img_resize':
					$text = 'Automatically generate missing or incorrect image sizes for previously uploaded images in the 
					WordPress Media Library (default is checked).';
					break;
				case 'tooltip-og_def_img_id':
					$text = 'The ID number and location of your default image (example: 123). The <em>Default Image ID</em> 
					will be used as a fallback for Posts and Pages that do not have any images <em>featured</em>, 
					<em>attached</em>, or in their content. The Image ID number for images in the 
					WordPress Media Library can be found in the URL when editing an image (post=123 in the URL, for example). 
					The NextGEN Gallery Image IDs are easier to find -- it\'s the number in the first column when viewing a Gallery.';
					break;
				case 'tooltip-og_def_img_url':
					$text = 'You can also specify a <em>Default Image URL</em> (including the http:// prefix) instead of choosing a 
					<em>Default Image ID</em>. This allows you to use an image outside of a managed collection (WordPress Media Library or NextGEN Gallery). 
					The image should be at least '.$this->p->cf['head']['min_img_width'].'x'.$this->p->cf['head']['min_img_height'].' or more in width and height. 
					If both the <em>Default Image ID</em> and <em>Default Image URL</em> are defined, the <em>Default Image ID</em>
					will take precedence.';
					break;
				case 'tooltip-og_def_img_on_index':
					$text = 'Check this option if you would like to use the default image on index webpages (homepage, archives, categories, author, etc.). 
					If you leave this unchecked, '.$this->p->cf['full'].' will attempt to use image(s) from the first entry on the webpage (default is checked).';
					break;
				case 'tooltip-og_def_img_on_search':
					$text = 'Check this option if you would like to use the default image on search result webpages as well (default is checked).';
					break;
				case 'tooltip-og_ngg_tags':
					$text = 'If the <em>featured</em> image in a Post or Page is from a NextGEN Gallery, then add that image\'s tags to the 
					Open Graph / Rich Pin tag list (default is unchecked).';
					break;
				case 'tooltip-og_img_max':
					$text = 'The maximum number of images to list in the Open Graph / Rich Pin meta property tags -- this includes 
					the <em>featured</em> or <em>attached</em> images, and any images found in the Post or Page content.
					If you select \'0\', then no images will be listed in the Open Graph / Rich Pin meta tags.';
					break;
				case 'tooltip-og_vid_max':
					$text = 'The maximum number of videos, found in the Post or Page content, to include in the Open Graph / Rich Pin meta property tags. 
					If you select \'0\', then no videos will be listed in the Open Graph / Rich Pin meta tags.';
					break;
				case 'tooltip-og_vid_https':
					$text = 'Use an HTTPS connection whenever possible to retrieve information about videos from YouTube, Vimeo, Wistia, etc. (default is checked).';
					break;
				/*
				 * Open Graph 'Title and Description' settings tab
				 */
				case 'tooltip-og_art_section':
					$text = 'The topic that best describes the Posts and Pages on your website.
					This name will be used in the \'article:section\' Open Graph / Rich Pin meta tag. 
					Select \'[none]\' if you prefer to exclude the \'article:section\' meta tag.
					The Pro version also allows you to select a custom Topic for each individual Post and Page.';
					break;
				case 'tooltip-og_site_name':
					$text = 'By default, the Site Title from the <a href="'.get_admin_url( null, 'options-general.php' ).'">WordPress General Settings</a>
					page is used for the Open Graph, Rich Pin site name (\'og:site_name\' meta tag). You may override the default Site Title value here.';
					break;
				case 'tooltip-og_site_description':
					$text = 'By default, the Tagline in the <a href="'.get_admin_url( null, 'options-general.php' ).'">WordPress General Settings</a>
					page is used as a description for the index home page, and as fallback for the Open Graph, Rich Pin 
					description field (\'og:description\' meta tag). You may override that default value here.';
					break;
				case 'tooltip-og_title_sep':
					$text = 'One or more characters used to separate values (category parent names, page numbers, etc.) 
					within the Open Graph / Rich Pin title string (default is \''.$this->p->opt->get_defaults( 'og_title_sep' ).'\').';
					break;
				case 'tooltip-og_title_len':
					$text = 'The maximum length of text used in the Open Graph / Rich Pin title tag 
					(default is '.$this->p->opt->get_defaults( 'og_title_len' ).' characters).';
					break;
				case 'tooltip-og_desc_len':
					$text = 'The maximum length of text used in the Open Graph / Rich Pin description tag. 
					The length should be at least '.$this->p->cf['head']['min_desc_len'].' characters or more, and the
					default is '.$this->p->opt->get_defaults( 'og_desc_len' ).' characters.';
					break;
				case 'tooltip-og_page_title_tag':
					$text = 'Add the title of the <em>Page</em> to the Open Graph / Rich Pin article tags and Hashtag list (default is unchecked). 
					If the <em>Add Page Ancestor Tags</em> option is checked, all the titles of the ancestor Pages will be added as well. 
					This option works well if the title of your Pages are short (one or two words) and subject-oriented.';
					break;
				case 'tooltip-og_page_parent_tags':
					$text = 'Add the WordPress tags from the <em>Page</em> ancestors (parent, parent of parent, etc.) 
					to the Open Graph / Rich Pin article tags and Hashtag list (default is unchecked).';
					break;
				case 'tooltip-og_desc_hashtags':
					$text = 'The maximum number of tag names (not their slugs), converted to hashtags, to include in the 
					Open Graph / Rich Pin description, tweet text, and social captions.
					Each tag name is converted to lowercase with any whitespaces removed. 
					Select \'0\' (the default) to disable this feature.';
					break;
				case 'tooltip-og_desc_strip':
					$text = 'For a Page or Post <em>without</em> an excerpt, if this option is checked, 
					the plugin will ignore all text until the first html paragraph tag in the content. 
					If an excerpt exists, then this option is ignored, and the complete text of that 
					excerpt is used instead.';
					break;
				/*
				 * Open Graph 'Authorship' settings tab
				 */
				case 'tooltip-og_author_field':
					$text = 'Select the profile field to use for Posts and Pages in the \'article:author\' Open Graph / Rich Pin meta tag.
					The URL should point to an author\'s <em>personal</em> website or social page.
					This Open Graph / Rich Pin meta tag is primarily used by Facebook, so the preferred (and default) 
					value is the author\'s Facebook webpage URL.
					See the Google Settings below for an <em>Author Link URL</em> for Google.';
					break;
				case 'tooltip-og_author_fallback':
					$text = 'If the <em>Author Profile URL</em> (and the <em>Author Link URL</em> in the Google Settings below) 
					is not a valid URL, then '.$this->p->cf['full'].' can fallback to using the author index on this 
					website (\''.trailingslashit( site_url() ).'author/username\' for example). 
					Uncheck this option to disable the fallback feature (default is unchecked).';
					break;
				case 'tooltip-og_def_author_id':
					$text = 'A default author for webpages <em>missing authorship information</em> (for example, an index webpage without posts). 
					If you have several authors on your website, you should probably leave this option set to <em>[none]</em> (the default).';
					break;
				case 'tooltip-og_def_author_on_index':
					$text = 'Check this option if you would like to force the <em>Default Author</em> on index webpages 
					(homepage, archives, categories, author, etc.). 
					If this option is checked, index webpages will be labeled as a an \'article\' with authorship 
					attributed to the <em>Default Author </em> (default is unchecked).
					If the <em>Default Author</em> is <em>[none]</em>, then the index webpages will be labeled as a \'website\'.';
					break;
				case 'tooltip-og_def_author_on_search':
					$text = 'Check this option if you would like to force the <em>Default Author</em> on search result webpages as well.
					If this option is checked, search results will be labeled as a an \'article\' with authorship
					attributed to the <em>Default Author </em> (default is unchecked).';
					break;
				case 'tooltip-og_publisher_url':
					$text = 'The URL of your website\'s social page (usually a Facebook page). 
					For example, the Publisher Page URL for <a href="http://surniaulula.com/" target="_blank">Surnia Ulula</a> 
					is <a href="https://www.facebook.com/SurniaUlulaCom" target="_blank">https://www.facebook.com/SurniaUlulaCom</a>.
					The Publisher Page URL will be included on <em>article</em> type webpages (not indexes).
					See the Google Settings below for a <em>Publisher Link URL</em> for Google.';
					break;
				/*
				 * Publisher 'Facebook' settings tab
				 */
				case 'tooltip-fb_admins':
					$text = 'The <em>Facebook Admin(s)</em> user names are used by Facebook to allow access to 
					<a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data.
					Note that these are <em>user</em> account names, not Facebook <em>page</em> names.
					<p>Enter one or more Facebook user names, separated with commas. 
					When viewing your own Facebook wall, your user name is located in the URL 
					(example: https://www.facebook.com/<strong>user_name</strong>). 
					Enter only the user user name(s), not the URL(s).</p>
					<a href="https://www.facebook.com/settings?tab=account&section=username&view" target="_blank">Update 
					your user name in the Facebook General Account Settings</a>.';
					break;
				case 'tooltip-fb_app_id':
					$text = 'If you have a <a href="https://developers.facebook.com/apps" target="_blank">Facebook Application</a> 
					ID for your website, enter it here. The Facebook Application ID will appear in your webpage meta tags,
					and is used by Facebook to allow access to <a href="https://developers.facebook.com/docs/insights/" 
					target="_blank">Facebook Insight</a> data for <em>accounts associated with that Application ID</em>.';
					break;
				case 'tooltip-fb_lang':
					$text = 'The language / locale for your website content. This option also controls the language of the 
					Facebook social sharing button.';
					break;
				/*
				 * Publisher 'Google' settings tab
				 */
				case 'tooltip-meta_desc_len':
					$text = 'The maximum length of text used for the Google Search description meta tag.
					The length should be at least '.$this->p->cf['head']['min_desc_len'].' characters or more 
					(the default is '.$this->p->opt->get_defaults( 'meta_desc_len' ).' characters).';
					break;
				case 'tooltip-link_author_field':
					$text = $this->p->cf['full'].' can include an <em>author</em> and <em>publisher</em> link in your webpage headers.
					These are not Open Graph / Rich Pin meta property tags - they are used primarily by Google\'s search engine 
					to associate Google+ profiles with search results.';
					break;
				case 'tooltip-link_def_author_id':
					$text = 'A default author for webpages missing authorship information (for example, an index webpage without posts). 
					If you have several authors on your website, you should probably leave this option set to <em>[none]</em> (the default).
					This option is similar to the Open Graph / Rich Pin <em>Default Author</em>, except that it\'s applied to the Link meta tag instead.';
					break;
				case 'tooltip-link_def_author_on_index':
					$text = 'Check this option if you would like to force the <em>Default Author</em> on index webpages 
					(homepage, archives, categories, author, etc.).';
					break;
				case 'tooltip-link_def_author_on_search':
					$text = 'Check this option if you would like to force the <em>Default Author</em> on search result webpages as well.';
					break;
				case 'tooltip-link_publisher_url':
					$text = 'If you have a <a href="http://www.google.com/+/business/" target="_blank">Google+ business page for your website</a>, 
					you may use it\'s URL as the Publisher Link. 
					For example, the Publisher Link URL for <a href="http://surniaulula.com/" target="_blank">Surnia Ulula</a> 
					is <a href="https://plus.google.com/u/1/103457833348046432604/posts" target="_blank">https://plus.google.com/u/1/103457833348046432604/posts</a>.
					The <em>Publisher Link URL</em> may take precedence over the <em>Author Link URL</em> in Google\'s search results.';
					break;
				/*
				 * Publisher 'Twitter' settings tab
				 */
				case 'tooltip-tc_enable':
					$text = 'Add Twitter Card meta tags to all webpage headers.
					<strong>Your website must be "authorized" by Twitter for each type of Twitter Card you support</strong>. 
					See the FAQ entry titled <a href="http://surniaulula.com/codex/plugins/nextgen-facebook/faq/why-dont-my-twitter-cards-show-on-twitter/" 
					target="_blank">Why don’t my Twitter Cards show on Twitter?</a> for more information on Twitter\'s 
					authorization process.';
					break;
				case 'tooltip-tc_desc_len':
					$text = 'The maximum length of text used for the Twitter Card description.
					The length should be at least '.$this->p->cf['head']['min_desc_len'].' characters or more 
					(the default is '.$this->p->opt->get_defaults( 'tc_desc_len' ).' characters).';
					break;
				case 'tooltip-tc_site':
					$text = 'The Twitter username for your website and / or company (not your personal Twitter username).
					As an example, the Twitter username for <a href="http://surniaulula.com/" target="_blank">Surnia Ulula</a> 
					is <a href="https://twitter.com/surniaululacom" target="_blank">@surniaululacom</a>.';
					break;
				case 'tooltip-tc_sum_size':
					$text = 'The size of content images provided for the
					<a href="https://dev.twitter.com/docs/cards/types/summary-card" target="_blank">Summary Card</a>
					(should be at least 120x120, larger than 60x60, and less than 1MB).';
					break;
				case 'tooltip-tc_large_size':
					$text = 'The size of Post Meta, Featured or Attached images provided for the
					<a href="https://dev.twitter.com/docs/cards/types/large-image-summary-card" target="_blank">Large Image Summary Card</a>
					(must be larger than 280x150 and less than 1MB).';
					break;
				case 'tooltip-tc_photo_size':
					$text = 'The size of ImageBrowser or Attachment Page images provided for the 
					<a href="https://dev.twitter.com/docs/cards/types/photo-card" target="_blank">Photo Card</a> 
					(should be at least 560x750 and less than 1MB).';
					break;
				case 'tooltip-tc_gal_size':
					$text = 'The size of gallery images provided for the
					<a href="https://dev.twitter.com/docs/cards/types/gallery-card" target="_blank">Gallery Card</a>.';
					break;
				case 'tooltip-tc_gal_min':
					$text = 'The minimum number of images found in a gallery to qualify for the
					<a href="https://dev.twitter.com/docs/cards/types/gallery-card" target="_blank">Gallery Card</a>.';
					break;
				case 'tooltip-tc_prod_size':
					$text = 'The size of a featured product image for the
					<a href="https://dev.twitter.com/docs/cards/types/product-card" target="_blank">Product Card</a>.
					The product card requires an image of size 160 x 160 or greater. A square (aka cropped) image is better, 
					but Twitter can crop/resize oddly shaped images to fit, as long as both dimensions are greater 
					than or equal to 160 pixels.';
					break;
				case 'tooltip-tc_prod_def':
					$text = 'The <em>Product</em> Twitter Card needs a minimum of two product attributes.
					The first attribute will be the product price, and if your product has additional attribute fields associated with it 
					(weight, size, color, etc), these will be included in the <em>Product</em> Card as well (maximum of 4 attributes). 
					<strong>If your product does not have additional attributes beyond just a price</strong>, then this default second attribute label and value will be used. 
					You may modify both the Label <em>and</em> Value for whatever is most appropriate for your website and/or products.';
					break;
				/*
				 * 'Profile Contact Methods' settings
				 */
				case 'contact-info':
					$text = '<p>The following options allow you to customize the contact field names and labels shown on the 
					<a href="'.get_admin_url( null, 'profile.php' ).'">user profile page</a>. '.$this->p->cf['full'].' uses the Facebook, 
					Google+ and Twitter contact field values for Open Graph and Twitter Card meta tags (along with the Twitter social sharing button).
					<strong>You should not modify the <em>Contact Field Name</em> unless you have a very good reason to do so.</strong>
					The <em>Profile Contact Label</em> on the other hand, is for display purposes only, and its text can be changed as you wish.
					Although the following contact methods may be shown on user profile pages, your theme is responsible for displaying their values 
					in the appropriate template locations (see <a href="http://codex.wordpress.org/Function_Reference/get_the_author_meta" 
					target="_blank">get_the_author_meta()</a> for examples).</p>';
					break;
				case 'tooltip-plugin-cm-field-name':
					$text = '<strong>You should not modify the contact field names unless you have a specific reason to do so.</strong>
					As an example, to match the contact field name of a theme or other plugin, you might change \'gplus\' to \'googleplus\'.
					If you change the Facebook or Google+ field names, please make sure to update the Open Graph 
					<em>Author Profile URL</em> and Google <em>Author Link URL</em> options in the '.
					$this->p->util->get_admin_url( 'general', 'General Settings' ).' as well.';
					break;
				case 'tooltip-wp-cm-field-name':
					$text = 'The built-in WordPress contact field names cannot be changed.';
					break;
				/*
				 * 'Meta Tag List' settings
				 */
				case 'taglist-info':
					$text = '<p>'.$this->p->cf['full'].' will add the following Facebook and Open Graph meta tags to your webpages. 
					If your theme or another plugin already generates one or more of these meta tags, you may uncheck them here to 
					prevent duplicates from being added (for example, the "description" meta tag is unchecked by default if any 
					known SEO plugin was detected).</p>';
					break;
				case 'tooltip-og_empty_tags':
					$text = 'Include meta property tags of type og:* without any content (default is unchecked).';
					break;
				/*
				 * 'Plugin Features' side metabox
				 */
				case 'tooltip-side-debug-messages':
					$text = 'Debug code is loaded when the \'Add Hidden Debug Info\' option is checked or one of the available 
					<a href="http://surniaulula.com/codex/plugins/nextgen-facebook/notes/constants/" target="_blank">debugging 
					constants</a> is defined.';
					break;
				case 'tooltip-side-nextgen-gallery':
					$text = 'The NextGEN Gallery integration addon is loaded only when the NextGEN Gallery plugin is detected.';
					break;
				case 'tooltip-side-non-persistant-cache':
					$text = $this->p->cf['full'].' saves filtered / rendered content to a non-persistant cache
					(aka <a href="http://codex.wordpress.org/Class_Reference/WP_Object_Cache" target="_blank">WP Object Cache</a>) 
					for re-use within the same page load. You can adjust the \'Object Cache Expiry\' value on the '.
					$this->p->util->get_admin_url( 'advanced', 'Advanced settings' ).' page, and disable the non-persistant cache feature 
					using one of the available <a href="http://surniaulula.com/codex/plugins/nextgen-facebook/notes/constants/" 
					target="_blank">constant</a>.';
					break;
				case 'tooltip-side-open-graph-rich-pin':
					$text = 'Open Graph and Rich Pin meta tags are added to the head section of all webpages. 
					You must have a compatible eCommerce plugin installed to include <em>Product</em> Rich Pins, 
					including their prices, images, and other attributes.';
					break;
				case 'tooltip-side-pro-update-check':
					$text = 'When a \'Pro Version Authentication ID\' is entered on the '.$this->p->util->get_admin_url( 'advanced', 
					'Advanced settings' ).' page, an update check is scheduled every 12 hours to see if a new Pro version is available.';
					break;
				case 'tooltip-side-social-sharing-buttons':
					$text = 'Social sharing features include the Open Graph+ '.$this->p->util->get_admin_url( 'social', 'Social Sharing' ).
					' and '.$this->p->util->get_admin_url( 'style', 'Social Style' ).' settings pages (aka social sharing buttons), 
					the Custom Settings - Social Sharing tab on Post and Page editing pages, along with the social sharing shortcode 
					and widget. All social sharing features can be disabled using an available
					<a href="http://surniaulula.com/codex/plugins/nextgen-facebook/notes/constants/" target="_blank">constant</a>.';
					break;
				case 'tooltip-side-social-sharing-shortcode':
					$text = 'Support for shortcode(s) can be enabled / disabled on the '.
					$this->p->util->get_admin_url( 'advanced', 'Advanced settings' ).' page. Shortcodes are disabled by default
					to optimize WordPress performance and content processing.';
					break;
				case 'tooltip-side-social-sharing-widget':
					$text = 'The social sharing widget feature adds an \'NGFB Social Sharing\' widget in the WordPress Appearance - Widgets page.
					The widget can be used in any number of widget areas, to share the current webpage. The widget, along with all social
					sharing featured, can be disabled using an available 
					<a href="http://surniaulula.com/codex/plugins/nextgen-facebook/notes/constants/" target="_blank">constant</a>.';
					break;
				case 'tooltip-side-transient-cache':
					$text = $this->p->cf['full'].' saves Open Graph, Rich Pin, Twitter Card meta tags, and social buttons to a persistant
					(aka <a href="http://codex.wordpress.org/Transients_API" target="_blank">Transient</a>) cache for '.
					$this->p->options['plugin_object_cache_exp'].' seconds (default is '.$this->p->opt->defaults['plugin_object_cache_exp'].
					' seconds). You can adjust the Transient Cache expiration value from the '.
					$this->p->util->get_admin_url( 'advanced', 'Advanced settings' ).' page, or disable it completely using an available
					<a href="http://surniaulula.com/codex/plugins/nextgen-facebook/notes/constants/" target="_blank">constant</a>.';
					break;
				case 'tooltip-side-social-file-cache':
					$text = $this->p->cf['full_pro'].' can save social sharing images and JavaScript to a cache folder, 
					and provide URLs to these cached files instead of the originals. The current \'File Cache Expiry\'
					value, as defined on the '.$this->p->util->get_admin_url( 'advanced', 'Advanced settings' ).' page, is '.
					$this->p->options['plugin_file_cache_hrs'].' Hours (the default value of 0 Hours disables the 
					file caching feature).';
					break;
				case 'tooltip-side-custom-post-meta':
					$text = 'The Custom Post Meta feature adds an Open Graph+ Custom Settings metabox to the Post and Page editing pages.
					Custom values van be entered for Open Graph, Rich Pin, and Twitter Card meta tags, along with custom social sharing
					text and meta tag validation tools.';
					break;
				case 'tooltip-side-wp-locale-language':
					$text = $this->p->cf['full_pro'].' uses the WordPress locale value to define a language for the Open Graph and Rich Pin meta tags,
					along with the Google, Facebook, and Twitter social sharing buttons. If your website and/or webpages are available in multiple
					languages, this can be an important feature.';
					break;
				case 'tooltip-side-twitter-cards':
					$text = 'Twitter Cards extend the standard Open Graph and Rich Pin meta tags with content-specific information for image galleries, 
					photographs, eCommerce products, etc. Twitter Cards are displayed differently on Twitter, either online or from mobile Twitter 
					clients, allowing you to better feature your content. The Twitter Cards addon can be enabled from the '.
					$this->p->util->get_admin_url( 'general', 'General settings' ).' page.';
					break;
				case 'tooltip-side-url-rewriter':
					$text = $this->p->cf['full_pro'].' can rewrite image URLs in meta tags, cached images and JavaScript, 
					and for social sharing buttons like Pinterest and Tumblr, which use URL-encoded image URLs. 
					Rewriting image URLs can be an important part of optimizing page load speeds. See the \'Static Content URL(s)\'
					option on the '.$this->p->util->get_admin_url( 'advanced', 'Advanced settings' ).' page to enable URL rewriting.';
					break;
				case 'tooltip-side-url-shortener':
					$text = '<strong>When using the Twitter social sharing button provided by '.$this->p->cf['full_pro'].'</strong>, 
					the webpage URL (aka the <em>canonical</em> or <em>permalink</em> URL) within the Tweet, 
					can be shortened by one of the available URL shortening services. Enable URL shortening for Twitter
					from the '.$this->p->util->get_admin_url( 'social', 'Social Sharing' ).' settings page.';
					break;
				case 'tooltip-side-wistia-video-api':
					$text = 'If the \'Check for Wistia Videos\' option on the '.
					$this->p->util->get_admin_url( 'advanced', 'Advanced settings' ).' page is checked, '.
					$this->p->cf['full_pro'].' will load an integration addon for Wistia to detect embedded Wistia videos, 
					and retrieve information (video dimentions, preview image, etc) using Wistia\'s oEmbed API.';
					break;
			}
			if ( strpos( $idx, 'tooltip' ) !== false && ! empty( $text ) )
				return '<img src="'.NGFB_URLPATH.'images/question-mark.png" width="14" height="14" class="'.
					$tooltip_class.'" alt="'.esc_attr( $text ).'" />';
			else return $text;
		}
	}
}
?>
