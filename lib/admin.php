<?php

class ngfbAdminPanel {

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

	function __construct() {
		natsort ( $this->article_sections );
		add_action( 'admin_init', array( &$this, 'admin_init' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
	}

	function admin_init() {
		register_setting( 'ngfb_plugin_options', 'ngfb_options', 'ngfb_validate_options' );
	}

	function admin_menu() {
		add_options_page('NextGEN Facebook OG Plugin', 'NextGEN Facebook', 'manage_options', 'ngfb', array( &$this, 'options_page' ) );
	}

	function options_page() {

		$options = ngfb_get_options( 'ngfb_options' );

		$buttons_count = 0;
		foreach ( $options as $opt => $val )
			if ( preg_match( '/_enable$/', $opt ) )
				$buttons_count++;

		?><style type="text/css">
			.form-table tr { vertical-align:top; }
			.form-table td { padding:2px 6px 2px 6px; }
			.form-table th { text-align:right; white-space:nowrap; padding:2px 6px 2px 6px; width:180px; }
			.form-table th#social { font-weight:bold; text-align:left; background-color:#eee; border:1px solid #ccc; }
			.form-table th#meta { width:220px; }
			.form-table td select,
			.form-table td input { margin:0 0 5px 0; }
			.form-table td input[type=radio] { vertical-align:top; margin:4px 4px 4px 0; }
			.form-table td select { width:250px; }
			.wrap { font-size:1em; line-height:1.3em; }
			.wrap h2 { margin:0 0 10px 0; }
			.wrap p { text-align:justify; line-height:1.3em; margin:5px 0 5px 0; }
			.btn_wizard_column { white-space:nowrap; }
			.btn_wizard_example { display:inline-block; width:155px; }
		</style>
		<div class="wrap" id="ngfb">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>NextGEN Facebook OG Plugin</h2>

		<p>The NextGEN Facebook OG plugin adds Open Graph HTML meta tags to your webpages. If your post or page has a featured image, it will be included as well - even if it's located in a NextGEN Gallery. All options bellow are optional. You can enable social sharing buttons, define a default image, etc.</p>

		<p>The image used in Open Graph HTML meta tags will be determined in this sequence; a featured image from a NextGEN Gallery or WordPress Media Library, the first NextGEN [singlepic] shortcode or &lt;IMG&gt; HTML tag in the content, and the default image defined here. If none of these conditions can be satisfied, then the Open Graph image tag will be left out.</p>

		<div class="updated" style="margin:10px 0;">
		<p style="text-align:center">We don't ask for donations, but if you like the NextGEN Facebook OG plugin, <a href="http://wordpress.org/support/view/plugin-reviews/nextgen-facebook?rate=5#postform"><strong>please take a moment to encourage us and review it</strong></a> on the WordPress website. Thank you. :-)</p>
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
			<td>
				<select name='ngfb_options[og_art_section]'>
				<?php
					echo '<option value="" ', selected($options['og_art_section'], '', false), '></option>', "\n";
					foreach ( $this->article_sections as $s ) {
						echo '<option value="', $s, '" ',
							selected( $options['og_art_section'], $s, false), '>', $s, '</option>', "\n";
					}
					unset ( $s );
				?>
				</select>
			</td><td>
			<p>The topic name that best describes the posts and pages on your website.  This topic name will be used in the "article:section" Open Graph HTML meta tag for your posts and pages. You can leave the topic name blank, if you would prefer not to include an "article:section" HTML meta tag.</p>
			</td>
		</tr>
		<tr>
			<th>Image Size Name</th>
			<td>
				<?php ngfb_select_img_size( $options, 'og_img_size' ); ?>
			</td><td>
			<p>The <a href="options-media.php">WordPress Media Library "size name"</a> for the image used in the Open Graph HTML meta tag. Generally this would be "thumbnail" (currently defined as <?php echo get_option('thumbnail_size_w'), 'x', get_option('thumbnail_size_h'), ', ', get_option('thumbnail_crop') == "1" ? "" : "not"; ?> cropped), or another size name like "medium", "large", etc. Choose a size name that is at least <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> or more in width and height, and preferably cropped. You can use the <a href="http://wordpress.org/extend/plugins/simple-image-sizes/" target="_blank">Simple Image Size</a> plugin (or others) to define your own custom size names on the <a href="options-media.php">Media Settings</a> admin page. I would suggest creating a "facebook-thumbnail" size name of <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> (or larger) cropped, to manage the size of Open Graph images independently from those of your theme.</p>
		</td>
		</tr>
		<tr>
			<th>Default Image ID</th>
			<td><input type="text" name="ngfb_options[og_def_img_id]" size="6"
				value="<?php echo $options['og_def_img_id']; ?>" />
				in the
				<select name='ngfb_options[og_def_img_id_pre]' style="width:150px;">
					<option value='' <?php selected($options['og_def_img_id_pre'], ''); ?>>Media Library</option>
					<option value='ngg' <?php selected($options['og_def_img_id_pre'], 'ngg'); ?>>NextGEN Gallery</option>
				</select>
			</td><td>
			<p>The ID number and location of your default image (example: 123).</p>
			</td>
		</tr>
		<tr>
			<th>Default Image URL</th>
			<td colspan="2"><input type="text" name="ngfb_options[og_def_img_url]" size="80"
				value="<?php echo $options['og_def_img_url']; ?>" style="width:100%;"/>
			<p>You can specify a Default Image URL (including the http:// prefix) instead of a Default Image ID. This allows you to use an image outside of a managed collection (Media Library or NextGEN Gallery). The image should be at least <?php echo NGFB_MIN_IMG_WIDTH, 'x', NGFB_MIN_IMG_HEIGHT; ?> or more in width and height. If both the Default Image ID and URL are defined, the Default Image ID takes precedence.</p>
			</td>
		</tr>
		<tr>
			<th>Default on Index Webpages</th>
			<td><input name="ngfb_options[og_def_on_home]" type="checkbox" value="1" 
				<?php checked(1, $options['og_def_on_home']); ?> />
			</td><td>
			<p>Check this box if you would like to use the default image on index webpages (homepage, archives, categories, author, etc.). If you leave this un-checked, NextGEN Facebook OG will attempt to use the first featured image, [singlepic] shortcode, or IMG HTML tag within the list of entries on the webpage.</p>
			</td>
		</tr>
		<tr>
			<th>Default Image on Search Page</th>
			<td><input name="ngfb_options[og_def_on_search]" type="checkbox" value="1" 
				<?php checked(1, $options['og_def_on_search']); ?> />
			</td><td>
			<p>Check this box if you would like to use the default image on search result webpages as well.</p>
			</td>
		</tr>
		<tr>
			<th>Add NextGEN Gallery Tags</th>
			<td><input name="ngfb_options[og_ngg_tags]" type="checkbox" value="1" 
				<?php checked(1, $options['og_ngg_tags']); ?> />
			</td><td>
			<p>If the featured or default image is from a NextGEN Gallery, then add that image's tags to the Open Graph tag list.</p>
			</td>
		</tr>
		<tr>
			<th>Max Title Length</th>
			<td><input type="text" size="6" name="ngfb_options[og_title_len]" 
				value="<?php echo $options['og_title_len']; ?>" /> Characters
			</td><td>
			<p>The maximum length of text used in the Open Graph title tag (default is 100 characters).</p>
			</td>
		</tr>
		<tr>
			<th>Max Description Length</th>
			<td><input type="text" size="6" name="ngfb_options[og_desc_len]" 
				value="<?php echo $options['og_desc_len']; ?>" /> Characters
			</td><td>
			<p>The maximum length of text, from your post / page excerpt or content, used in the Open Graph description tag. The length must be <?php echo NGFB_MIN_DESC_LEN; ?> characters or more (default is 300).</p>
			</td>
		</tr>
		<tr>
			<th>Content Begins at First Paragraph</th>
			<td><input name="ngfb_options[og_desc_strip]" type="checkbox" value="1" 
				<?php checked(1, $options['og_desc_strip']); ?> />
			</td><td>
			<p>For a page or post <i>without</i> an excerpt, if this option is checked, the plugin will ignore all text until the first &lt;p&gt; paragraph in <i>the content</i>. If an excerpt exists, then it's complete text will be used instead.</p>
			</td>
		</tr>
		<?php	// hide WP-WikiBox option if not installed and activated
			if ( function_exists( 'wikibox_summary' ) ): ?>
		<tr>
			<th>Use WP-WikiBox for Pages</th>
			<td><input name="ngfb_options[og_desc_wiki]" type="checkbox" value="1" 
				<?php checked(1, $options['og_desc_wiki']); ?> />
			</td><td>
			<p>The <a href="http://wordpress.org/extend/plugins/wp-wikibox/" target="_blank">WP-WikiBox</a> plugin has been detected. NextGEN Facebook OG can ignore the content of your pages when creating the "description" Open Graph HTML meta tag, and retrieve it from Wikipedia instead. This only aplies to pages, not posts. Here's how it works; the plugin will check for the page's tags and use their names to retrieve content from Wikipedia. If no tags are defined, then the page title will be used. If Wikipedia does not return a summary for the tags or title, then the content of your page will be used.</p>
			</td>
		</tr>
		<tr>
			<th>WP-WikiBox Tag Prefix</th>
			<td><input type="text" size="6" name="ngfb_options[og_wiki_tag]" 
				value="<?php echo $options['og_wiki_tag']; ?>" />
			</td><td>
			<p>A prefix to identify the WordPress tag names used to retrieve Wikipedia content. Leave this option blank to use all tags associated to a post, or choose a prefix (like "Wiki-") to use only tag names starting with that prefix.</p>
			</td>
		</tr>
		<?php	endif; ?>
		<tr>
			<th>Facebook Admin(s)</th>
			<td><input type="text" size="40" name="ngfb_options[og_admins]" 
				value="<?php echo $options['og_admins']; ?>" />
			</td><td>
			<p>One or more Facebook account names (generally your own) separated with a comma. When you are viewing your own Facebook wall, your account name is located in the URL (example: https://www.facebook.com/<b>account_name</b>). The Facebook Admin names are used by Facebook to provide <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data to those accounts.</p>
			</td>
		</tr>
		<tr>
			<th>Facebook App ID</th>
			<td><input type="text" size="40" name="ngfb_options[og_app_id]" 
				value="<?php echo $options['og_app_id']; ?>" />
			</td><td>
			<p>If you have a <a href="https://developers.facebook.com/apps" target="_blank">Facebook Application</a> ID, enter it here.  Facebook Application IDs are used by Facebook to provide <a href="https://developers.facebook.com/docs/insights/" target="_blank">Facebook Insight</a> data to one or more accounts associated with the Application ID.</p>
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
			<p>NextGEN Facebook OG will add all known Facebook and Open Graph meta tags to your webpage headers. If your theme, or another plugin, already generates one or more of these meta tags, you may uncheck them here to prevent NextGEN Facebook OG from adding them.</p>
			</td>
		</tr>
		<?php
			$og_cells = array();
			$og_rows = array();
	
			foreach ( $options as $opt => $val ) {
				if ( preg_match( '/^inc_(.*)$/', $opt, $match ) )
					$og_cells[] = '<th id="meta">Include '.$match[1].' Meta Tag</th>
						<td><input name="ngfb_options['.$opt.']" type="checkbox" 
							value="1" '.checked(1, $options[$opt], false).'/></td>';
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
				<?php checked(1, $options['buttons_on_home']); ?> />
			</td>
			</td><td colspan="2">
			<p>Add social buttons (that are enabled bellow) to each entry's content on index webpages (index, archives, author, etc.).</p>
			</td>
		</tr>
	
		<?php	// hide Add to Excluded Pages option if not installed and activated
			if ( function_exists( 'ep_get_excluded_ids' ) ): ?>
		<tr>
			<th>Add to Excluded Pages</th>
			<td><input name="ngfb_options[buttons_on_ex_pages]" type="checkbox" value="1"
				<?php checked(1, $options['buttons_on_ex_pages']); ?> />
			</td><td colspan="2">
			<p>The <a href="http://wordpress.org/extend/plugins/exclude-pages/" target="_blank">Exclude Pages</a> plugin has been detected. By default, social buttons are not added to excluded pages. You can over-ride the default, and add social buttons to excluded page content, by selecting this option.</p>
			</td>
		</tr>
		<?php	endif; ?>
	
		<tr>
			<th>Location in Content</th>
			<td>
				<select name='ngfb_options[buttons_location]'>
					<option value='top' <?php selected($options['buttons_location'], 'top'); ?>>Top</option>
					<option value='bottom' <?php selected($options['buttons_location'], 'bottom'); ?>>Bottom</option>
				</select>
			</td>
		</tr>
		</table>
		<table class="form-table">
		<tr>
			<!-- Facebook -->
			<th colspan="2" id="social">Facebook</th>
			<!-- Google+ -->
			<th colspan="2" id="social">Google+</th>
		</tr>
		<tr><td style="height:5px;"></td></tr>
		<tr>
			<!-- Facebook -->
			<th>Add Button to Content</th>
			<td><input name="ngfb_options[fb_enable]" type="checkbox" value="1" 
				<?php checked(1, $options['fb_enable']); ?> />
			</td>
			<!-- Google+ -->
			<th>Add Button to Content</th>
			<td><input name="ngfb_options[gp_enable]" type="checkbox" value="1" 
				<?php checked(1, $options['gp_enable']); ?> />
			</td>
		</tr>
		<tr>
			<!-- Facebook -->
			<th>Preferred Order</th>
			<td>
				<select name='ngfb_options[fb_order]' style="width:50px;">
				<?php foreach ( range( 1, $buttons_count ) as $i ) echo '<option value="', $i, '"', 
					selected($options['fb_order'], $i), '>', $i, '</option>', "\n"; ?>
				</select>
			</td>
			<!-- Google+ -->
			<th>Preferred Order</th>
			<td>
				<select name='ngfb_options[gp_order]' style="width:50px;">
				<?php foreach ( range( 1, $buttons_count ) as $i ) echo '<option value="', $i, '"', 
					selected($options['gp_order'], $i), '>', $i, '</option>', "\n"; ?>
				</select>
			</td>
		</tr>
		<tr>
			<!-- Facebook -->
			<th>Include Send Button</th>
			<td><input name="ngfb_options[fb_send]" type="checkbox" value="1"
				<?php checked(1, $options['fb_send']); ?> />
			</td>
			<!-- Google+ -->
			<th>Button Type</th>
			<td>
				<select name='ngfb_options[gp_action]'>
					<option value='plusone' <?php selected($options['gp_action'], 'plusone'); ?>>G +1</option>
					<option value='share' <?php selected($options['gp_action'], 'share'); ?>>G+ Share</option>
				</select>
			</td>
		</tr>
		<tr>
			<!-- Facebook -->
			<th>Button Layout</th>
			<td>
				<select name='ngfb_options[fb_layout]'>
					<option value='standard' <?php selected($options['fb_layout'], 'standard'); ?>>Standard</option>
					<option value='button_count' <?php selected($options['fb_layout'], 'button_count'); ?>>Button Count</option>
					<option value='box_count' <?php selected($options['fb_layout'], 'box_count'); ?>>Box Count</option>
				</select>
			</td>
			<!-- Google+ -->
			<th>Button Size</th>
			<td>
				<select name='ngfb_options[gp_size]'>
					<option value='small' <?php selected($options['gp_size'], 'small'); ?>>Small (15px)</option>
					<option value='medium' <?php selected($options['gp_size'], 'medium'); ?>>Medium (20px)</option>
					<option value='standard' <?php selected($options['gp_size'], 'standard'); ?>>Standard (24px)</option>
					<option value='tall' <?php selected($options['gp_size'], 'tall'); ?>>Tall (60px)</option>
				</select>
			</td>
		</tr>
		<tr>
			<!-- Facebook -->
			<th>Show Facebook Faces</th>
			<td><input name="ngfb_options[fb_show_faces]" type="checkbox" value="1"
				<?php checked(1, $options['fb_show_faces']); ?> />
			</td>
			<!-- Google+ -->
			<th>Annotation</th>
			<td>
				<select name='ngfb_options[gp_annotation]'>
					<option value='inline' <?php selected($options['gp_annotation'], 'inline'); ?>>Inline</option>
					<option value='bubble' <?php selected($options['gp_annotation'], 'bubble'); ?>>Bubble</option>
					<option value='vertical-bubble' <?php selected($options['gp_annotation'], 'vertical-bubble'); ?>>Vertical Bubble</option>
					<option value='none' <?php selected($options['gp_annotation'], 'none'); ?>>None</option>
				</select>
			</td>
		</tr>
		<tr>
			<!-- Facebook -->
			<th>Button Font</th>
			<td>
				<select name='ngfb_options[fb_font]'>
					<option value='arial' <?php selected('arial', $options['fb_font']); ?>>Arial</option>
					<option value='lucida grande' <?php selected('lucida grande', $options['fb_font']); ?>>Lucida Grande</option>
					<option value='segoe ui' <?php selected('segoe ui', $options['fb_font']); ?>>Segoe UI</option>
					<option value='tahoma' <?php selected('tahoma', $options['fb_font']); ?>>Tahoma</option>
					<option value='trebuchet ms' <?php selected('trebuchet ms', $options['fb_font']); ?>>Trebuchet MS</option>
					<option value='verdana' <?php selected('verdana', $options['fb_font']); ?>>Verdana</option>
				</select>
			</td>
			<!-- Google+ -->
			<td colspan="2"></td>
		</tr>
		<tr>
			<!-- Facebook -->
			<th>Button Color Scheme</th>
			<td>
				<select name='ngfb_options[fb_colorscheme]'>
					<option value='light' <?php selected('light', $options['fb_colorscheme']); ?>>Light</option>
					<option value='dark' <?php selected('dark', $options['fb_colorscheme']); ?>>Dark</option>
				</select>
			</td>
			<!-- Google+ -->
			<td colspan="2"></td>
		</tr>
		<tr>
			<!-- Facebook -->
			<th>Facebook Action Name</th>
			<td>
				<select name='ngfb_options[fb_action]'>
					<option value='like' <?php selected('like', $options['fb_action']); ?>>Like</option>
					<option value='recommend' <?php selected('recommend', $options['fb_action']); ?>>Recommend</option>
				</select>
			</td>
			<!-- Google+ -->
			<td colspan="2"></td>
		</tr>				
		<tr><td style="height:5px;"></td></tr>
		<tr>
			<!-- LinkedIn -->
			<th colspan="2" id="social">LinkedIn</th>
			<!-- Twitter -->
			<th colspan="2" id="social">Twitter</th>
		</tr>
		<tr><td style="height:5px;"></td></tr>
		<tr>
			<!-- LinkedIn -->
			<th>Add Button to Content</th>
			<td><input name="ngfb_options[linkedin_enable]" type="checkbox" value="1" 
				<?php checked(1, $options['linkedin_enable']); ?> />
			</td>
			<!-- Twitter -->
			<th>Add Button to Content</th>
			<td><input name="ngfb_options[twitter_enable]" type="checkbox" value="1" 
				<?php checked(1, $options['twitter_enable']); ?> />
			</td>
		</tr>
		<tr>
			<!-- LinkedIn -->
			<th>Preferred Order</th>
			<td>
				<select name='ngfb_options[linkedin_order]' style="width:50px;">
				<?php foreach ( range( 1, $buttons_count ) as $i ) echo '<option value="', $i, '"', 
					selected($options['linkedin_order'], $i), '>', $i, '</option>', "\n"; ?>
				</select>
			</td>
			<!-- Twitter -->
			<th>Preferred Order</th>
			<td>
				<select name='ngfb_options[twitter_order]' style="width:50px;">
				<?php foreach ( range( 1, $buttons_count ) as $i ) echo '<option value="', $i, '"', 
					selected($options['twitter_order'], $i), '>', $i, '</option>', "\n"; ?>
				</select>
			</td>
		</tr>
		<tr>
			<!-- LinkedIn -->
			<th>Counter Mode</th>
			<td>
				<select name='ngfb_options[linkedin_counter]'>
					<option value='top' <?php selected($options['linkedin_counter'], 'top'); ?>>Vertical</option>
					<option value='right' <?php selected($options['linkedin_counter'], 'right'); ?>>Horizontal</option>
					<option value='' <?php selected($options['linkedin_counter'], ''); ?>>None</option>
				</select>
			</td>
			<!-- Twitter -->
			<th>Count Box Position</th>
			<td>
				<select name='ngfb_options[twitter_count]'>
					<option value='horizontal' <?php selected($options['twitter_count'], 'horizontal'); ?>>Horizontal</option>
					<option value='vertical' <?php selected($options['twitter_count'], 'vertical'); ?>>Vertical</option>
					<option value='none' <?php selected($options['twitter_count'], 'none'); ?>>None</option>
				</select>
			</td>
		</tr>
		<tr>
			<!-- LinkedIn -->
			<td colspan="2"></td>
			<!-- Twitter -->
			<th>Button Size</th>
			<td>
				<select name='ngfb_options[twitter_size]'>
					<option value='medium' <?php selected($options['twitter_size'], 'medium'); ?>>Medium</option>
					<option value='large' <?php selected($options['twitter_size'], 'large'); ?>>Large</option>
				</select>
			</td>
		</tr>
		<tr>
			<!-- LinkedIn -->
			<td colspan="2"></td>
			<!-- Twitter -->
			<th>Do Not Track</th>
			<td><input name="ngfb_options[twitter_dnt]" type="checkbox" value="1" 
				<?php checked(1, $options['twitter_dnt']); ?> />
			</td>
		</tr>
		<tr><td style="height:5px;"></td></tr>
		<tr>
			<!-- Pinterest -->
			<th colspan="2" id="social">Pinterest</th>
			<!-- tumblr -->
			<th colspan="2" id="social">tumblr</th>
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
			<td><input name="ngfb_options[pin_enable]" type="checkbox" value="1" 
				<?php checked(1, $options['pin_enable']); ?> />
			</td>
			<!-- tumblr -->
			<th>Add Button to Content</th>
			<td><input name="ngfb_options[tumblr_enable]" type="checkbox" value="1" 
				<?php checked(1, $options['tumblr_enable']); ?> />
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<th>Preferred Order</th>
			<td>
				<select name='ngfb_options[pin_order]' style="width:50px;">
				<?php foreach ( range( 1, $buttons_count ) as $i ) echo '<option value="', $i, '"', 
					selected($options['pin_order'], $i), '>', $i, '</option>', "\n"; ?>
				</select>
			</td>
			<!-- tumblr -->
			<th>Preferred Order</th>
			<td>
				<select name='ngfb_options[tumblr_order]' style="width:50px;">
				<?php foreach ( range( 1, $buttons_count ) as $i ) echo '<option value="', $i, '"', 
					selected($options['tumblr_order'], $i), '>', $i, '</option>', "\n"; ?>
				</select>
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<th>Pin Count Layout</th>
			<td>
				<select name='ngfb_options[pin_count_layout]'>
					<option value='horizontal' <?php selected($options['pin_count_layout'], 'horizontal'); ?>>Horizontal</option>
					<option value='vertical' <?php selected($options['pin_count_layout'], 'vertical'); ?>>Vertical</option>
					<option value='none' <?php selected($options['pin_count_layout'], 'none'); ?>>None</option>
				</select>
			</td>
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
											checked( 'share_'.$i.$t, $options['tumblr_button_style'], false ), '/>
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
			<td>
				<?php ngfb_select_img_size( $options, 'pin_img_size' ); ?>
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<th>Image Caption Text</th>
			<td>
				<select name='ngfb_options[pin_caption]'>
					<option value='title' <?php selected($options['pin_caption'], 'title'); ?>>Title Only</option>
					<option value='excerpt' <?php selected($options['pin_caption'], 'excerpt'); ?>>Excerpt Only</option>
					<option value='both' <?php selected($options['pin_caption'], 'both'); ?>>Title and Excerpt</option>
					<option value='none' <?php selected($options['pin_caption'], 'none'); ?>>None</option>
				</select>
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<th>Max Caption Length</th>
				<td><input type="text" size="6" name="ngfb_options[pin_cap_len]" 
				value="<?php echo $options['pin_cap_len']; ?>" /> Characters
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<td colspan="2"></td>
			<!-- tumblr -->
			<th>Max <u>Link</u> Description Length</th>
			<td><input type="text" size="6" name="ngfb_options[tumblr_desc_len]" 
				value="<?php echo $options['tumblr_desc_len']; ?>" /> Characters
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<td colspan="2"></td>
			<!-- tumblr -->
			<th>Share Featured Image</th>
			<td><input name="ngfb_options[tumblr_photo]" type="checkbox" value="1" 
				<?php checked(1, $options['tumblr_photo']); ?> />
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<td colspan="2"></td>
			<!-- tumblr -->
			<th>Featured Image Size to Share</th>
			<td>
				<?php ngfb_select_img_size( $options, 'tumblr_img_size' ); ?>
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<td colspan="2"></td>
			<!-- tumblr -->
			<th>Image and Video Caption Text</th>
			<td>
				<select name='ngfb_options[tumblr_caption]'>
					<option value='title' <?php selected($options['tumblr_caption'], 'title'); ?>>Title Only</option>
					<option value='excerpt' <?php selected($options['tumblr_caption'], 'excerpt'); ?>>Excerpt Only</option>
					<option value='both' <?php selected($options['tumblr_caption'], 'both'); ?>>Title and Excerpt</option>
					<option value='none' <?php selected($options['tumblr_caption'], 'none'); ?>>None</option>
					</select>
			</td>
		</tr>
		<tr>
			<!-- Pinterest -->
			<td colspan="2"></td>
			<!-- tumblr -->
			<th>Max Caption Length</th>
			<td><input type="text" size="6" name="ngfb_options[tumblr_cap_len]" 
				value="<?php echo $options['tumblr_cap_len']; ?>" /> Characters
			</td>
		</tr>
		<tr><td style="height:5px;"></td></tr>
		<tr>
			<!-- StumbleUpon -->
			<th colspan="2" id="social">StumbleUpon</th>
		</tr>
		<tr><td style="height:5px;"></td></tr>
		<tr>
			<!-- StumbleUpon -->
			<th>Add Button to Content</th>
			<td><input name="ngfb_options[stumble_enable]" type="checkbox" value="1" 
				<?php checked(1, $options['stumble_enable']); ?> />
			</td>
		</tr>
		<tr>
			<!-- StumbleUpon -->
			<th>Preferred Order</th>
			<td>
				<select name='ngfb_options[stumble_order]' style="width:50px;">
				<?php foreach ( range( 1, $buttons_count ) as $i ) echo '<option value="', $i, '"', 
					selected($options['stumble_order'], $i), '>', $i, '</option>', "\n"; ?>
				</select>
			</td>
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
								value="', $i, '" ', checked( $i, $options['stumble_badge'], false ), '/>', "\n";
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
			<td><input name="ngfb_options[ngfb_reset]" type="checkbox" value="1" 
				<?php checked(1, $options['ngfb_reset']); ?> />
			</td><td>
			<p>Check this option to reset NextGEN Facebook OG settings to their default values <u>when you deactivate, and then reactivate the plugin</u>.</p>
			</td>
		</tr>
		<tr>
			<th>Add Hidden Debug Info</th>
			<td><input name="ngfb_options[ngfb_debug]" type="checkbox" value="1" 
				<?php checked(1, $options['ngfb_debug']); ?> />
			</td><td>
			<p>Include hidden debug information with the Open Graph meta tags.</p>
			</td>
		</tr>
		<tr>
			<th>Filter Content for Meta Tags</th>
			<td><input name="ngfb_options[ngfb_filter_content]" type="checkbox" value="1" 
				<?php checked(1, $options['ngfb_filter_content']); ?> />
			</td><td>
			<p>When NextGEN Facebook OG generates the Open Graph meta tags, it applies Wordpress filters on the content to expand shortcodes etc. In most cases this is fine, even desirable, but in a few rare cases, it may break another plugin. You can prevent NextGEN Facebook OG from applying the Wordpress filters by un-checking this option. If you do, NextGEN Facebook OG may not have access to the complete content text (if your content includes some shortcodes, for example), and may generate inaccurate Open Graph description or image meta tags.</p>
			</td>
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
	
}
	
?>
