<?php
/*
Copyright 2012 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
*/

if ( ! class_exists( 'ngfbAdmin' ) ) {

	class ngfbAdmin extends ngfbPlugin {
	
		// list from http://en.wikipedia.org/wiki/Category:Websites_by_topic
		var $article_sections = array(
			'Animation',
			'Architecture',
			'Art',
			'Automotive',
			'Aviation',
			'Chat',
			'Children\'s',
			'Comics',
			'Commerce',
			'Community',
			'Dance',
			'Dating',
			'Digital Media',
			'Documentary',
			'Download',
			'Economics',
			'Educational',
			'Employment',
			'Entertainment',
			'Environmental',
			'Erotica and Pornography',
			'Fashion',
			'File Sharing',
			'Food and Drink',
			'Fundraising',
			'Genealogy',
			'Health',
			'History',
			'Humor',
			'Law Enforcement',
			'Legal',
			'Literature',
			'Medical',
			'Military',
			'News',
			'Nostalgia',
			'Parenting',
			'Photography',
			'Political',
			'Religious',
			'Review',
			'Reward',
			'Route Planning',
			'Satirical',
			'Science Fiction',
			'Science',
			'Shock',
			'Social Networking',
			'Spiritual',
			'Sport',
			'Technology',
			'Travel',
			'Vegetarian',
			'Webmail',
			'Women\'s',
		);
	
		var $locale = array(
			'af'	=> 'Afrikaans',
			'am'	=> 'Amharic',
			'ar'	=> 'Arabic',
			'eu'	=> 'Basque',
			'bn'	=> 'Bengali',
			'bg'	=> 'Bulgarian',
			'ca'	=> 'Catalan',
			'zh-HK'	=> 'Chinese (Hong Kong)',
			'zh-CN'	=> 'Chinese (Simplified)',
			'zh-TW'	=> 'Chinese (Traditional)',
			'hr'	=> 'Croatian',
			'cs'	=> 'Czech',
			'da'	=> 'Danish',
			'nl'	=> 'Dutch',
			'en-GB'	=> 'English (UK)',
			'en-US'	=> 'English (US)',
			'et'	=> 'Estonian',
			'fil'	=> 'Filipino',
			'fi'	=> 'Finnish',
			'fr'	=> 'French',
			'fr-CA'	=> 'French (Canadian)',
			'gl'	=> 'Galician',
			'de'	=> 'German',
			'el'	=> 'Greek',
			'gu'	=> 'Gujarati',
			'iw'	=> 'Hebrew',
			'hi'	=> 'Hindi',
			'hu'	=> 'Hungarian',
			'is'	=> 'Icelandic',
			'id'	=> 'Indonesian',
			'it'	=> 'Italian',
			'ja'	=> 'Japanese',
			'kn'	=> 'Kannada',
			'ko'	=> 'Korean',
			'lv'	=> 'Latvian',
			'lt'	=> 'Lithuanian',
			'ms'	=> 'Malay',
			'ml'	=> 'Malayalam',
			'mr'	=> 'Marathi',
			'no'	=> 'Norwegian',
			'fa'	=> 'Persian',
			'pl'	=> 'Polish',
			'pt-BR'	=> 'Portuguese (Brazil)',
			'pt-PT'	=> 'Portuguese (Portugal)',
			'ro'	=> 'Romanian',
			'ru'	=> 'Russian',
			'sr'	=> 'Serbian',
			'sk'	=> 'Slovak',
			'sl'	=> 'Slovenian',
			'es'	=> 'Spanish',
			'es-419'	=> 'Spanish (Latin America)',
			'sw'	=> 'Swahili',
			'sv'	=> 'Swedish',
			'ta'	=> 'Tamil',
			'te'	=> 'Telugu',
			'th'	=> 'Thai',
			'tr'	=> 'Turkish',
			'uk'	=> 'Ukrainian',
			'ur'	=> 'Urdu',
			'vi'	=> 'Vietnamese',
			'zu'	=> 'Zulu'
		);

		var $js_locations = array(
			'header' => 'Header',
			'footer' => 'Footer'
		);

		function __construct() {
			natsort ( $this->article_sections );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		}
	
		function admin_init() {
			register_setting( NGFB_SHORTNAME . '_plugin_options', NGFB_OPTIONS_NAME, array( &$this, 'sanitize_options' ) );
		}
	
		function admin_menu() {
			add_options_page( NGFB_FULLNAME . ' Plugin', 'NextGEN Facebook', 'manage_options', NGFB_SHORTNAME, array( &$this, 'options_page' ) );
		}

		function sanitize_options( $opts ) {
			return parent::sanitize_options( $opts );
		}

		function options_page() {
			global $ngfb;
			$buttons_count = 0;
			foreach ( $ngfb->options as $opt => $val )
				if ( preg_match( '/_enable$/', $opt ) )
					$buttons_count++;
	
			?><style type="text/css">
				.form-table tr { vertical-align:top; }
				.form-table th { 
					text-align:right;
					white-space:nowrap; 
					padding:2px 6px 2px 6px; 
					min-width:180px;
				}
				.form-table th.social { 
					font-weight:bold; 
					text-align:left; 
					background-color:#eee; 
					border:1px solid #ccc;
					width:50%;
				}
				.form-table th.metatag { width:220px; }
				.form-table td { padding:2px 6px 2px 6px; }
				.form-table td select,
				.form-table td input { margin:0 0 5px 0; }
				.form-table td input[type=text] { width:250px; }
				.form-table td input[type=text].number { width:50px; }
				.form-table td input[type=text].wide { width:100%; }
				.form-table td input[type=radio] { vertical-align:top; margin:4px 4px 4px 0; }
				.form-table td select { width:250px; }
				.form-table td select.number { width:100px; }
				.wrap { font-size:1em; line-height:1.3em; }
				.wrap h2 { margin:0 0 10px 0; }
				.wrap p { 
					text-align:justify; 
					line-height:1.2em; 
					margin:0 0 10px 0;
				}
				.wrap p.inline { 
					display:inline-block;
					margin:0 0 10px 10px;
				}
				.btn_wizard_column { white-space:nowrap; }
				.btn_wizard_example { display:inline-block; width:155px; }
				.postbox {
					-webkit-border-radius:5px;
					border-radius:5px;
					border:1px solid transparent;
					margin:0 0 10px 0;
				}
				.donatebox {
					float:left;
					display:block;
					font-weight:bold;
					width:350px;
					margin:0 20px 10px 0;
					padding:5px 15px 5px 15px;
					color:#333;
					background:#eeeeff;
					background-image: -webkit-gradient(linear, left bottom, left top, color-stop(7%, #eeeeff), color-stop(77%, #ddddff));
					background-image: -webkit-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image:    -moz-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image:      -o-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image: linear-gradient(to top, #eeeeff 7%, #ddddff 77%);
					-webkit-border-radius:5px;
					border-radius:5px;
					border:1px solid #b4b4b4;
				}
				.donatebox p { 
					font-size:1em;
					line-height:1.25em;
					margin:10px 0 10px 0;
					text-align:center;
				}
				#donate { text-align:center; }
				#message p { text-align:left; }
			</style>
		
			<div class="wrap" id="ngfb">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php echo NGFB_FULLNAME, " Plugin v", $ngfb->version; ?></h2>
	
			<div class="metabox-holder">
			<div class="postbox">
			<div class="inside">	
			<div class="donatebox">
			<p>NextGEN Facebook OG has taken many, many months to develop and fine-tune. Please help us to continue by donating $10 or $20.</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="donate">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
			<p>You can also help by giving NGFB a rating of 5 stars on <a href="http://wordpress.org/support/view/plugin-reviews/nextgen-facebook" target="_blank">WordPress.org</a>.</p>
			</div>

			<p>The NextGEN Facebook OG plugin adds Open Graph meta property tags to all webpage headers, including the artical object type for Posts and Pages. This plugin goes well beyond other plugins I know in handling various archive-type webpages. It will create appropriate title and description meta tags for category, tag, date based archive (day, month, or year), author webpages and search results.</p>
			<p>All plugin settings are optional -- though you may want to enable some social sharing buttons and define a default image for your index webpages (home webpage, category webpage, etc.).</p>
			<p>The images listed in the Open Graph image property tags are chosen in this sequence: A featured image from a NextGEN Gallery (NGG) or WordPress Media Library, NGG [singlepic] shortcodes, NGG &lt;div&gt; HTML tags for images, &lt;img/&gt; HTML tags in the content, the default image defined in the plugin settings. NextGEN Facebook OG will detect images of varying sizes, embedded videos, and include one or more of each in your Open Graph property tags.</p>

			<p>NextGEN Facebook OG is being actively developed and supported. You can review the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/" target="_blank">FAQ</a> and <a href="http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/" target="_blank">Other Notes</a> pages for additional setup information. If you have questions or suggestions, post them on the NextGEN Facebook OG <a href="http://wordpress.org/support/plugin/nextgen-facebook">Support Page</a>. Your comment or suggestion will be answered in a timely manner.</p>

			<div style="clear:both;"></div>
			</div>
			</div>

			<form name="ngfb" method="post" action="options.php">
			<?php
				settings_fields( 'ngfb_plugin_options' );
				$this->hidden( 'ngfb_version', $ngfb->opts_version );
			?>
			<div id="ngfb-ogsettings" class="postbox">
			<h3 class="hndle"><span>Open Graph Settings</span></h3>
			<div class="inside">	
			<table class="form-table">
			<tr>
				<th>Website Topic</th>
				<td><?php $this->select( 'og_art_section', array_merge( array( '' ), $this->article_sections ) ); ?></td>
				<td><p>The topic name that best describes the Posts and Pages on your website. This topic name will be used in the "article:section" Open Graph meta tag for all your webpages. You can leave the topic name blank, if you would prefer not to include an "article:section" meta tag.</p></td>
			</tr>
			<tr>
				<th>Author URL</th>
				<td><?php $this->select( 'og_author_field', $this->author_fields() ); ?></td>
				<td><p>Select the profile field for the Open Graph author property tag. You can use the author's Website, Facebook, Google+, or the index webpage at "<?php echo trailingslashit( site_url() ), 'author/{username}'; ?>". If the selected profile field is empty, then the author's index webpage will be used. The URL should point to an author's <em>personal</em> website or social page. See the Head Link Settings bellow to define a common <em>publisher</em> URL for all webpages.</p></td>
			</tr>
			<tr>
				<th>Default Author</th>
				<td><?php
					echo '<select name="', NGFB_OPTIONS_NAME, '[og_def_author_id]">', "\n";
					echo '<option value="0" ';
					selected( $ngfb->options['og_def_author_id'], 0 );
					echo '>None (default)</option>', "\n";

					foreach ( get_users() as $user ) {
						echo '<option value="', $user->ID, '"';
						selected( $ngfb->options['og_def_author_id'], $user->ID );
						echo '>', $user->display_name, '</option>', "\n";
					}
					echo '</select>', "\n";
				?></td>
				<td><p>A default author for webpages missing authorship information (for example, an index webpage without posts). If you have several authors on your website, you should probably leave this option to <em>None</em> (the default).</p></td>
			</tr>
			<tr>
				<th>Default Author on Indexes</th>
				<td><?php $this->checkbox( 'og_def_author_on_index' ); ?></td>
				<td><p>Check this option if you would like to force the Default Author on index webpages (homepage, archives, categories, author, etc.). If the Default Author is <em>None</em>, then the index webpages will be labeled as a 'webpage' instead of an 'article' (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Default Author on Search Results</th>
				<td><?php $this->checkbox( 'og_def_author_on_search' ); ?></td>
				<td><p>Check this option if you would like to force the Default Author on search result webpages as well. If the Default Author is <em>None</em>, then the search results webpage will be labeled as a 'webpage' instead of an 'article' (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Image Size Name</th>
				<td><?php $this->select_img_size( 'og_img_size' ); ?></td>
				<td><p>The <a href="options-media.php">Media Settings</a> "size name" for the image used in the Open Graph HTML meta tag. Generally this would be "thumbnail" (currently defined as <?php echo get_option('thumbnail_size_w'), 'x', get_option('thumbnail_size_h'), ', ', get_option('thumbnail_crop') == "1" ? "" : "not"; ?> cropped), or another size name like "medium", "large", etc. Choose a size name that is at least <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> or more in width and height, and preferably cropped. You can use the <a href="http://wordpress.org/extend/plugins/simple-image-sizes/" target="_blank">Simple Image Size</a> plugin (or others) to define your own custom size names on the Media Settings admin webpage. I would suggest creating a "facebook-thumbnail" size name of <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> (or larger) cropped, to manage the size of Open Graph images independently from those of your theme.</p></td>
			</tr>
			<tr>
				<th>Default Image ID</th>
				<td><?php 
					$this->input( 'og_def_img_id', 'number' );
					echo ' in the <select name="', NGFB_OPTIONS_NAME, '[og_def_img_id_pre]" style="width:160px;">', "\n";
					echo '<option value="wp" ';
					selected( $ngfb->options['og_def_img_id_pre'], 'wp' );
					echo '>Media Library</option>', "\n";

					if ( $ngfb->is_active['ngg'] ) {
						echo '<option value="ngg" '; 
						selected( $ngfb->options['og_def_img_id_pre'], 'ngg' );
						echo '>NextGEN Gallery</option>', "\n";
					}
					echo '</select>', "\n";
				?></td>
				<td><p>The ID number and location of your default image (example: 123). The ID number in the Media Library can be found from the URL when editing the media (post=123 in the URL, for example). The ID number for an image in a NextGEN Gallery is easier to find -- it's the number in the first column when viewing a Gallery.</p></td>
			</tr>
			<tr>
				<th>Default Image URL</th>
				<td colspan="2"><?php $this->input( 'og_def_img_url', 'wide' ); ?>
				<p>You can specify a Default Image URL (including the http:// prefix) instead of a Default Image ID. This allows you to use an image outside of a managed collection (Media Library or NextGEN Gallery). The image should be at least <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> or more in width and height. If both the Default Image ID and URL are defined, the Default Image ID takes precedence.</p>
				</td>
			</tr>
			<tr>
				<th>Default Image on Indexes</th>
				<td><?php $this->checkbox( 'og_def_img_on_index' ); ?></td>
				<td><p>Check this option if you would like to use the default image on index webpages (homepage, archives, categories, author, etc.). If you leave this unchecked, NextGEN Facebook OG will attempt to use the featured image, NGG [singlepic] shortcodes, NGG &lt;div&gt; HTML tags for images, and &lt;img/&gt; HTML tag within the first entry on the webpage (default is checked).</p></td>
			</tr>
			<tr>
				<th>Default Image on Search Results</th>
				<td><?php $this->checkbox( 'og_def_img_on_search' ); ?></td>
				<td><p>Check this option if you would like to use the default image on search result webpages as well (default is checked).</p></td>
			</tr>
			<?php	if ( $ngfb->is_active['ngg'] ) : ?>
			<tr>
				<th>Add Featured Image Tags</th>
				<td><?php $this->checkbox( 'og_ngg_tags' ); ?></td>
				<td><p>If the <em>featured</em> image in a Post or Page is from a NextGEN Gallery (NGG), then add the NGG image's tags to the Open Graph tag list (default is unchecked).</p></td>
			</tr>
			<?php	else : $this->hidden( 'og_ngg_tags' ); endif; ?>
			<tr>
				<th>Add Page Ancestor Tags</th>
				<td><?php $this->checkbox( 'og_page_parent_tags' ); ?></td>
				<td><p>Add the WordPress tags from the Page ancestors (parent, parent of parent, etc.) to the Open Graph tag list.</p></td>
			</tr>
			<tr>
				<th>Add Page Title as Tag</th>
				<td><?php $this->checkbox( 'og_page_title_tag' ); ?></td>
				<td><p>Add the title of the Page to the Open Graph tag list as well. If the <em>Add Page Ancestor Tags</em> is checked, the titles of ancestor Pages will be added as well. This option works well if the title of your Pages are short and subject-oriented.</p></td>
			</tr>
			<tr>
				<th>Maximum Number of Images</th>
				<td><?php $this->select( 'og_img_max', range( 0, NGFB_MAX_IMG_OG ), 'number' ); ?></td>
				<td><p>The maximum number of images to list in the Open Graph meta property tags -- this includes the featured image, and any images found in the Post or Page content (selecting "0" disables all image property tags).</p></td>
			</tr>
			<tr>
				<th>Maximum Number of Videos</th>
				<td><?php $this->select( 'og_vid_max', range( 0, NGFB_MAX_VID_OG ), 'number' ); ?></td>
				<td><p>The maximum number of videos from the content to use in the Open Graph meta property tags (selecting "0" disables all video property tags).</p></td>
			</tr>
			<tr>
				<th>Maximum Title Length</th>
				<td><?php $this->input( 'og_title_len', 'number' ); ?> Characters</td>
				<td><p>The maximum length of text used in the Open Graph title tag (default is <?php echo $ngfb->default_options['og_title_len']; ?> characters).</p></td>
			</tr>
			<tr>
				<th>Maximum Description Length</th>
				<td><?php $this->input( 'og_desc_len', 'number' ); ?> Characters</td>
				<td><p>The maximum length of text, from your post/page excerpt or content, used in the Open Graph description tag. The length must be <?php echo NGFB_MIN_DESC_LEN; ?> characters or more (default is <?php echo $ngfb->default_options['og_desc_len']; ?>).</p></td>
			</tr>
			<tr>
				<th>Content Begins at First Paragraph</th>
				<td><?php $this->checkbox( 'og_desc_strip' ); ?></td>
				<td><p>For a Page or Post <i>without</i> an excerpt, if this option is checked, the plugin will ignore all text until the first &lt;p&gt; paragraph in <i>the content</i>. If an excerpt exists, then the complete excerpt text is used instead.</p></td>
			</tr>
			<?php	// hide WP-WikiBox option if not installed and activated
				if ( $ngfb->is_active['wikibox'] ) : ?>
			<tr>
				<th>Use WP-WikiBox for Pages</th>
				<td><?php $this->checkbox( 'og_desc_wiki' ); ?></td>
				<td><p>The <a href="http://wordpress.org/extend/plugins/wp-wikibox/" target="_blank">WP-WikiBox</a> plugin has been detected. NextGEN Facebook OG can ignore the content of your Pages when creating the Open Graph description property tag, and retrieve it from Wikipedia instead. This only aplies to Pages - not Posts. Here's how it works: The plugin will check for the Page's tags and use their names to retrieve content from Wikipedia. If no tags are defined, then the Page title will be used to retrieve content. If Wikipedia does not return a summary for the tags or title, then the original content of the Page will be used.</p></td>
			</tr>
			<tr>
				<th>WP-WikiBox Tag Prefix</th>
				<td><?php $this->input( 'og_wiki_tag' ); ?></td>
				<td><p>A prefix to identify WordPress tag names used to retrieve Wikipedia content. Leave this option blank to use all tags associated to a post, or choose a prefix (like "Wiki-") to use only tag names starting with that prefix.</p></td>
			</tr>
			<?php	else : 
					$this->hidden( 'og_desc_wiki' ); 
					$this->hidden( 'og_wiki_tag' ); 
				endif; ?>
			<tr>
				<th>Facebook Admin(s)</th>
				<td><?php $this->input( 'og_admins' ); ?></td>
				<td><p>One or more Facebook account names (generally your own) separated with a comma. When you are viewing your own Facebook wall, your account name is located in the URL (example: https://www.facebook.com/<b>account_name</b>). Enter only the account names, not the URLs. The Facebook Admin names are used by Facebook to allow access to <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data for those accounts.</p></td>
			</tr>
			<tr>
				<th>Facebook App ID</th>
				<td><?php $this->input( 'og_app_id' ); ?></td>
				<td><p>If you have a <a href="https://developers.facebook.com/apps" target="_blank">Facebook Application</a> ID for your website, enter it here. Facebook Application IDs are used by Facebook to allow access to <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data for accounts associated with the Application ID.</p></td>
			</tr>
			</table>
			</div><!-- .inside -->
			</div><!-- .postbox -->
		
			<div id="ngfb-ogtags" class="postbox">
			<h3 class="hndle"><span>Open Graph Meta Tags</span></h3>
			<div class="inside">	
			<table class="form-table">
			<tr>
				<?php $og_cols = 4; ?>
				<?php echo '<td colspan="'.($og_cols * 2).'">'; ?>
				<p>NextGEN Facebook OG will add the following Facebook and Open Graph meta property tags to your webpages. If your theme, or another plugin, already generates one or more of these meta tags, you can uncheck them here to prevent NextGEN Facebook OG from adding duplicate property tags.</p>
				</td>
			</tr>
			<?php
				$og_cells = array();
				$og_rows = array();
				foreach ( $ngfb->default_options as $opt => $val ) {
					if ( preg_match( '/^inc_(.*:.*)$/', $opt, $match ) )
						$og_cells[] = '<th class="metatag">Include '.$match[1].' Meta Tag</th>
							<td>'. $this->checkbox( $opt, false ) . '</td>';
				}
				unset( $opt, $val );
				$og_per_col = ceil( count( $og_cells ) / $og_cols );
				// initialize the array
				foreach ( $og_cells as $num => $cell ) $og_rows[ $num % $og_per_col ] = '';
				// create the html for each row
				foreach ( $og_cells as $num => $cell ) $og_rows[ $num % $og_per_col ] .= $cell;
				unset( $num, $cell );
				foreach ( $og_rows as $num => $row ) 
					echo '<tr>', $row, '</tr>', "\n";
				unset( $num, $row );
			?>
			<tr>
				<th>Include Empty og:* Meta Tags</th>
				<td><?php $this->checkbox( 'og_empty_tags' ); ?></td>
				<td colspan="<?php echo ( $og_cols * 2 ) - 2; ?>"><p>Include meta property tags of type og:* without any content (default is checked).</p></td>
			</tr>
			</table>
			</div><!-- .inside -->
			</div><!-- .postbox -->
		
			<div id="ngfb-head" class="postbox">
			<h3 class="hndle"><span>Additional Head Settings</span></h3>
			<div class="inside">	
			<table class="form-table">
			<tr>
				<td colspan="3">
				<p>NextGEN Facebook OG can also include an <em>author</em> and <em>publisher</em> link in your webpage headers. These are used by Google Search to associate a Google+ profile to search results. If you have a Google+ <em>Page</em> for your website, you may use it's URL as the publisher link. As an example, the publisher link for <a href="http://surniaulula.com/" target="_blank">Surnia Ulula</a> is <a href="https://plus.google.com/b/100429778043098222378/100429778043098222378/posts" target="_blank">https://plus.google.com/b/100429778043098222378/100429778043098222378/posts</a>. The publisher link takes precedence over the author link in search results.</p>
				</td>
			</tr>
			<tr>
				<th>Author Link URL</th>
				<td colspan="2"><?php $this->select( 'link_author_field', $this->author_fields() ); ?></td>
			</tr>
			<tr>
				<th>Publisher Link URL</th>
				<td colspan="2"><?php $this->input( 'link_publisher_url', 'wide' ); ?></td>
			</tr>
			<tr>
				<th>Add a Meta Description Tag</th>
				<td><?php $this->checkbox( 'add_meta_desc' ); ?></td>
				<td width="100%"><p>Add a meta description tag to the webpage head section (the value is identical to the Open Graph description).</p></td>
			</tr>
			</table>
			</div><!-- .inside -->
			</div><!-- .postbox -->

			<div id="ngfb-buttons" class="postbox">
			<h3 class="hndle"><span>Social Button Settings</span></h3>
			<div class="inside">	
			<table class="form-table">
			<tr>
				<td colspan="4">
				<p>NextGEN Facebook OG uses the "ngfb-buttons" CSS class name to wrap all social buttons, and each button has it's own individual class name as well. <a href="http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/" target="_blank">Refer to the NextGEN Facebook OG Other Notes page for stylesheet examples</a> -- including how to hide the buttons for specific Posts, Pages, categories, tags, etc. Each of the following social buttons can be added to an "NGFB Social Buttons" widget as well (<a href="widgets.php">see the widgets admin webpage for the widget options</a>).</p>
				</td>
			</tr>
			<tr>
				<th>Include on Index Webpages</th>
				<td><?php $this->checkbox( 'buttons_on_index' ); ?></td>
				<td colspan="2"><p>Add the social buttons enabled bellow, to each entry's content on index webpages (index, archives, author, etc.).</p></td>
			</tr>
			<?php	// hide Add to Excluded Pages option if not installed and activated
				if ( $ngfb->is_active['expages'] ) : ?>
			<tr>
				<th>Add to Excluded Pages</th>
				<td><?php $this->checkbox( 'buttons_on_ex_pages' ); ?></td>
				</td><td colspan="2"><p>The <a href="http://wordpress.org/extend/plugins/exclude-pages/" target="_blank">Exclude Pages</a> plugin has been detected. By default, social buttons are not added to excluded Pages. You can over-ride the default and add social buttons to excluded Page content by selecting this option.</p></td>
			</tr>
			<?php	else : $this->hidden( 'buttons_on_ex_pages' ); endif; ?>
			<tr>
				<th>Location in Content Text</th>
				<td><?php $this->select( 'buttons_location', array( 'top' => 'Top', 'bottom' => 'Bottom' ) ); ?></td>
			</tr>
			<tr>
				<th>Language</th>
				<td><?php $this->select( 'buttons_lang', $this->locale ); ?></td>
				<td colspan="2"><p>Prefered language for social buttons text (when supported).</p></td>
			</tr>
			</table>
			<table class="form-table">
			<tr>
				<!-- Facebook -->
				<th colspan="2" class="social">Facebook</th>
				<!-- Google+ -->
				<th colspan="2" class="social">Google+</th>
			</tr>
			<tr><td style="height:5px;"></td></tr>
			<tr>
				<!-- Facebook -->
				<th>Add Button to Content</th>
				<td><?php $this->checkbox( 'fb_enable' ); ?></td>
				<!-- Google+ -->
				<th>Add Button to Content</th>
				<td><?php $this->checkbox( 'gp_enable' ); ?></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'fb_order', range( 1, $buttons_count ), 'number' ); ?></td>
				<!-- Google+ -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'gp_order', range( 1, $buttons_count ), 'number' ); ?></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>JavaScript in</th>
				<td><?php $this->select( 'fb_js_loc', $this->js_locations ); ?></td>
				<!-- Google+ -->
				<th>JavaScript in</th>
				<td><?php $this->select( 'gp_js_loc', $this->js_locations ); ?></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Include Send Button</th>
				<td><?php $this->checkbox( 'fb_send' ); ?></td>
				<!-- Google+ -->
				<th>Button Type</th>
				<td><?php $this->select( 'gp_action', array( 
					'plusone' => 'G +1',
					'share' => 'G+ Share',
				) ); ?></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Button Layout</th>
				<td><?php $this->select( 'fb_layout', array( 
					'standard' => 'Standard',
					'button_count' => 'Button Count',
					'box_count' => 'Box Count' ) ); ?></td>
				<!-- Google+ -->
				<th>Button Size</th>
				<td><?php $this->select( 'gp_size', array( 
					'small' => 'Small [ 15px ]',
					'medium' => 'Medium [ 20px ]',
					'standard' => 'Standard [ 24px ]',
					'tall' => 'Tall [ 60px ]' ) ); ?></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Default Width</th>
				<td><?php $this->input( 'fb_width', 'number' ); ?></td>
				<!-- Google+ -->
				<th>Annotation</th>
				<td><?php $this->select( 'gp_annotation', array( 
					'inline' => 'Inline',
					'bubble' => 'Bubble',
					'vertical-bubble' => 'Vertical Bubble',
					'none' => 'None' ) ); ?></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Show Faces</th>
				<td><?php $this->checkbox( 'fb_show_faces' ); ?></td>
				<!-- Google+ -->
				<td colspan="2"></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Button Font</th>
				<td><?php $this->select( 'fb_font', array( 
					'arial' => 'Arial',
					'lucida grande' => 'Lucida Grande',
					'segoe ui' => 'Segoe UI',
					'tahoma' => 'Tahoma',
					'trebuchet ms' => 'Trebuchet MS',
					'verdana' => 'Verdana' ) ); ?></td>
				<!-- Google+ -->
				<td colspan="2"></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Button Color Scheme</th>
				<td><?php $this->select( 'fb_colorscheme', array( 
					'light' => 'Light',
					'dark' => 'Dark' ) ); ?></td>
				<!-- Google+ -->
				<td colspan="2"></td>
			</tr>				
			<tr>
				<!-- Facebook -->
				<th>Facebook Action Name</th>
				<td><?php $this->select( 'fb_action', array( 
					'like' => 'Like',
					'recommend' => 'Recommend' ) ); ?></td>
				<!-- Google+ -->
				<td colspan="2"></td>
			</tr>				
			<tr><td style="height:5px;"></td></tr>
			<tr>
				<!-- LinkedIn -->
				<th colspan="2" class="social">LinkedIn</th>
				<!-- Twitter -->
				<th colspan="2" class="social">Twitter</th>
			</tr>
			<tr><td style="height:5px;"></td></tr>
			<tr>
				<!-- LinkedIn -->
				<th>Add Button to Content</th>
				<td><?php $this->checkbox( 'linkedin_enable' ); ?></td>
				<!-- Twitter -->
				<th>Add Button to Content</th>
				<td><?php $this->checkbox( 'twitter_enable' ); ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'linkedin_order', range( 1, $buttons_count ), 'number' ); ?></td>
				<!-- Twitter -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'twitter_order', range( 1, $buttons_count ), 'number' ); ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<th>JavaScript in</th>
				<td><?php $this->select( 'linkedin_js_loc', $this->js_locations ); ?></td>
				<!-- Twitter -->
				<th>JavaScript in</th>
				<td><?php $this->select( 'twitter_js_loc', $this->js_locations ); ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<th>Counter Mode</th>
				<td><?php $this->select( 'linkedin_counter', array( 
					'right' => 'Horizontal',
					'top' => 'Vertical',
					'none' => 'None' ) ); ?></td>
				<!-- Twitter -->
				<th>Count Box Position</th>
				<td><?php $this->select( 'twitter_count', array( 
					'horizontal' => 'Horizontal',
					'vertical' => 'Vertical',
					'none' => 'None' ) ); ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<th>Show Zero in Counter</th>
				<td><?php $this->checkbox( 'linkedin_showzero' ); ?></td>
				<!-- Twitter -->
				<th>Button Size</th>
				<td><?php $this->select( 'twitter_size', array( 
					'medium' => 'Medium',
					'large' => 'Large' ) ); ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<td colspan="2"></td>
				<!-- Twitter -->
				<th>Do Not Track</th>
				<td><?php $this->checkbox( 'twitter_dnt' ); ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<td colspan="2"></td>
				<!-- Twitter -->
				<th>Shorten URLs</th>
				<td><?php $this->checkbox( 'twitter_shorten' ); ?><p class="inline">See the Goo.gl API Key option in the Plugin Settings.</p></td>
			</tr>
			<tr><td style="height:5px;"></td></tr>
			<tr>
				<!-- Pinterest -->
				<th colspan="2" class="social">Pinterest</th>
				<!-- tumblr -->
				<th colspan="2" class="social">tumblr</th>
			</tr>
			<tr><td style="height:5px;"></td></tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"><p>The Pinterest "Pin It" button will only appear on Posts and Pages with a featured image.</p></td>
				<!-- tumblr -->
				<td colspan="2"><p>The tumblr button shares featured images (when the option is checked), embedded videos, quote post formats, and links to webpages.</p></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>Add Button to Content</th>
				<td><?php $this->checkbox( 'pin_enable' ); ?></td>
				<!-- tumblr -->
				<th>Add Button to Content</th>
				<td><?php $this->checkbox( 'tumblr_enable' ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'pin_order', range( 1, $buttons_count ), 'number' ); ?></td>
				<!-- tumblr -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'tumblr_order', range( 1, $buttons_count ), 'number' ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>JavaScript in</th>
				<td><?php $this->select( 'pin_js_loc', $this->js_locations ); ?></td>
				<!-- tumblr -->
				<th>JavaScript in</th>
				<td><?php $this->select( 'tumblr_js_loc', $this->js_locations ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>Pin Count Layout</th>
				<td><?php $this->select( 'pin_count_layout', array( 
					'horizontal' => 'Horizontal',
					'vertical' => 'Vertical',
					'none' => 'None' ) ); ?></td>
				<!-- tumblr -->
				<th rowspan="4">tumblr Button Style</th>
				<td rowspan="4">
					<div class="btn_wizard_row clearfix" id="button_styles">
					<?php
						foreach ( range( 1, 4 ) as $i ) {
							echo '<div class="btn_wizard_column share_', $i, '">';
							foreach ( array( '', 'T' ) as $t ) {
								echo '
									<div class="btn_wizard_example clearfix">
										<label for="share_', $i, $t, '">
											<input type="radio" id="share_', $i, $t, '" 
												name="', NGFB_OPTIONS_NAME, '[tumblr_button_style]" 
												value="share_', $i, $t, '" ', 
												checked( 'share_'.$i.$t, $ngfb->options['tumblr_button_style'], false ), '/>
											<img src="http://platform.tumblr.com/v1/share_', $i, $t, '.png" 
												height="20" class="share_button_image"/>
										</label>
									</div>
								';
							}
							echo '</div>';
						}
					?>
					</div> 
				</td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>Featured Image Size to Share</th>
				<td><?php $this->select_img_size( 'pin_img_size' ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>Image Caption Text</th>
				<td><?php $this->select( 'pin_caption', array( 
					'title' => 'Title Only',
					'excerpt' => 'Excerpt Only',
					'both' => 'Title and Excerpt',
					'none' => 'None' ) ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>Maximum Caption Length</th>
				<td><?php $this->input( 'pin_cap_len', 'number' ); ?> Characters</td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"></td>
				<!-- tumblr -->
				<th>Maximum <u>Link</u> Description Length</th>
				<td><?php $this->input( 'tumblr_desc_len', 'number' ); ?> Characters</td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"></td>
				<!-- tumblr -->
				<th>Prioritize Featured Image</th>
				<td><?php $this->checkbox( 'tumblr_photo' ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"></td>
				<!-- tumblr -->
				<th>Featured Image Size to Share</th>
				<td><?php $this->select_img_size( 'tumblr_img_size' ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"></td>
				<!-- tumblr -->
				<th>Image and Video Caption Text</th>
				<td><?php $this->select( 'tumblr_caption', array( 
					'title' => 'Title Only',
					'excerpt' => 'Excerpt Only',
					'both' => 'Title and Excerpt',
					'none' => 'None' ) ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"></td>
				<!-- tumblr -->
				<th>Maximum Caption Length</th>
				<td><?php $this->input( 'tumblr_cap_len', 'number' ); ?> Characters</td>
			</tr>
			<tr><td style="height:5px;"></td></tr>
			<tr>
				<!-- StumbleUpon -->
				<th colspan="2" class="social">StumbleUpon</th>
			</tr>
			<tr><td style="height:5px;"></td></tr>
			<tr>
				<!-- StumbleUpon -->
				<th>Add Button to Content</th>
				<td><?php $this->checkbox( 'stumble_enable' ); ?></td>
			</tr>
			<tr>
				<!-- StumbleUpon -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'stumble_order', range( 1, $buttons_count ), 'number' ); ?></td>
			</tr>
			<tr>
				<!-- StumblrUpon -->
				<th>JavaScript in</th>
				<td><?php $this->select( 'stumble_js_loc', $this->js_locations ); ?></td>
			</tr>
			<tr>
				<!-- StumbleUpon -->
				<th>StumbleUpon Badge</th>
				<td>
					<style type="text/css">
						.badge { 
							display:block;
							background: url("http://b9.sustatic.com/7ca234_0mUVfxHFR0NAk1g") no-repeat transparent; 
							width:130px;
							margin:0 0 10px 0;
						}
						.badge-col-left { display:inline-block; float:left; }
						.badge-col-right { display:inline-block; }
						#badge-1 { height:60px; background-position:50% 0px; }
						#badge-2 { height:30px; background-position:50% -100px; }
						#badge-3 { height:20px; background-position:50% -200px; }
						#badge-4 { height:60px; background-position:50% -300px; }
						#badge-5 { height:30px; background-position:50% -400px; }
						#badge-6 { height:20px; background-position:50% -500px; }
					</style>
					<?php
						foreach ( range( 1, 6 ) as $i ) {
							switch ( $i ) {
								case '1' : echo '<div class="badge-col-left">', "\n"; break;
								case '4' : echo '</div><div class="badge-col-right">', "\n"; break;
							}
							echo '<div class="badge" id="badge-', $i, '">', "\n";
							echo '<input type="radio" name="', NGFB_OPTIONS_NAME, '[stumble_badge]" value="', $i, '" ', 
								checked( $i, $ngfb->options['stumble_badge'], false ), '/>', "\n";
							echo '</div>', "\n";
							switch ( $i ) { case '6' : echo '</div>', "\n"; break; }
						}
					?>
				</td>
			</tr>
			</table>
			</div><!-- .inside -->
			</div><!-- .postbox -->
		
			<div id="ngfb-plugin" class="postbox">
			<h3 class="hndle"><span>Plugin Settings</span></h3>
			<div class="inside">	
			<table class="form-table">
			<tr>
				<th>Reset Settings on Activate</th>
				<td><?php $this->checkbox( 'ngfb_reset' ); ?></td>
				<td><p>Check this option to reset NextGEN Facebook OG settings to their default values <u>when you deactivate, and then reactivate the plugin</u>.</p></td>
			</tr>
			<tr>
				<th>Add Hidden Debug Info</th>
				<td><?php $this->checkbox( 'ngfb_debug' ); ?></td>
				<td><p>Include hidden debug information with the Open Graph meta tags.</p></td>
			</tr>
			<tr>
				<th>Cache Expiry in Hours</th>
				<td><?php $this->select( 'ngfb_cache_hours', range( 0, NGFB_MAX_CACHE ), 'number' ); ?></td>
				<td><p>Save social button images and JavaScript to a cache folder and provide URLs to these cached files instead of the originals. A value of 0 hours (the default) disables this option. Caching should only be enabled if your infrastructure can deliver these files faster and more reliably than the original websites.</p><p>Note: Caching remote content works with all social buttons except for the Facebook JavaScript SDK, which cannot be cached (for now). All other social button images and JavaScript files will be cached in <?php echo NGFB_CACHEDIR; ?>.</p></td>
			</tr>
			<tr>
				<th>Verify SSL Certificates</th>
				<td><?php $this->checkbox( 'ngfb_verify_certs' ); ?></td>
				<td><p>Verify the peer SSL certificate when fetching cache content by HTTPS. Note: PHP curl will use the <?php echo NGFB_PEM_FILE; ?> certificate file by default. You may want define the NGFB_PEM_FILE constant in your wp-config.php file to use an alternate certificate file.</p></td>
			</tr>
			<tr>
				<th>Apply Content Filter</th>
				<td><?php $this->checkbox( 'ngfb_filter_content' ); ?></td>
				<td><p>When NextGEN Facebook OG generates the Open Graph meta tags, it applies the Wordpress filters on the content text to expand shortcodes etc. In most cases this is fine, even desirable, but in a few rare cases, it may break another plugin. You can prevent NextGEN Facebook OG from applying the Wordpress filters by un-checking this option. If you do, NextGEN Facebook OG may not have access to the complete content text (if your content includes some shortcodes, for example), and may generate inaccurate Open Graph description or image meta property tags.</p></td>
			</tr>
			<tr>
				<th>Apply Excerpt Filter</th>
				<td><?php $this->checkbox( 'ngfb_filter_excerpt' ); ?></td>
				<td><p>There should be no need to filter the excerpt text, but the option is here if you need it.</p></td>
			</tr>
			<tr>
				<th>Ignore Small Images</th>
				<td><?php $this->checkbox( 'ngfb_skip_small_img' ); ?></td>
				<td><p>NextGEN Facebook OG will attempt to include images from the &lt;img/&gt; HTML tags it finds in the content (along with the featured image, NGG [singlepic], or NGG &lt;div&gt; HTML tags for images). The &lt;img/&gt; HTML tags must have a width and height attribute, and their size must be equal to or larger than the Image Size Name you've selected. You can uncheck this option to include smaller images from the content, or refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/">NextGEN Facebook OG FAQ</a> webpage for additional solutions.</p></td>
			</tr>
			<tr>
				<th>Goo.gl Simple API Access Key</th>
				<td colspan="2"><?php $this->input( 'ngfb_googl_api_key', 'wide' ); ?>
				<p>The Google URL Shortener API Key for this website / project (currently optional). If you don't already have one, visit Google's <a href="https://developers.google.com/url-shortener/v1/getting_started#APIKey" target="_blank">acquiring and using an API Key</a> documentation, and follow the directions to acquire your <em>Simple API Access Key</em>.</p></td>
			</tr>
			</table>
			</div><!-- .inside -->
			</div><!-- .postbox -->
		
			<input type="submit" class="button-primary" value="Save Changes" />
			</form>
			</div><!-- .metabox-holder -->
			</div><!-- .wrap -->
			<?php	
		}
	
		function select( $name, $values = array(), $class = '', $id = '' ) {
			if ( empty( $name ) ) return;	// just in case
			global $ngfb;
			$is_assoc = $ngfb->is_assoc( $values );
			echo '<select name="', NGFB_OPTIONS_NAME, '[', $name, ']"',
				( empty( $class ) ? '' : ' class="'.$class.'"' ),
				( empty( $id ) ? '' : ' id="'.$id.'"' ), '>', "\n";
			foreach ( (array) $values as $val => $desc ) {
				if ( ! $is_assoc ) $val = $desc;
				echo '<option value="', $val, '"';
				selected( $ngfb->options[$name], $val );
				echo '>', $desc;
				if ( $desc === '' ) echo 'None';
				if ( $val == $ngfb->default_options[$name] ) echo ' (default)';
				echo '</option>', "\n";
			}
			echo '</select>';
		}

		function checkbox( $name, $echo = true, $check = array( '1', '0' ) ) {
			if ( empty( $name ) ) return;	// just in case
			global $ngfb;
			$input = '<input type="checkbox" name="' . NGFB_OPTIONS_NAME . '[' . $name . ']" value="' . $check[0] . '"' .
				checked( $ngfb->options[$name], $check[0], false ) . ' title="Default is ' .
				( $ngfb->default_options[$name] == $check[0] ? 'Checked' : 'Unchecked' ) . '" />';
			if ( $echo ) echo $input;
			else return $input;
		}

		function input( $name, $class = '', $id = '' ) {
			if ( empty( $name ) ) return;	// just in case
			global $ngfb;
			echo '<input type="text" name="', NGFB_OPTIONS_NAME, '[', $name, ']"',
				( empty( $class ) ? '' : ' class="'.$class.'"' ),
				( empty( $id ) ? '' : ' id="'.$id.'"' ),
				' value="', $ngfb->options[$name], '" />';
		}

		function hidden( $name, $value = '' ) {
			if ( empty( $name ) ) return;	// just in case
			global $ngfb;
			$value = empty( $value ) ? $ngfb->options[$name] : $value;
			echo '<input type="hidden" name="', NGFB_OPTIONS_NAME, '[', $name, ']"',
				' value="', $value, '" />';
		}

		function select_img_size( $name ) {
			if ( empty( $name ) ) return;	// just in case
			global $ngfb;
			global $_wp_additional_image_sizes;
			$size_names = get_intermediate_image_sizes();
			natsort( $size_names );
			echo '<select name="', NGFB_OPTIONS_NAME, '[', $name, ']">', "\n";
			foreach ( $size_names as $size_name ) {
				if ( is_integer( $size_name ) ) continue;
				$size = $ngfb->get_size_values( $size_name );
				echo '<option value="', $size_name, '" ';
				selected( $ngfb->options[$name], $size_name );
				echo '>', $size_name, ' [ ', $size['width'], 'x', $size['height'], $size['crop'] ? " cropped" : "", ' ]';
				if ( $size_name == $ngfb->default_options[$name] ) echo ' (default)';
				echo '</option>', "\n";
			}
			unset ( $size_name );
			echo '</select>', "\n";
		}

		function author_fields() {
			global $ngfb;
			return $ngfb->user_contactmethods( 
				array( 'none' => 'None', 'author' => 'Author Index', 'url' => 'Website' ) 
			);
		}
	}
}
?>
