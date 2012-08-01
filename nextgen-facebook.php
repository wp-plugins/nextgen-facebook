<?php
/*
Plugin Name: NextGEN Facebook
Plugin URI: http://wordpress.org/extend/plugins/nextgen-facebook/
Description: Adds Facebook HTML meta tags to webpage headers, including featured images. Also includes optional Like and Send Facebook buttons.
Version: 1.5
Author: Jean-Sebastien Morisset
Author URI: http://trtms.com/

This plugin is based on the WP Facebook Like Send & Open Graph Meta v1.2.3
plugin by Marvie Pons.

The NextGEN Facebook plugin adds Facebook Open Graph HTML meta tags to all
webpage headers, including the artical meta tags for posts and pages. Featured
image thumbnails, from a NextGEN Gallery or Media Library, are listed in the
image meta tag. You can also, optionally, add Facebook like and send buttons
to your posts and pages.

NextGEN Facebook was specifically written to support featured images located
in a NextGEN Gallery, but works just as well with the WordPress Media Library.
The NextGEN Gallery plugin is not required to use this plugin - all features
work just as well without it.

The image used in the Open Graph meta tag is determined in this sequence; a
featured image from a NextGEN Gallery or WordPress Media Library, the first
NextGEN [singlepic] or IMG HTML tag in the content, a default image defined in
the plugin settings. If none of these conditions can be satisfied, then the
Open Graph image tag will be left empty.

This plugin goes well beyond any other plugins I know in handling various
archive-type webpages. It will create appropriate title and description meta
tags for category, tag, date based archive (day, month, or year), and author
webpages.

This plugin is being actively developed and supported. Post your comments and
suggestions to the NextGEN Facebook Support Page at
http://wordpress.org/support/plugin/nextgen-facebook.

Copyright 2012 Jean-Sebastien Morisset

This script is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This script is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

function ngfb_requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );
	if ( version_compare($wp_version, "3.0", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.0 or
			higher and has been deactivated. Please upgrade WordPress and try
			again.<br /><br />Back to <a href='".admin_url()."'>WordPress
			admin</a>." );
		}
	}
}
add_action( 'admin_init', 'ngfb_requires_wordpress_version' );

register_activation_hook(__FILE__, 'ngfb_add_defaults');
register_uninstall_hook(__FILE__, 'ngfb_delete_plugin_options');
add_action('admin_init', 'ngfb_init' );
add_action('admin_menu', 'ngfb_add_options_page');
add_filter('plugin_action_links', 'ngfb_plugin_action_links', 10, 2);

// Delete options table entries ONLY when plugin deactivated AND deleted
function ngfb_delete_plugin_options() {
	delete_option('ngfb_options');
}

// Define default option settings
function ngfb_add_defaults() {

	$tmp = get_option('ngfb_options');

    if( ( $tmp['ngfb_reset'] == '1' ) || ( !is_array($tmp) ) ) {
		delete_option('ngfb_options');	// remove old options, if any
		$arr = array(
			"og_art_section" => "",
			"og_img_size" => "thumbnail",
			"og_def_img_id_pre" => "",
			"og_def_img_id" => "",
			"og_def_img_url" => "",
			"og_def_on_home" => "",
			"og_ngg_tags" => "",
			"og_desc_strip" => "",
			"og_desc_wiki" => "",
			"og_desc_len" => "300",
			"og_admins" => "",
			"og_app_id" => "",
			"fb_enable" => "",
			"fb_on_home" => "",
			"fb_send" => "true",
			"fb_layout" => "button_count",
			"fb_colorscheme" => "light",
			"fb_font" => "arial",
			"fb_show_faces" => "false",
			"fb_action" => "like",
		);
		update_option('ngfb_options', $arr);
	}
}

// Init plugin options to white list our options
function ngfb_init(){
	register_setting( 'ngfb_plugin_options', 'ngfb_options', 'ngfb_validate_options' );
}

// Add menu page
function ngfb_add_options_page() {
	add_options_page('NextGEN Facebook Options Page', 'NextGEN Facebook', 'manage_options', 'ngfb', 'ngfb_render_form');
}

// Render the Plugin options form
function ngfb_render_form() {

	// list from http://en.wikipedia.org/wiki/Category:Websites_by_topic
	$article_sections = array(
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
	sort ( $article_sections );

	?>
	<div class="wrap">
	<div class="icon32" id="icon-options-general"><br></div>
	<h2>NextGEN Facebook Plugin</h2>

	<p>Once enabled, the NextGEN Facebook plugin will add Facebook Open Graph
	meta tags to your webpages. If your Post or Page has a featured image
	defined, it will be included in the meta tags for Facebook's share and
	like features. All options bellow are optional. You can enable share /
	like buttons, add a default image when there's no featured image defined,
	etc.</p>

	<p>The image used in the Open Graph meta tag will be determined in this
	sequence; a featured image from a NextGEN Gallery or WordPress Media
	Library, the first NextGEN [singlepic] or IMG HTML tag in the content, the
	default image defined bellow. If none of these conditions can be satisfied,
	then the Open Graph image tag will be left empty.</p>

	<p><strong>I don't ask for donations, but if you like the NextGEN Facebook
	plugin, please <a
	href="http://wordpress.org/extend/plugins/nextgen-facebook/"><strong>take a
	moment to rate it and confirm compatibility</strong></a> with your version
	of WordPress.</strong></p>

	<div class="metabox-holder">
		<div class="postbox">
			<h3>Facebook Open Graph Settings</h3>
			<div class="inside">	
	
	<!-- Beginning of the Plugin Options Form -->
	<form method="post" action="options.php">
		<?php 
			settings_fields('ngfb_plugin_options');
			$options = get_option('ngfb_options');

			// update option field names
			if ( ! $options['og_def_img_url'] && $options['og_def_img'] ) {
				$options['og_def_img_url'] = $options['og_def_img'];
				delete_option($options['og_def_img']);
			}
			if ( ! $options['og_def_on_home'] && $options['og_def_home']) {
				$options['og_def_on_home'] = $options['og_def_home'];
				delete_option($options['og_def_home']);
			}

		?>
		<table class="form-table">

			<tr>
				<th scope="row">Website Topic</th>
				<td valign="top">
					<select name='ngfb_options[og_art_section]' style="width:250px;">
						<?php
							echo '<option value="" ', 
								selected($options['og_art_section'], ''), 
								'></option>', "\n";

							foreach ( $article_sections as $s ) {
								echo '<option value="', $s, '" ',
									selected( $options['og_art_section'], $s),
										'>', $s, '</option>', "\n";
							}
						?>
					</select>
				</td><td>
					<p>The topic which best describes the posts and pages on
					your website. This topic name will be used in the
					"article:section" meta tag of all your posts and pages. You
					can leave the topic name blank, if you would prefer not to
					include an "article:section" meta tag.</p>
				</td>
			</tr>

			<tr>
				<th>Image Size Name</th>
				<td valign="top">
					<select name='ngfb_options[og_img_size]' style="width:250px;">
					<?php
						global $_wp_additional_image_sizes;
						// Display the sizes in the array
						foreach ( get_intermediate_image_sizes() as $s ) {
							// Don't make or numeric sizes that appear
							if( is_integer( $s ) ) continue;
	
							if ( isset( $_wp_additional_image_sizes[$s]['width'] ) ) // For theme-added sizes
								$width = intval( $_wp_additional_image_sizes[$s]['width'] );
							else                                                     // For default sizes set in options
								$width = get_option( "{$s}_size_w" );
			
							if ( isset( $_wp_additional_image_sizes[$s]['height'] ) ) // For theme-added sizes
								$height = intval( $_wp_additional_image_sizes[$s]['height'] );
							else                                                      // For default sizes set in options
								$height = get_option( "{$s}_size_h" );
			
							if ( isset( $_wp_additional_image_sizes[$s]['crop'] ) )   // For theme-added sizes
								$crop = intval( $_wp_additional_image_sizes[$s]['crop'] );
							else                                                      // For default sizes set in options
								$crop = get_option( "{$s}_crop" );

							echo "<option value='$s' ".(selected($options['og_img_size'], $s)).">$s (${width} x ${height}".($crop ? " cropped" : "").")</option>\n";
						}
					?>
					</select>
				</td><td>
					<p>The WordPress Media Library size name for the image used
					in the Open Graph meta tag. Generally this would be
					"thumbnail" (currently defined as <?php echo
					get_option('thumbnail_size_w'); ?> x <?php echo
					get_option('thumbnail_size_h'); ?>, <?php echo
					get_option('thumbnail_crop') == "1" ? "" : "not"; ?>
					cropped), or other size names like "medium", "large", etc.
					Choose a size name that is at least 200px or more in width
					and height, and preferably cropped. You can use the <a
					href="http://wordpress.org/extend/plugins/simple-image-sizes/"
					target="_blank">Simple Image Size</a> plugin (or others) to
					define your own custom size names on the <a
					href="options-media.php">Settings -&gt; Media</a> page. I
					would suggest creating a "facebook-thumbnail" size name of
					200 x 200 (or larger) cropped, to manage the size of Open
					Graph images independently from those of your theme.</p>
				</td>
			</tr>

			<tr>
				<th scope="row">Default Image ID</th>
				<td valign="top"><input type="text" name="ngfb_options[og_def_img_id]" size="6"
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
				<th scope="row">Default Image URL</th>
				<td colspan="2"><input type="text" name="ngfb_options[og_def_img_url]" size="80"
					value="<?php echo $options['og_def_img_url']; ?>" style="width:100%;"/>

					<p>You can specify a Default Image URL (including the
					http:// prefix) instead of a Default Image ID. This would
					allow you to use an image outside of a managed collection
					(Media Library or NextGEN Gallery). The image should be at
					least 200px or more in width and height. If both are
					specified, the Default Image ID takes precedence.</p>
				</td>
			</tr>

			<tr>
				<th scope="row" nowrap>Use Default on Multi-Entry Pages</th>
				<td valign="top"><input name="ngfb_options[og_def_on_home]" type="checkbox" value="1" 
					<?php if (isset($options['og_def_on_home'])) { checked('1', $options['og_def_on_home']); } ?> />
				</td><td>
					<p>Check this box if you would like to use the default
					image on page types with more than one entry (homepage,
					archives, categories, etc.). If you leave this un-checked,
					NextGEN Facebook will attempt to use the first featured
					image, [singlepic] shortcode, or IMG HTML tag within the
					list of entries on the page.</p>
				</td>
			</tr>

			<tr>
				<th scope="row" nowrap>Content Begins at a Paragraph</th>
				<td valign="top"><input name="ngfb_options[og_desc_strip]" type="checkbox" value="1" 
					<?php if (isset($options['og_desc_strip'])) { checked('1', $options['og_desc_strip']); } ?> />
				</td><td>
					<p>For a page or post <i>without</i> an excerpt, the plugin
					will ignore all text until the first &lt;p&gt; paragraph
					HTML tag in <i>the content</i>. If an excerpt exists, then it's
					complete text will be used instead.</p>
				</td>
			</tr>

			<tr>
				<th scope="row" nowrap>Use WP-WikiBox for Description</th>
				<td valign="top"><input name="ngfb_options[og_desc_wiki]" type="checkbox" value="1" 
					<?php if (isset($options['og_desc_wiki'])) { checked('1', $options['og_desc_wiki']); } ?> />
				</td><td>
					<p><strong>Advanced setting:</strong> You must have the
					WP-WikiBox plugin installed for this option to do anything.
					NextGEN Facebook can ignore the content of your post or
					page, and retrieve it from Wikipedia instead. Here's how it
					works; if the page or post does NOT have an excerpt, the
					plugin will check for (page or post) tags and use the their
					names to retrieve content from Wikipedia. If no tags are
					defined, then the post or page title will be used. This
					option was created for photography based websites, which
					may not have any original content, aside from their
					photographs.</p>
				</td>
			</tr>

			<tr>
				<th scope="row">Max Description Length</th>
				<td valign="top"><input type="text" size="6" name="ngfb_options[og_desc_len]" 
					value="<?php echo $options['og_desc_len']; ?>" />
				</td><td>
					<p>The maximum length of text, from your post / page
					excerpt or content, used in the Open Graph description tag.
					The length must be 160 characters or more (the default is
					300).</p>
				</td>
			</tr>

			<tr>
				<th scope="row" nowrap>Add NextGEN Gallery Tags</th>
				<td valign="top"><input name="ngfb_options[og_ngg_tags]" type="checkbox" value="1" 
					<?php if (isset($options['og_ngg_tags'])) { checked('1', $options['og_ngg_tags']); } ?> />
				</td><td>
					<p>If the featured or default image is from a NextGEN
					Gallery, then add that image's tags to the Open Graph tag
					list.</p>
				</td>
			</tr>

			<tr>
				<th scope="row">Facebook Admin(s)</th>
				<td valign="top"><input type="text" size="40" name="ngfb_options[og_admins]" 
					value="<?php echo $options['og_admins']; ?>" style="width:250px;" />
				</td><td>
					<p>Enter one of more Facebook account names (generally your
					own), seperated with a comma. When you are viewing your own
					Facebook wall, your account name is located in the URL.
					(example:
					https://www.facebook.com/<b>account_name</b>).</p>
				</td>
			</tr>

			<tr>
				<th scope="row">Facebook App ID</th>
				<td valign="top"><input type="text" size="40" name="ngfb_options[og_app_id]" 
					value="<?php echo $options['og_app_id']; ?>" style="width:250px;" />
				</td><td>
					<p>If you have a Facebook App ID, enter it here.</p>
				</td>
			</tr>
		</table>
			</div>
		</div>
		<div class="postbox">
			<h3>Facebook Button Settings</h3>
			<div class="inside">	
		<table class="form-table">
			<tr valign="top">
				<th scope="row" nowrap>Enable Facebook Button(s)</th>
				<td valign="top"><input name="ngfb_options[fb_enable]" type="checkbox" value="1" 
					<?php if (isset($options['fb_enable'])) { checked('1', $options['fb_enable']); } ?> />
				</td><td>
					<p>Add Facebook "Like" (and optionally "Send") button to
					your posts and pages. The default is not to include the
					Facebook button.</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row" nowrap>Include on Homepage</th>
				<td valign="top"><input name="ngfb_options[fb_on_home]" type="checkbox" value="1"
					<?php if (isset($options['fb_on_home'])) { checked('1', $options['fb_on_home']); } ?> /></td>
			</tr>

			<tr valign="top">
				<th scope="row" nowrap>Add Facebook Send Button</th>
				<td valign="top"><input name="ngfb_options[fb_send]" type="checkbox" value="true"
					<?php if (isset($options['fb_send'])) { checked('true', $options['fb_send']); } ?> /></td>
			</tr>
			
			<tr>
				<th scope="row">Button Layout Style</th>
				<td valign="top">
					<select name='ngfb_options[fb_layout]' style="width:250px;">
						<option value='standard' <?php selected($options['fb_layout'], 'standard'); ?>>Standard</option>
						<option value='button_count' <?php selected($options['fb_layout'], 'button_count'); ?>>Button Count</option>
						<option value='box_count' <?php selected($options['fb_layout'], 'box_count'); ?>>Box Count</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<th scope="row">Show Facebook Faces</th>
				<td valign="top">
					<select name='ngfb_options[fb_show_faces]' style="width:250px;">
						<option value='true' <?php selected($options['fb_show_faces'], 'true'); ?>>Show</option>
						<option value='false' <?php selected($options['fb_show_faces'], 'false'); ?>>Hide</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<th scope="row">Button Font</th>
				<td valign="top">
					<select name='ngfb_options[fb_font]' style="width:250px;">
						<option value='arial' <?php selected('arial', $options['fb_font']); ?>>Arial</option>
						<option value='lucida grande' <?php selected('lucida grande', $options['fb_font']); ?>>Lucida Grande</option>
						<option value='segoe ui' <?php selected('segoe ui', $options['fb_font']); ?>>Segoe UI</option>
						<option value='tahoma' <?php selected('tahoma', $options['fb_font']); ?>>Tahoma</option>
						<option value='trebuchet ms' <?php selected('trebuchet ms', $options['fb_font']); ?>>Trebuchet MS</option>
						<option value='verdana' <?php selected('verdana', $options['fb_font']); ?>>Verdana</option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row">Button Color Scheme</th>
				<td valign="top">
					<select name='ngfb_options[fb_colorscheme]' style="width:250px;">
						<option value='light' <?php selected('light', $options['fb_colorscheme']); ?>>Light</option>
						<option value='dark' <?php selected('dark', $options['fb_colorscheme']); ?>>Dark</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<th scope="row">Facebook Action Name</th>
				<td valign="top">
					<select name='ngfb_options[fb_action]' style="width:250px;">
						<option value='like' <?php selected('like', $options['fb_action']); ?>>Like</option>
						<option value='recommend' <?php selected('recommend', $options['fb_action']); ?>>Recommend</option>
					</select>
				</td>
			</tr>				
		</table>
			</div>
		</div>
		<div class="postbox">
			<h3>Plugin Settings</h3>
			<div class="inside">	
		<table class="form-table">
			<tr>
				<th scope="row" nowrap>Reset Settings on Activate</th>
				<td valign="top"><input name="ngfb_options[ngfb_reset]" type="checkbox" value="1" 
					<?php if (isset($options['ngfb_reset'])) { checked('1', $options['ngfb_reset']); } ?> />
				</td><td>
					<p>Check this option to reset NextGEN Facebook settings to
					their default values <u>when you deactivate, and then
					reactivate the plugin</u>.</p>
				</td>
			</tr>
		</table>
			</div><!-- .inside -->
		</div><!-- .postbox -->
	</div><!-- .metabox-holder -->

		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />

	</form>
</div>
	<?php	
}

// Sanitize and validate input
function ngfb_validate_options($input) {

	$input['og_img_size'] = wp_filter_nohtml_kses($input['og_img_size']);
	if (! $input['og_img_size']) $input['og_img_size'] = "thumbnail";

	$input['og_def_img_url'] = wp_filter_nohtml_kses($input['og_def_img_url']);
	$input['og_admins'] = wp_filter_nohtml_kses($input['og_admins']);
	$input['og_app_id'] = wp_filter_nohtml_kses($input['og_app_id']);

	if ( ! is_numeric( $input['og_def_img_id'] ) ) $input['og_def_img_id'] = null;

	if ( ! $input['og_desc_len'] 
		|| ! is_numeric( $input['og_desc_len'] ) 
		|| ! $input['og_desc_len'] > 160 )
			$input['og_desc_len'] = 160;

	if ( ! isset( $input['og_desc_strip'] ) ) $input['og_desc_strip'] = null;
	$input['og_desc_strip'] = ( $input['og_desc_strip'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['og_desc_wiki'] ) ) $input['og_desc_wiki'] = null;
	$input['og_desc_wiki'] = ( $input['og_desc_wiki'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['og_ngg_tags'] ) ) $input['og_ngg_tags'] = null;
	$input['og_ngg_tags'] = ( $input['og_ngg_tags'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['og_def_on_home'] ) ) $input['og_def_on_home'] = null;
	$input['og_def_on_home'] = ( $input['og_def_on_home'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['fb_enable'] ) ) $input['fb_enable'] = null;
	$input['fb_enable'] = ( $input['fb_enable'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['fb_on_home'] ) ) $input['fb_on_home'] = null;
	$input['fb_on_home'] = ( $input['fb_on_home'] == 1 ? 1 : 0 );
	
	if ( ! isset( $input['fb_send'] ) ) $input['fb_send'] = null;
	$input['fb_send'] = ( $input['fb_send'] == "true" ? "true" : "false" );

	if ( ! isset( $input['ngfb_reset'] ) ) $input['ngfb_reset'] = null;
	$input['ngfb_reset'] = ( $input['ngfb_reset'] == 1 ? 1 : 0 );
	
	return $input;
}

// Display a Settings link on the main Plugins page
function ngfb_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$ngfb_links = '<a href="'.get_admin_url().'options-general.php?page=ngfb">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $ngfb_links );
	}

	return $links;
}

function ngfb_facebook_buttons($content){

	$options = get_option('ngfb_options');

	if (! $options['fb_enable']) return $content;

	$fb_send = $options['fb_send'];
	if($fb_send == '') { $fb_send = 'true'; }
	
	$fb_layout = $options['fb_layout'];
	if($fb_layout == '') { $fb_layout = 'button_count'; }
	
	$fb_show_faces = $options['fb_show_faces'];
	if($fb_show_faces == '') { $fb_show_faces = 'false'; }
	
	$fb_colorscheme = $options['fb_colorscheme'];
	if($fb_colorscheme == '') { $fb_colorscheme = 'light'; }
	
	$fb_action = $options['fb_action'];
	if($fb_action == '') { $fb_action = 'like'; }
	
	$fb_font = $options['fb_font'];
	if($fb_font == '') { $fb_font = 'arial'; }
	
	$fb_buttons = '<div id="fb-root"></div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>';
	$fb_buttons .= '<fb:like href="'.get_permalink($post->ID).'"
		send="'.$fb_send.'" layout="'.$fb_layout.'" width="400"
		show_faces="'.$fb_show_faces.'" font="'.$fb_font.'" action="'.$fb_action.'"
		colorscheme="'.$fb_colorscheme.'"></fb:like>';

	if( !is_feed() && !is_home() ) {
		$content .= $fb_buttons;
	} elseif ( $options['fb_on_home'] ) { 
		$content .= $fb_buttons;
	}

	return $content;
}
add_action('the_content', 'ngfb_facebook_buttons');

function ngfb_get_ngg_thumb_tags( $thumb_id ) {

    if (! method_exists( 'nggdb', 'find_image' ) ) return;

	if ( is_string($thumb_id) && substr($thumb_id, 0, 4) == 'ngg-') {

		$thumb_id = substr($thumb_id, 4);
		$img_tags = wp_get_object_terms($thumb_id, 'ngg_tag', 'fields=names');
	}
	return $img_tags;
}

// thumb_id must be 'ngg-#'
function ngfb_get_ngg_thumb_url( $thumb_id ) {

    if (! method_exists( 'nggdb', 'find_image' ) ) return;

	if ( is_string($thumb_id) && substr($thumb_id, 0, 4) == 'ngg-') {

		$thumb_id = substr($thumb_id, 4);
		$image = nggdb::find_image($thumb_id);	// returns an nggImage object

		if ($image != null) {

			$options = get_option('ngfb_options');
			$size = $options['og_img_size'];

			global $_wp_additional_image_sizes;
			$tmp = get_intermediate_image_sizes();
	
			if ( isset( $_wp_additional_image_sizes[$size]['width'] ) )
				$width = intval( $_wp_additional_image_sizes[$size]['width'] );
			else $width = get_option( "{$size}_size_w" );

			if ( isset( $_wp_additional_image_sizes[$size]['height'] ) )
				$height = intval( $_wp_additional_image_sizes[$size]['height'] );
			else $height = get_option( "{$size}_size_h" );

			if ( isset( $_wp_additional_image_sizes[$size]['crop'] ) )
				$crop = intval( $_wp_additional_image_sizes[$size]['crop'] );
			else $crop = get_option( "{$size}_crop" );

			$crop = ( $crop == 1 ? 'crop' : '' );

			// Check to see if the image already exists
			$image_url = $image->cached_singlepic_file( $width, $height, $crop );

			// If not, then use the dynamic image url
			if (empty($image_url)) 
				$image_url = trailingslashit(site_url()).'index.php?callback=image&amp;pid='.$thumb_id.'&amp;width='.$width.'&amp;height='.$height.'&amp;mode='.$crop;
		}
    }
    return $image_url;
}

function ngfb_str_decode( $str ) {
	$str = preg_replace('/&#8230;/', '...', $str );
	return preg_replace('/&#\d{2,5};/ue', "ctx_aj_utf8_entity_decode('$0')", $str );
}

function ngfb_utf8_entity_decode( $entity ) {
	$convmap = array( 0x0, 0x10000, 0, 0xfffff );
	return mb_decode_numericentity( $entity, $convmap, 'UTF-8' );
}

function ngfb_add_meta() {
	global $post;
	
	$options = get_option('ngfb_options');

	if( !is_feed() && !is_home() ) {
		$content .= $fb_buttons;
	} else if ( isset($options['fb_on_home']) && ( $options['fb_on_home'] != "" ) ) { 
		$content .= $fb_buttons;
	}

	/* define the list of tags
	-------------------------------------------------------------- */

	if ( is_single() || is_page() ) {
			
			$tag_names = array();
			$page_tags = wp_get_post_tags( $post->ID );
			foreach ( $page_tags as $tag ) $tag_names[] = $tag->name;
			
			if ( $options['og_ngg_tags'] ) {
				if ( function_exists('has_post_thumbnail') && 
					has_post_thumbnail( $post->ID ) ) {

					$thumb_id = get_post_thumbnail_id( $post->ID );

					if ( is_string( $thumb_id ) && substr( $thumb_id, 0, 4 ) == 'ngg-' ) {
						$image_tags = ngfb_get_ngg_thumb_tags( $thumb_id );
					}

				} elseif ( $options['og_def_img_id'] != '' && $options['og_def_img_id_pre'] == 'ngg') {

						$image_tags = ngfb_get_ngg_thumb_tags( $options['og_def_img_id_pre'].'-'.$options['og_def_img_id'] );
				}
				if ( is_array( $image_tags ) ) $tag_names = array_merge( $tag_names, $image_tags );
			}

	}

	/* define the image_url
	-------------------------------------------------------------- */

	if ( is_single() || is_page() || ! $options['og_def_on_home'] ) {

		if ( function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
	
			$thumb_id = get_post_thumbnail_id( $post->ID );
	
			// if the post thumbnail id has the form ngg- then it's a NextGEN image
			if ( is_string( $thumb_id ) && substr( $thumb_id, 0, 4 ) == 'ngg-' ) {
				$image_url = ngfb_get_ngg_thumb_url( $thumb_id );
			} else {
				$out = wp_get_attachment_image_src( $thumb_id, $options['og_img_size'] );
				$image_url = $out[0];
			}
		}
	
		// if there's no featured image, search post for images and display first one
		if( ! $image_url ) {

			if ( preg_match_all( '/\[singlepic[^\]]+id=([0-9]+)/i', $post->post_content, $match) > 0 ) {
				$thumb_id = $match[1][0];					
				$image_url = ngfb_get_ngg_thumb_url( 'ngg-'.$thumb_id );
			} elseif ( preg_match_all( '/<img[^>]+src=[\'"]([^\'"]+)[\'"]/i', $post->post_content, $match) > 0 ) {
				$image_url = $match[1][0];					
			}
		}
	}

	if ( ! $image_url && $options['og_def_img_id'] != '') {
		if ($options['og_def_img_id_pre'] == 'ngg') {
			$image_url = ngfb_get_ngg_thumb_url( $options['og_def_img_id_pre'].'-'.$options['og_def_img_id'] );
		} else {
			$out = wp_get_attachment_image_src( $options['og_def_img_id'], $options['og_img_size'] );
			$image_url = $out[0];
		}
	}

	if( ! $image_url ) $image_url = $options['og_def_img_url'];	// if still empty, use the default url.

	/* define the site_title
	-------------------------------------------------------------- */

	$site_title = get_bloginfo( 'name', 'display' );

	/* define the page_title
	-------------------------------------------------------------- */

	global $page, $paged;
	$page_title = trim( wp_title( '|', false, 'right' ), ' |');

	if ( is_singular() ) {

		$parent_id  = $post->post_parent;
		if ($parent_id) $parent_title = get_the_title($parent_id);
		if ($parent_title) $page_title .= ' ('.$parent_title.')';

	} elseif ( is_category() ) { 
		// wordpress does not include parents - we want the parents too
		$page_title = ngfb_str_decode( single_cat_title( '', false ) );
		$page_title = trim( get_category_parents( get_cat_ID( $page_title ), false, ' | ', false ), ' |');
		$page_title = preg_replace('/\.\.\. \| /', '... ', $page_title);	// my own little quirk ;-)
	}

	if ( ! $page_title ) $page_title = $site_title;

	if ( $paged >= 2 || $page >= 2 ) 
		$page_title .= ' | ' . sprintf( 'Page %s', max( $paged, $page ) );	// add a page number if necessary

	/* define the page_desc
	-------------------------------------------------------------- */

	if ( is_singular() ) {
	
		if ( has_excerpt($post->ID) ) {

			$page_text = strip_tags( get_the_excerpt( $post->ID ) );

		// use WP-WikiBox for content, if allowed and activated
		} elseif ( $options['og_desc_wiki'] && function_exists( 'wikibox_summary' ) ) {

			$tags = wp_get_post_tags( $post->ID );
	
			if ( $tags ) foreach ( $tags as $tag ) $page_text .= wikibox_summary( $tag->name, '', false ); 
			else $page_text .= wikibox_summary( the_title( '', '', false ), '', false );

		} 
	
		// fallback to regular content
		if ( ! $page_text ) {

			$page_text = $post->post_content;
			
			// ignore everything until the first paragraph tag
			if (  $options['og_desc_strip'] )
				$page_text = preg_replace( '/^.*<p>/s', '', $page_text );

		}

		$page_text = preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( $page_text ) );
		$page_text = substr( $page_text, 0, $options['og_desc_len'] );
		$page_text = preg_replace( '/[^ ]*$/', '', $page_text );	// remove trailing bits of words
		$page_desc = esc_attr( trim( $page_text ) );

	} elseif ( is_author() ) { 

		the_post();
		$page_desc = sprintf( 'Authored by %s', get_the_author_meta( 'display_name' ) );
		$author_desc = get_the_author_meta( 'description' );
		if ($author_desc) $page_desc .= ': '.$author_desc;	// add the author's profile description, if there is one

	} elseif ( is_tag() ) {

		$page_desc = sprintf( 'Tagged with %s', single_tag_title('', false) );
		$tag_desc = esc_attr( trim( substr( preg_replace( '/[\r\n]/', ' ', 
			strip_tags( strip_shortcodes( tag_description() ) ) ), 0, 
			$options['og_desc_len'] - strlen($page_desc) ) ) );
		if ($tag_desc) $page_desc .= ': '.$tag_desc;	// add the tag description, if there is one

	} elseif ( is_category() ) { 

		$page_desc = sprintf( '%s Category', single_cat_title( '', false ) ); 
		$cat_desc = esc_attr( trim( substr( preg_replace( '/[\r\n]/', ' ', 
			strip_tags( strip_shortcodes( category_description() ) ) ), 0, 
			$options['og_desc_len'] - strlen($page_desc) ) ) );
		if ($cat_desc) $page_desc .= ': '.$cat_desc;	// add the category description, if there is one
	}
	elseif ( is_day() ) $page_desc = sprintf( 'Daily Archives for %s', get_the_date() );
	elseif ( is_month() ) $page_desc = sprintf( 'Monthly Archives for %s', get_the_date('F Y') );
	elseif ( is_year() ) $page_desc = sprintf( 'Yearly Archives for %s', get_the_date('Y') );
	else $page_desc = get_bloginfo( 'description', 'display' );

	/* define the page_type
	-------------------------------------------------------------- */

	if ( is_single() || is_page() ) $page_type = "article";
	else $page_type = "blog";	// 'website' could also be another choice

?>

<!-- NextGEN Facebook Plugin Open Graph Tags: BEGIN -->
<?php
	if ( $options['og_admins'] )
		echo '<meta property="fb:admins" content="', $options['og_admins'], '" />', "\n";

	if ( $options['og_app_id'] )
		echo '<meta property="fb:app_id" content="', $options['og_app_id'], '" />', "\n";
?>
<meta property="og:site_name" content="<?php echo $site_title; ?>" />
<meta property="og:title" content="<?php echo $page_title; ?>" />
<meta property="og:type" content="<?php echo $page_type ?>" />
<meta property="og:image" content="<?php echo $image_url; ?>" />
<meta property="og:description" content="<?php echo $page_desc; ?>" />
<meta property="og:url" content="http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" />
<?php
	if ($page_type == "article") {

			echo '<meta property="article:published_time" content="', 
				get_the_date('c'), '" />', "\n";

			echo '<meta property="article:modified_time" content="',
				get_the_modified_date('c'), '" />', "\n";

			if ($options['og_art_section'])
				echo '<meta property="article:section" content="', 
					$options['og_art_section'], '" />', "\n";

			echo '<meta property="article:author" content="',
				trailingslashit(site_url()), 'author/', 
				get_the_author_meta( 'user_login', 
				$post->post_author ), '/" />', "\n";

			foreach( $tag_names as $tag )
				echo '<meta property="article:tag" content="', $tag, '" />', "\n";
	}
?>
<!-- NextGEN Facebook Plugin Open Graph Tags: END -->

<?php
}
add_action('wp_head', 'ngfb_add_meta');

// It would be better to use '<head prefix="">' but WP doesn't offer hooks into <head>
function ngfb_add_og_doctype( $output ) {
	return $output . '
		xmlns:og="http://ogp.me/ns"
		xmlns:fb="http://ogp.me/ns/fb"';
}
add_filter('language_attributes', 'ngfb_add_og_doctype');

?>
