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

	class ngfbAdmin {
	
		// list from http://en.wikipedia.org/wiki/Category:Websites_by_topic
		var $article_sections = array(
			'',
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
	
		function __construct() {
			natsort ( $this->article_sections );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
		}
	
		function admin_init() {
			register_setting( 'ngfb_plugin_options', 'ngfb_options', array( &$this, 'validate_options' ) );
		}
	
		function admin_menu() {
			add_options_page('NextGEN Facebook OG Plugin', 'NextGEN Facebook', 'manage_options', 'ngfb', array( &$this, 'options_page' ) );
		}
	
		// sanitize and validate input
		function validate_options( $opts ) {
			global $ngfb;
			return $ngfb->validate_options( $opts );
		}

		function options_page() {
			global $ngfb;
			$buttons_count = 0;
			foreach ( $ngfb->options as $opt => $val )
				if ( preg_match( '/_enable$/', $opt ) )
					$buttons_count++;
	
			?><style type="text/css">
				.form-table tr { vertical-align:top; }
				.form-table td { padding:2px 6px 2px 6px; }
				.form-table th { text-align:right; white-space:nowrap; padding:2px 6px 2px 6px; width:180px; }
				.form-table th.social { font-weight:bold; text-align:left; background-color:#eee; border:1px solid #ccc; }
				.form-table th.metatag { width:220px; }
				.form-table td select,
				.form-table td input { margin:0 0 5px 0; }
				.form-table td input[type=text] { width:250px; }
				.form-table td input[type=text].number { width:50px; }
				.form-table td input[type=radio] { vertical-align:top; margin:4px 4px 4px 0; }
				.form-table td select { width:250px; }
				.form-table td select.order { width:100px; }
				.form-table td select.yesno { width:100px; }
				.wrap { font-size:1em; line-height:1.3em; }
				.wrap h2 { margin:0 0 10px 0; }
				.wrap p { text-align:justify; line-height:1.3em; margin:5px 0 5px 0; }
				.btn_wizard_column { white-space:nowrap; }
				.btn_wizard_example { display:inline-block; width:155px; }
			</style>
			<div class="wrap" id="ngfb">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2>NextGEN Facebook OG Plugin</h2>
	
			<p>NextGEN Facebook OG plugin provides <a href="http://ogp.me/" target="_blank">Open Graph</a> HTML meta tags for all your webpages. If your post or page has a featured image, from a NextGEN Gallery or Media Library, it will be included in the OG meta tags. All plugin settings are optional -- though you may want to enable some social sharing buttons and define a default image for your index webpages (home page, category page, etc.).</p>
	
			<p>The image used in the Open Graph HTML meta tags will be determined in this sequence: A featured image from a NextGEN Gallery or WordPress Media Library, the first NextGEN [singlepic] shortcode or IMG HTML tag in the content, and finally, the default image defined here. If none of these conditions can be satisfied, then the OG image meta tag will be left out.</p>
	
			<div class="updated" style="margin:10px 0;">
			<p style="text-align:center">If you appreciate the NextGEN Facebook OG plugin, please take a moment to <a href="http://wordpress.org/support/view/plugin-reviews/nextgen-facebook?rate=5#postform"><strong>encourage us and rate it</strong></a> on the WordPress website. Thank you.</p>
			</div>
		
			<div class="metabox-holder">
			<form name="ngfb" method="post" action="options.php">
			<?php settings_fields('ngfb_plugin_options'); ?>
		
			<div id="ngfb-ogsettings" class="postbox">
			<h3 class="hndle"><span>Open Graph Settings</span></h3>
			<div class="inside">	
			<table class="form-table">
			<tr>
				<th>Website Topic</th>
				<td><?php $this->select( 'og_art_section', $this->article_sections ); ?></td>
				<td><p>The topic name that best describes the posts and pages on your website. This topic name will be used in the "article:section" Open Graph meta tag for all your webpages. You can leave the topic name blank, if you would prefer not to include an "article:section" meta tag.</p></td>
			</tr>
			<tr>
				<th>Image Size Name</th>
				<td><?php $this->select_img_size( 'og_img_size' ); ?></td>
				<td><p>The <a href="options-media.php">Media Settings</a> "size name" for the image used in the Open Graph HTML meta tag. Generally this would be "thumbnail" (currently defined as <?php echo get_option('thumbnail_size_w'), 'x', get_option('thumbnail_size_h'), ', ', get_option('thumbnail_crop') == "1" ? "" : "not"; ?> cropped), or another size name like "medium", "large", etc. Choose a size name that is at least <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> or more in width and height, and preferably cropped. You can use the <a href="http://wordpress.org/extend/plugins/simple-image-sizes/" target="_blank">Simple Image Size</a> plugin (or others) to define your own custom size names on the Media Settings admin page. I would suggest creating a "facebook-thumbnail" size name of <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> (or larger) cropped, to manage the size of Open Graph images independently from those of your theme.</p></td>
			</tr>
			<tr>
				<th>Default Image ID</th>
				<td><input type="text" name="ngfb_options[og_def_img_id]" class="number"
					value="<?php echo $ngfb->options['og_def_img_id']; ?>" /> in the
					<select name='ngfb_options[og_def_img_id_pre]' style="width:150px;">
						<option value='' <?php selected($ngfb->options['og_def_img_id_pre'], ''); ?>>Media Library</option>
						<?php	if ( method_exists( 'nggdb', 'find_image' ) ): ?>
						<option value='ngg' <?php selected($ngfb->options['og_def_img_id_pre'], 'ngg'); ?>>NextGEN Gallery</option>
						<?php	endif; ?>
					</select>
				</td><td>
				<p>The ID number and location of your default image (example: 123). The ID number in the Media Library can be found from the URL when editing the media (post=123 in the URL, for example). The ID number for an image in a NextGEN Gallery is easier to find -- it's the number in the first column when viewing a Gallery.</p>
				</td>
			</tr>
			<tr>
				<th>Default Image URL</th>
				<td colspan="2"><input type="text" name="ngfb_options[og_def_img_url]"
					value="<?php echo $ngfb->options['og_def_img_url']; ?>" style="width:100%;"/>
				<p>You can specify a Default Image URL (including the http:// prefix) instead of a Default Image ID. This allows you to use an image outside of a managed collection (Media Library or NextGEN Gallery). The image should be at least <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> or more in width and height. If both the Default Image ID and URL are defined, the Default Image ID takes precedence.</p>
				</td>
			</tr>
			<tr>
				<th>Default Image on Indexes</th>
				<td><?php $this->checkbox( 'og_def_img_on_index' ); ?></td>
				<td><p>Check this box if you would like to use the default image on index webpages (homepage, archives, categories, author, etc.). If you leave this unchecked, NextGEN Facebook OG will attempt to use the first featured image, [singlepic] shortcode, or IMG HTML tag within the list of entries on the webpage. The default is checked.</p></td>
			</tr>
			<tr>
				<th>Default Image on Search Results</th>
				<td><?php $this->checkbox( 'og_def_img_on_search' ); ?></td>
				<td><p>Check this box if you would like to use the default image on search result webpages as well. The default is checked.</p></td>
			</tr>
			<?php	if ( method_exists( 'nggdb', 'find_image' ) ): ?>
			<tr>
				<th>Add NextGEN Gallery Tags</th>
				<td><?php $this->checkbox( 'og_ngg_tags' ); ?></td>
				<td><p>If the featured or default image is from a NextGEN Gallery, then add the image's tags to the Open Graph tag list. Default is unchecked.</p></td>
			</tr>
			<?php	endif; ?>
			<tr>
				<th>Author URL</th>
				<td><?php
					$author_url = array(
						'index' => 'Author Index Webpage',
						'website' => 'Profile Website',
					);
					if ( class_exists( 'GPAISRProfile' ) )
						$author_url['gplus_link'] = 'Google+ Profile';
					$this->select( 'og_author_url', $author_url ); ?></td>
				<td><p>Select the URL to use in the Open Graph author meta tag. The default is the author's index webpage at "<?php echo trailingslashit( site_url() ), 'author/{username}'; ?>". You can also use the author's website in their profile -- if the website field is empty, then the author's index webpage will be used instead. 
				<?php if ( class_exists( 'GPAISRProfile' ) ): ?>
				The <a href="http://wordpress.org/extend/plugins/google-author-information-in-search-results-wordpress-plugin/" target="_blank">Google Plus Author Information in Search Result (GPAISR)</a> plugin has been detected. You may choose to use the author's Google+ profile in the Open Graph author meta tag.
				<?php endif; ?>
				</p></td>
			</tr>
			<tr>
				<th>Default Author</th>
				<td><select name="ngfb_options[og_def_author_id]">
					<option value=""<?php selected( $ngfb->options['og_def_author_id'], '' ); ?>>None (default)</option>
					<?php $users = get_users( $query_args );
						foreach ( (array) $users as $user ) 
							echo '<option value="', $user->ID, '"', 
								selected( $ngfb->options['og_def_author_id'], $user->ID, false ), '>', 
								$user->display_name, '</option>', "\n";
					?>
				</select></td>
				<td><p>A default author for webpages missing authorship information (for example, an index webpage without posts). If you have several authors on your website, you should probably leave this option to None (the default).</p></td>
			</tr>
			<tr>
				<th>Max Title Length</th>
				<td><input type="text" name="ngfb_options[og_title_len]" class="number"
					value="<?php echo $ngfb->options['og_title_len']; ?>" /> Characters
				</td><td>
				<p>The maximum length of text used in the Open Graph title tag (default is 100 characters).</p>
				</td>
			</tr>
			<tr>
				<th>Max Description Length</th>
				<td><input type="text" name="ngfb_options[og_desc_len]" class="number"
					value="<?php echo $ngfb->options['og_desc_len']; ?>" /> Characters
				</td><td>
				<p>The maximum length of text, from your post/page excerpt or content, used in the Open Graph description tag. The length must be <?php echo NGFB_MIN_DESC_LEN; ?> characters or more (default is 300).</p>
				</td>
			</tr>
			<tr>
				<th>Content Begins at First Paragraph</th>
				<td><?php $this->checkbox( 'og_desc_strip' ); ?></td>
				<td><p>For a page or post <i>without</i> an excerpt, if this option is checked, the plugin will ignore all text until the first &lt;p&gt; paragraph in <i>the content</i>. If an excerpt exists, then it's complete text will be used instead.</p></td>
			</tr>
			<?php	// hide WP-WikiBox option if not installed and activated
				if ( function_exists( 'wikibox_summary' ) ): ?>
			<tr>
				<th>Use WP-WikiBox for Pages</th>
				<td><input name="ngfb_options[og_desc_wiki]" type="checkbox" value="1" 
					<?php checked(1, $ngfb->options['og_desc_wiki']); ?> />
				</td><td>
				<p>The <a href="http://wordpress.org/extend/plugins/wp-wikibox/" target="_blank">WP-WikiBox</a> plugin has been detected. NextGEN Facebook OG can ignore the content of your pages when creating the description Open Graph meta tag, and retrieve it from Wikipedia instead. This only aplies to pages - not posts. Here's how it works: The plugin will check for the page's tags and use their names to retrieve content from Wikipedia. If no tags are defined, then the page title will be used. If Wikipedia does not return a summary for the tags or title, then the original content of the page will be used.</p>
				</td>
			</tr>
			<tr>
				<th>WP-WikiBox Tag Prefix</th>
				<td><input type="text" name="ngfb_options[og_wiki_tag]" 
					value="<?php echo $ngfb->options['og_wiki_tag']; ?>" />
				</td><td>
				<p>A prefix to identify WordPress tag names used to retrieve Wikipedia content. Leave this option blank to use all tags associated to a post, or choose a prefix (like "Wiki-") to use only tag names starting with that prefix.</p>
				</td>
			</tr>
			<?php	endif; ?>
			<tr>
				<th>Facebook Admin(s)</th>
				<td><input type="text" name="ngfb_options[og_admins]" 
					value="<?php echo $ngfb->options['og_admins']; ?>" />
				</td><td>
				<p>One or more Facebook account names (generally your own) separated with a comma. When you are viewing your own Facebook wall, your account name is located in the URL (example: https://www.facebook.com/<b>account_name</b>). Enter only the account names, not the URLs. The Facebook Admin names are used by Facebook to allow access to <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data for those accounts.</p>
				</td>
			</tr>
			<tr>
				<th>Facebook App ID</th>
				<td><input type="text" name="ngfb_options[og_app_id]" 
					value="<?php echo $ngfb->options['og_app_id']; ?>" />
				</td><td>
				<p>If you have a <a href="https://developers.facebook.com/apps" target="_blank">Facebook Application</a> ID for your website, enter it here. Facebook Application IDs are used by Facebook to allow access to <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data for accounts associated with the Application ID.</p>
				</td>
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
				<p>NextGEN Facebook OG will add all known Facebook and Open Graph meta tags to your webpages. If your theme, or another plugin, already generates one or more of these meta tags, you may uncheck them here to prevent NextGEN Facebook OG from adding duplicate meta tags.</p>
				</td>
			</tr>
			<?php
				$og_cells = array();
				$og_rows = array();
		
				foreach ( $ngfb->options as $opt => $val ) {
					if ( preg_match( '/^inc_(.*)$/', $opt, $match ) )
						$og_cells[] = '<th class="metatag">Include '.$match[1].' Meta Tag</th>
							<td><input name="ngfb_options['.$opt.']" type="checkbox" 
								value="1" '.checked(1, $ngfb->options[$opt], false).'/></td>';
				}
				unset( $opt, $val );
		
				$og_per_col = ceil( count( $og_cells ) / $og_cols );
		
				foreach ( $og_cells as $num => $cell ) 
					$og_rows[ $num % $og_per_col ] .= $cell;
				unset( $num, $cell );
		
				foreach ( $og_rows as $num => $row ) 
					echo '<tr>', $row, '</tr>', "\n";
				unset( $num, $row );
			?>
			</table>
			</div><!-- .inside -->
			</div><!-- .postbox -->
		
			<div id="ngfb-socialbuttons" class="postbox">
			<h3 class="hndle"><span>Social Button Settings</span></h3>
			<div class="inside">	
			<table class="form-table">
			<tr>
				<td colspan="4">
				<p>NextGEN Facebook OG uses the "ngfb-buttons" CSS class name to wrap all social buttons, and each button has it's own individual class name as well. Refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/">NextGEN Facebook OG FAQ</a> page for stylesheet examples -- including how to hide the buttons for specific posts, pages, categories, tags, etc. Each of the following social buttons can be added to an "NGFB Social Buttons" widget as well (see the <a href="widgets.php">widgets admin page</a> for the widget options).</p>
				</td>
			</tr>
			<tr>
				<th>Include on Index Webpages</th>
				<td><input name="ngfb_options[buttons_on_home]" type="checkbox" value="1"
					<?php checked(1, $ngfb->options['buttons_on_home']); ?> />
				</td>
				<td colspan="2">
				<p>Add the social buttons enabled bellow, to each entry's content on index webpages (index, archives, author, etc.).</p>
				</td>
			</tr>
			<?php	// hide Add to Excluded Pages option if not installed and activated
				if ( function_exists( 'ep_get_excluded_ids' ) ): ?>
			<tr>
				<th>Add to Excluded Pages</th>
				<td><input name="ngfb_options[buttons_on_ex_pages]" type="checkbox" value="1"
					<?php checked(1, $ngfb->options['buttons_on_ex_pages']); ?> />
				</td><td colspan="2">
				<p>The <a href="http://wordpress.org/extend/plugins/exclude-pages/" target="_blank">Exclude Pages</a> plugin has been detected. By default, social buttons are not added to excluded pages. You can over-ride the default and add social buttons to excluded page content by selecting this option.</p>
				</td>
			</tr>
			<?php	endif; ?>
			<tr>
				<th>Location in Content Text</th>
				<td><?php $this->select( 'buttons_location', array( 'top' => 'Top', 'bottom' => 'Bottom' ) ); ?></td>
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
				<td><?php $this->select( 'fb_order', range( 1, $buttons_count ), 'order' ); ?></td>
				<!-- Google+ -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'gp_order', range( 1, $buttons_count ), 'order' ); ?></td>
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
				) ) ?></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Button Layout</th>
				<td><?php $this->select( 'fb_layout', array( 
					'standard' => 'Standard',
					'button_count' => 'Button Count',
					'box_count' => 'Box Count',
				) ) ?></td>
				<!-- Google+ -->
				<th>Button Size</th>
				<td><?php $this->select( 'gp_size', array( 
					'small' => 'Small [ 15px ]',
					'medium' => 'Medium [ 20px ]',
					'standard' => 'Standard [ 24px ]',
					'tall' => 'Tall [ 60px ]',
				) ) ?></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Show Facebook Faces</th>
				<td><?php $this->checkbox( 'fb_show_faces' ); ?></td>
				<!-- Google+ -->
				<th>Annotation</th>
				<td><?php $this->select( 'gp_annotation', array( 
					'inline' => 'Inline',
					'bubble' => 'Bubble',
					'vertical-bubble' => 'Vertical Bubble',
					'none' => 'None',
				) ) ?></td>
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
					'verdana' => 'Verdana',
				) ) ?></td>
				<!-- Google+ -->
				<td colspan="2"></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Button Color Scheme</th>
				<td><?php $this->select( 'fb_colorscheme', array( 
					'light' => 'Light',
					'dark' => 'Dark',
				) ) ?></td>
				<!-- Google+ -->
				<td colspan="2"></td>
			</tr>
			<tr>
				<!-- Facebook -->
				<th>Facebook Action Name</th>
				<td><?php $this->select( 'fb_action', array( 
					'like' => 'Like',
					'recommend' => 'Recommend',
				) ) ?></td>
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
				<td><?php $this->select( 'linkedin_order', range( 1, $buttons_count ), 'order' ); ?></td>
				<!-- Twitter -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'twitter_order', range( 1, $buttons_count ), 'order' ); ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<th>Counter Mode</th>
				<td><?php $this->select( 'linkedin_counter', array( 
					'right' => 'Horizontal',
					'top' => 'Vertical',
					'none' => 'None',
				) ) ?></td>
				<!-- Twitter -->
				<th>Count Box Position</th>
				<td><?php $this->select( 'twitter_count', array( 
					'horizontal' => 'Horizontal',
					'vertical' => 'Vertical',
					'none' => 'None',
				) ) ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<td colspan="2"></td>
				<!-- Twitter -->
				<th>Button Size</th>
				<td><?php $this->select( 'twitter_size', array( 
					'medium' => 'Medium',
					'large' => 'Large',
				) ) ?></td>
			</tr>
			<tr>
				<!-- LinkedIn -->
				<td colspan="2"></td>
				<!-- Twitter -->
				<th>Do Not Track</th>
				<td><?php $this->checkbox( 'twitter_dnt' ); ?></td>
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
				<td colspan="2">
					<p>The Pinterest "Pin It" button will only appear on posts and pages with a featured image.</p>
				</td>
				<!-- tumblr -->
				<td colspan="2">
					<p>The tumblr button shares featured images, embeded videos, quote post formats, and links to webpages.</p>
				</td>
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
				<td><?php $this->select( 'pin_order', range( 1, $buttons_count ), 'order' ); ?></td>
				<!-- tumblr -->
				<th>Preferred Order</th>
				<td><?php $this->select( 'tumblr_order', range( 1, $buttons_count ), 'order' ); ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>Pin Count Layout</th>
				<td><?php $this->select( 'pin_count_layout', array( 
					'horizontal' => 'Horizontal',
					'vertical' => 'Vertical',
					'none' => 'None',
				) ) ?></td>
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
											<input type="radio" id="share_', $i, $t, '" name="ngfb_options[tumblr_button_style]" 
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
					'none' => 'None',
				) ) ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<th>Max Caption Length</th>
					<td><input type="text" name="ngfb_options[pin_cap_len]" class="number"
					value="<?php echo $ngfb->options['pin_cap_len']; ?>" /> Characters
				</td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"></td>
				<!-- tumblr -->
				<th>Max <u>Link</u> Description Length</th>
				<td><input type="text" name="ngfb_options[tumblr_desc_len]" class="number"
					value="<?php echo $ngfb->options['tumblr_desc_len']; ?>" /> Characters
				</td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"></td>
				<!-- tumblr -->
				<th>Share Featured Image</th>
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
					'none' => 'None',
				) ) ?></td>
			</tr>
			<tr>
				<!-- Pinterest -->
				<td colspan="2"></td>
				<!-- tumblr -->
				<th>Max Caption Length</th>
				<td><input type="text" name="ngfb_options[tumblr_cap_len]" class="number"
					value="<?php echo $ngfb->options['tumblr_cap_len']; ?>" /> Characters
				</td>
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
				<td><?php $this->select( 'stumble_order', range( 1, $buttons_count ), 'order' ); ?></td>
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
						.badge-col-left {
							display:inline-block;
							float:left;
						}
						.badge-col-right {
							display:inline-block;
						}
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
								case '1': echo '<div class="badge-col-left">', "\n"; break;
								case '4': echo '</div><div class="badge-col-right">', "\n"; break;
							}
							echo '<div class="badge" id="badge-', $i, '">', "\n";
							echo '<input type="radio" name="ngfb_options[stumble_badge]" 
									value="', $i, '" ', checked( $i, $ngfb->options['stumble_badge'], false ), '/>', "\n";
							echo '</div>', "\n";
							switch ( $i ) { case '6': echo '</div>', "\n"; break; }
						}
					?>
				</td>
			</tr>
			</table>
			</div><!-- .inside -->
			</div><!-- .postbox -->
		
			<div id="ngfb-pluginsettings" class="postbox">
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
				<th>Filter Content for Meta Tags</th>
				<td><?php $this->checkbox( 'ngfb_filter_content' ); ?></td>
				<td><p>When NextGEN Facebook OG generates the Open Graph meta tags, it applies Wordpress filters on the content to expand shortcodes etc. In most cases this is fine, even desirable, but in a few rare cases, it may break another plugin. You can prevent NextGEN Facebook OG from applying the Wordpress filters by un-checking this option. If you do, NextGEN Facebook OG may not have access to the complete content text (if your content includes some shortcodes, for example), and may generate inaccurate Open Graph description or image meta tags.</p></td>
			</tr>
			<tr>
				<th>Ignore Small Images in Content</th>
				<td><?php $this->checkbox( 'ngfb_skip_small_img' ); ?></td>
				<td><p>If there is no featured image defined, or NextGEN [singlepic] found in the content, the plugin will attempt to use the first IMG HTML tag it finds. The IMG must have a width and height attribute, and it's size must be equal to or larger than the Image Size Name you've selected. You can uncheck this option to use smaller images from the content, or refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/">NextGEN Facebook OG FAQ</a> page for additional solutions.</p></td>
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
			global $ngfb;
			$is_assoc = is_numeric( implode( array_keys( $values ) ) ) && $class != 'yesno' ? 0 : 1;
			echo '<select name="ngfb_options[', $name, ']"';
			echo empty( $class ) ? '' : ' class="'.$class.'"';
			echo empty( $id ) ? '' : ' id="'.$id.'"';
			echo '>', "\n";
			foreach ( (array) $values as $val => $desc ) {
				if ( ! $is_assoc ) $val = $desc;
				echo '<option value="', $val, '"';
				selected( $ngfb->options[$name], $val );
				echo '>', $desc;
				if ( empty( $desc ) ) echo 'None';
				if ( $val == $ngfb->default_options[$name] ) echo ' (default)';
				echo '</option>', "\n";
			}
			echo '</select>';
		}

		function checkbox( $name, $check = array( '1', '0' ) ) {
			global $ngfb;
			echo '<input name="ngfb_options[', $name, ']" type="checkbox" value="', $check[0], '"';
			checked( $ngfb->options[$name], $check[0] );
			echo ' title="Default is ';
			echo $ngfb->default_options[$name] == $check[0] ? 'Checked' : 'Unchecked';
			echo '" />';
			echo "</small>";
		}

		function select_img_size( $name ) {
			global $ngfb;
			global $_wp_additional_image_sizes;
			$size_names = get_intermediate_image_sizes();
			natsort( $size_names );
			echo '<select name="ngfb_options[', $name, ']">', "\n";
			foreach ( $size_names as $size_name ) {
				if ( is_integer( $size_name ) ) continue;
				$size = ngfb_get_size_values( $size_name );
				echo '<option value="', $size_name, '" ', 
					selected( $ngfb->options[$name], $size_name, false ), '>', 
					$size_name, ' [ ', $size['width'], 'x', $size['height'],
					$size['crop'] ? " cropped" : "", ' ]', "\n";
				if ( $size_name == $this->default_options[$name] ) echo ' (default)';
				echo '</option>', "\n";
			}
			unset ( $size_name );
			echo '</select>';
		}
	}
}
?>
