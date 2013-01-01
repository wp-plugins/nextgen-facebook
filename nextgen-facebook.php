<?php
/*
Plugin Name: NextGEN Facebook OG
Plugin URI: http://wordpress.org/extend/plugins/nextgen-facebook/
Description: Adds Open Graph meta tags for Facebook, G+, LinkedIn, etc., plus sharing buttons for FB, G+, Twitter, LinkedIn, Pinterest, tumblr.
Version: 2.2
Author: Jean-Sebastien Morisset
Author URI: http://surniaulula.com/

This plugin is based on the "WP Facebook Like Send & Open Graph Meta v1.2.3"
plugin by Marvie Pons.

The NextGEN Facebook OG plugin adds Open Graph meta tags to all webpage
headers, including the "artical" object type for posts and pages. The featured
image thumbnails, from a NextGEN Gallery or Media Library, are also correctly
listed in the "image" meta tag. This plugin goes well beyond any other plugins
I know in handling various archive-type webpages. It will create appropriate
title and description meta tags for category, tag, date based archive (day,
month, or year), author webpages and search results. You can also, optionally,
add Facebook, Google+, Twitter, LinkedIn, Pinterest and tumblr sharing buttons
to post and page content (above or bellow), as a widget, or even use a function
from your templates.

The Open Graph protocol enables any web page to become a rich object in a
social graph. For instance, this is used on Facebook to allow any web page to
have the same functionality as any other object on Facebook. The Open Graph
meta tags are read by almost all social websites, including Facebook, Google
(Search and Google+), and LinkedIn.

NextGEN Facebook OG was specifically written to support featured images located
in a NextGEN Gallery, but also works just as well with the WordPress Media
Library. The NextGEN Gallery plugin is not required to use this plugin - all
features work just as well without it. The image used in the Open Graph meta
tag is chosen in this sequence; a featured image from a NextGEN Gallery or
WordPress Media Library, the first NextGEN [singlepic] or IMG HTML tag in the
content, a default image defined in the plugin settings. If none of these
conditions can be satisfied, then the Open Graph image tag will be left empty.

This plugin is being actively developed and supported. Post your comments and
suggestions to the NextGEN Facebook OG support page at
http://wordpress.org/support/plugin/nextgen-facebook.

Copyright 2012 Jean-Sebastien Morisset (http://surniaulula.com/)

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.

*/

register_activation_hook( __FILE__, 'ngfb_add_default_options' );
register_uninstall_hook( __FILE__, 'ngfb_delete_plugin_options' );

add_action( 'admin_init', 'ngfb_requires_wordpress_version' );
add_action( 'admin_init', 'ngfb_init' );
add_action( 'admin_menu', 'ngfb_add_options_page' );
add_action( 'widgets_init', 'ngfb_widgets_init' );

add_filter( 'wp_head', 'ngfb_add_meta_tags', 20 );
add_filter( 'the_content', 'ngfb_add_content_buttons', 20 );
add_filter( 'language_attributes', 'ngfb_add_og_doctype' );
add_filter( 'plugin_action_links', 'ngfb_plugin_action_links', 10, 2 );

function ngfb_widgets_init() {
        if ( ! is_blog_installed() ) return;
	register_widget( 'ngfb_widget_buttons' );
}

// add menu page
function ngfb_add_options_page() {
	add_options_page('NextGEN Facebook OG Plugin', 'NextGEN Facebook', 'manage_options', 'ngfb', 'ngfb_render_form');
}

function ngfb_requires_wordpress_version() {
	global $wp_version;
	$plugin = plugin_basename( __FILE__ );
	$plugin_data = get_plugin_data( __FILE__, false );
	if ( version_compare($wp_version, "3.0", "<" ) ) {
		if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "'".$plugin_data['Name']."' requires WordPress 3.0 or higher and has been deactivated. Please upgrade WordPress and try again.<br /><br />Back to <a href='".admin_url()."'>WordPress admin</a>." );
		}
	}
}

// it would be better to use '<head prefix="">' but WP doesn't offer hooks into <head>
function ngfb_add_og_doctype( $output ) {
	return $output.' xmlns:og="http://ogp.me/ns" xmlns:fb="http://ogp.me/ns/fb"';
}

// delete options table entries ONLY when plugin deactivated AND deleted
function ngfb_delete_plugin_options() {
	delete_option('ngfb_options');
}

// define default option settings
function ngfb_add_default_options() {
	$options = ngfb_get_options();
	if ( ( $options['ngfb_reset'] == 1 ) || ( ! is_array( $options ) ) ) {
		delete_option('ngfb_options');	// remove old options, if any
		$options = ngfb_get_default_options();
		update_option('ngfb_options', $options);
	}
}

function ngfb_get_default_options() {
	return array (
		'og_art_section' => '',
		'og_img_size' => 'thumbnail',
		'og_def_img_id_pre' => '',
		'og_def_img_id' => '',
		'og_def_img_url' => '',
		'og_def_on_home' => 1,
		'og_def_on_search' => 1,
		'og_ngg_tags' => '',
		'og_title_len' => '100',
		'og_desc_len' => '300',
		'og_desc_strip' => '',
		'og_desc_wiki' => '',
		'og_wiki_tag' => 'Wiki-',
		'og_admins' => '',
		'og_app_id' => '',
		'buttons_on_home' => '',
		'buttons_on_ex_pages' => '',
		'buttons_location' => 'bottom',
		'fb_enable' => '',
		'fb_send' => 1,
		'fb_layout' => 'button_count',
		'fb_colorscheme' => 'light',
		'fb_font' => 'arial',
		'fb_show_faces' => 'false',
		'fb_action' => 'like',
		'gp_enable' => '',
		'gp_action' => 'plusone',
		'gp_size' => 'medium',
		'gp_annotation' => 'bubble',
		'twitter_enable' => '',
		'twitter_count' => 'horizontal',
		'twitter_size' => 'medium',
		'twitter_dnt' => 1,
		'linkedin_enable' => '',
		'linkedin_counter' => 'right',
		'pin_enable' => '',
		'pin_count_layout' => 'horizontal',
		'pin_img_size' => 'large',
		'pin_caption' => 'both',
		'pin_cap_len' => '500',
		'tumblr_enable' => '',
		'tumblr_button_style' => 'share_1',
		'tumblr_desc_len' => '300',
		'tumblr_photo' => 1,
		'tumblr_img_size' => 'large',
		'tumblr_caption' => 'both',
		'tumblr_cap_len' => '500',
		'inc_fb:admins' => 1,
		'inc_fb:app_id' => 1,
		'inc_og:site_name' => 1,
		'inc_og:title' => 1,
		'inc_og:type' => 1,
		'inc_og:url' => 1,
		'inc_og:description' => 1,
		'inc_og:image' => 1,
		'inc_og:video' => 1,
		'inc_og:video:width' => 1,
		'inc_og:video:height' => 1,
		'inc_og:video:type' => 1,
		'inc_article:author' => 1,
		'inc_article:published_time' => 1,
		'inc_article:modified_time' => 1,
		'inc_article:section' => 1,
		'inc_article:tag' => 1,
		'ngfb_reset' => '',
		'ngfb_debug' => '',
		'ngfb_filter_content' => 1,
	);
}

// init plugin options to white list our options
function ngfb_init() {
	register_setting( 'ngfb_plugin_options', 'ngfb_options', 'ngfb_validate_options' );
}

// get the options, upgrade the option names (if necessary), and validate their values
function ngfb_get_options() {
	
	$options = get_option( 'ngfb_options' );

	// update option field names BEFORE using ngfb_validate_options()
	if ( ! $options['og_def_img_url'] && $options['og_def_img'] ) {
		$options['og_def_img_url'] = $options['og_def_img'];
		delete_option($options['og_def_img']);
	}
	if ( ! $options['og_def_on_home'] && $options['og_def_home']) {
		$options['og_def_on_home'] = $options['og_def_home'];
		delete_option($options['og_def_home']);
	}

	// default values for new options
	foreach ( ngfb_get_default_options() as $opt => $def )
		if ( ! array_key_exists( $opt, $options ) ) $options[$opt] = $def;
	unset( $opt, $def );

	return ngfb_validate_options( $options );
}


// sanitize and validate input
function ngfb_validate_options( $options ) {

	$def_opts = ngfb_get_default_options();
	$options['og_def_img_url'] = wp_filter_nohtml_kses($options['og_def_img_url']);
	$options['og_admins'] = wp_filter_nohtml_kses($options['og_admins']);
	$options['og_app_id'] = wp_filter_nohtml_kses($options['og_app_id']);

	if ( ! is_numeric( $options['og_def_img_id'] ) ) 
		$options['og_def_img_id'] = $def_opts['og_def_img_id'];

	// integer options that cannot be zero
	foreach ( array( 
		'og_title_len', 
		'og_desc_len', 
		'tumblr_desc_len', 
		'tumblr_cap_len',
		'pin_cap_len', 
	) as $opt ) {
		if ( ! $options[$opt] || ! is_numeric( $options[$opt] ) )
			$options[$opt] = $def_opts[$opt];
	}
	unset( $opt );
	if ( $options['og_desc_len'] < 160 ) $options['og_desc_len'] = 160;

	// options that cannot be blank
	foreach ( array( 
		'og_img_size', 
		'buttons_location', 
		'gp_action', 
		'gp_size', 
		'gp_annotation', 
		'twitter_count', 
		'twitter_size', 
		'linkedin_counter',
		'tumblr_button_style',
		'tumblr_img_size',
		'tumblr_caption',
		'pin_count_layout',
		'pin_img_size',
		'pin_caption',
	) as $opt ) {
		$options[$opt] = wp_filter_nohtml_kses( $options[$opt] );
		if (! $options[$opt] ) $options[$opt] = $def_opts[$opt];
	}
	unset( $opt );

	// true/false options
	foreach ( array( 
		'og_def_on_home',
		'og_def_on_search',
		'og_ngg_tags',
		'og_desc_strip',
		'og_desc_wiki',
		'buttons_on_home',
		'buttons_on_ex_pages',
		'fb_enable',
		'fb_send',
		'gp_enable',
		'twitter_enable',
		'twitter_dnt',
		'linkedin_enable',
		'tumblr_enable',
		'tumblr_photo',
		'pin_enable',
		'inc_fb:admins',
		'inc_fb:app_id',
		'inc_og:site_name',
		'inc_og:title',
		'inc_og:type',
		'inc_og:url',
		'inc_og:description',
		'inc_og:image',
		'inc_og:video',
		'inc_og:video:width',
		'inc_og:video:height',
		'inc_og:video:type',
		'inc_article:author',
		'inc_article:modified_time',
		'inc_article:published_time',
		'inc_article:section',
		'inc_article:tag',
		'ngfb_reset',
		'ngfb_debug',
		'ngfb_filter_content',
	) as $opt ) {
		$options[$opt] = ( $options[$opt] ? 1 : 0 );
	}
	unset( $opt );

	return $options;
}

// render the Plugin options form
function ngfb_render_form() {

	$options = ngfb_get_options( 'ngfb_options' );

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
	natsort ( $article_sections );
?>
	<style type="text/css">
		.form-table tr {
			vertical-align:top;
		}
		.form-table td {
			padding:2px 6px 2px 6px;
		}
		.form-table th {
			text-align:right;
			white-space:nowrap;
			padding:2px 6px 2px 6px;
			width:180px;
		}
		.form-table th#social {
			font-weight:bold;
			text-align:left;
			background-color:#eee;
			border:1px solid #ccc;
		}
		.form-table th#meta {
			width:220px;
		}
		.form-table td select,
		.form-table td input {
			margin:0 0 5px 0;
		}
		.form-table td input[type=radio] {
			vertical-align:top;
			margin:4px 4px 4px 0;
		}
		.form-table td select {
			width:250px;
		}
		.wrap {
			font-size:1em;
			line-height:1.3em;
		}
		.wrap h2 {
			margin:0 0 10px 0;
		}
		.wrap p {
			text-align:justify;
			line-height:1.3em;
			margin:5px 0 5px 0;
		}
		.btn_wizard_column {
			white-space:nowrap;
		}
		.btn_wizard_example {
			display:inline-block;
			width:155px;
		}
	</style>
	<div class="wrap" id="ngfb">
	<div class="icon32" id="icon-options-general"><br></div>
	<h2>NextGEN Facebook OG Plugin</h2>

	<p>The NextGEN Facebook OG plugin adds Open Graph HTML meta tags to your webpages. If your post or page has a featured image, it will be included as well - even if it's located in a NextGEN Gallery. All options bellow are optional. You can enable social sharing buttons, define a default image, etc.</p>

	<p>The image used in Open Graph HTML meta tags will be determined in this sequence; a featured image from a NextGEN Gallery or WordPress Media Library, the first NextGEN [singlepic] shortcode or &lt;img&gt; HTML tag in the content, and the default image defined here. If none of these conditions can be satisfied, then the Open Graph image tag will be left out.</p>

	<div class="updated" style="margin:10px 0;">
	<p style="text-align:center">We don't ask for donations, but if you like the NextGEN Facebook OG plugin, <a href="http://wordpress.org/support/view/plugin-reviews/nextgen-facebook?rate=5#postform"><strong>please take a moment to rate it</strong></a> on the WordPress website. Thank you. :-)</p>
	</div>

	<div class="metabox-holder">
	<form name="ngfb" method="post" action="options.php">
	<?php settings_fields('ngfb_plugin_options'); ?>

	<div id="ngfb-ogsettings" class="postbox">
	<!--<div class="handlediv" title="Click to toggle"><br /></div>--><h3 class="hndle"><span>Open Graph Settings</span></h3>
	<div class="inside">	
	<table class="form-table">
	<tr>
		<th>Website Topic</th>
		<td>
			<select name='ngfb_options[og_art_section]'>
			<?php
				echo '<option value="" ', selected($options['og_art_section'], '', false), '></option>', "\n";
				foreach ( $article_sections as $s ) {
					echo '<option value="', $s, '" ',
						selected( $options['og_art_section'], $s, false),
							'>', $s, '</option>', "\n";
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
		<p>The <a href="options-media.php">WordPress Media Library "size name"</a> for the image used in the Open Graph HTML meta tag. Generally this would be "thumbnail" (currently defined as <?php echo get_option('thumbnail_size_w'), ' x ', get_option('thumbnail_size_h'), ', ', get_option('thumbnail_crop') == "1" ? "" : "not"; ?> cropped), or another size name like "medium", "large", etc. Choose a size name that is at least 200px or more in width and height, and preferably cropped. You can use the <a href="http://wordpress.org/extend/plugins/simple-image-sizes/" target="_blank">Simple Image Size</a> plugin (or others) to define your own custom size names on the <a href="options-media.php">Media Settings</a> admin page. I would suggest creating a "facebook-thumbnail" size name of 200 x 200 (or larger) cropped, to manage the size of Open Graph images independently from those of your theme.</p>
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
		<p>You can specify a Default Image URL (including the http:// prefix) instead of a Default Image ID. This allows you to use an image outside of a managed collection (Media Library or NextGEN Gallery). The image should be at least 200px or more in width and height. If both the Default Image ID and URL are defined, the Default Image ID takes precedence.</p>
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
		<p>The maximum length of text, from your post / page excerpt or content, used in the Open Graph description tag. The length must be 160 characters or more (default is 300).</p>
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
	<!--<div class="handlediv" title="Click to toggle"><br /></div>--><h3 class="hndle"><span>Open Graph Meta Tags</span></h3>
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
	<!--<div class="handlediv" title="Click to toggle"><br /></div>--><h3 class="hndle"><span>Social Button Settings</span></h3>
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
				foreach ( range(1, 4) as $i ) {
	                    		echo '
						<div class="btn_wizard_column share_', $i, '">
							<div class="btn_wizard_example clearfix">
								<label for="share_', $i, '">
									<input type="radio" id="share_', $i, '" name="ngfb_options[tumblr_button_style]" 
										value="share_', $i, '" ', checked( 'share_'.$i, $options['tumblr_button_style'], false ), '/>
									<img src="http://platform.tumblr.com/v1/share_', $i, '.png" height="20" class="share_button_image"/>
								</label>
							</div>
							<div class="btn_wizard_example clearfix">
								<label for="share_', $i, 'T">
									<input type="radio" id="share_', $i, 'T" name="ngfb_options[tumblr_button_style]" 
										value="share_', $i, 'T" ', checked( 'share_'.$i.'T', $options['tumblr_button_style'], false ), '/>
									<img src="http://platform.tumblr.com/v1/share_', $i, 'T.png" height="20" class="share_button_image"/>
								</label>
							</div>
						</div>
					';
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
	</table>
	</div><!-- .inside -->
	</div><!-- .postbox -->

	<div id="ngfb-pluginsettings" class="postbox">
	<!--<div class="handlediv" title="Click to toggle"><br /></div>--><h3 class="hndle"><span>Plugin Settings</span></h3>
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

// display a settings link on the main plugins page
function ngfb_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$ngfb_links = '<a href="'.get_admin_url().'options-general.php?page=ngfb">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $ngfb_links );
	}

	return $links;
}

function ngfb_get_social_buttons( $ids = array(), $opts = array() ) {

	global $post;

	// make sure we have at least $post->ID or $opts['url'] defined
	if ( ! isset( $post->ID ) && empty( $opts['url' ] ) ) {
		$opts['url'] = $_SERVER['HTTPS'] ? 'https://' : 'http://';
		$opts['url'] .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}

	$options = ngfb_get_options();
	$buttons = '';

	foreach ( $ids as $id )
		$buttons .= eval ( "if ( function_exists( 'ngfb_${id}_button') ) 
			return ngfb_${id}_button( \$options, \$opts );" );

	if ( $buttons ) $buttons = "
<!-- NextGEN Facebook OG Social Buttons BEGIN -->
<div class=\"ngfb-buttons\">\n$buttons\n</div>
<!-- NextGEN Facebook OG Social Buttons END -->\n\n";

	return $buttons;
}

function ngfb_add_content_buttons( $content ) {

	// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
	if ( is_page() && ngfb_is_excluded() ) return $content;

	$options = ngfb_get_options();
	$buttons = '';

	if ( is_singular() || $options['buttons_on_home'] ) {

		if ($options['fb_enable']) $buttons .= ngfb_facebook_button( $options );
		if ($options['gp_enable']) $buttons .= ngfb_gplus_button( $options );
		if ($options['twitter_enable']) $buttons .= ngfb_twitter_button( $options );
		if ($options['linkedin_enable']) $buttons .= ngfb_linkedin_button( $options );
		if ($options['pin_enable']) $buttons .= ngfb_pinterest_button( $options );
		if ($options['tumblr_enable']) $buttons .= ngfb_tumblr_button( $options );
		if ($buttons) {
			$buttons = "
<!-- NextGEN Facebook OG Social Buttons BEGIN -->
<div class=\"ngfb-content-buttons ngfb-buttons\">\n$buttons\n</div>
<!-- NextGEN Facebook OG Social Buttons END -->\n\n";

			if ($options['buttons_location'] == "top") $content = $buttons.$content;
			else $content = $content.$buttons;
		}
	}
	return $content;
}

/* tumblr button */

function ngfb_pinterest_button( &$options, &$opts = array() ) {

	global $post;
	$button = '';
	if ( ! $opts['pin_count_layout'] ) $opts['pin_count_layout'] = $options['pin_count_layout'];
	if ( ! $opts['url'] ) $opts['url'] = get_permalink( $post->ID );
	if ( ! $opts['size'] ) $opts['size'] = $options['pin_img_size'];
	if ( ! $opts['caption'] ) $opts['caption'] = ngfb_get_caption( $options['pin_caption'], $options['pin_cap_len'] );
	if ( ! $opts['photo'] ) {
		if ( ! $opts['pid'] ) {
			// return if function doesn't exist, or there's no featured image
			if ( ! function_exists('has_post_thumbnail') || ! has_post_thumbnail( $post->ID ) ) return;
			$opts['pid'] = get_post_thumbnail_id( $post->ID );
		}
		if ( $opts['pid'] ) {
			// if the post thumbnail id has the form ngg- then it's a NextGEN image
			if ( is_string( $opts['pid'] ) && substr( $opts['pid'], 0, 4 ) == 'ngg-' ) {
				$opts['photo'] = ngfb_get_ngg_thumb_url( $opts['pid'], $opts['size'] );
			} else {
				$out = wp_get_attachment_image_src( $opts['pid'], $opts['size'] );
				$opts['photo'] = $out[0];
			}
		}
	}
	// define the button, based on what we have
	if ( $opts['photo'] ) {
		$button .= '?url=' . urlencode( $opts['url'] );
		$button .= '&media='. urlencode( ngfb_cdn_linker( $opts['photo'] ) );
		$button .= '&description=' . urlencode( ngfb_str_decode( $opts['caption'] ) );
	}
	// if we have something, then complete the button code
	if ( $button ) {
		$button = '
			<!-- url = ' . $opts['url'] . ' -->
			<!-- photo = ' . $opts['photo'] . ' -->
			<div class="pinterest-button"><a href="http://pinterest.com/pin/create/button/' . $button . '" 
				class="pin-it-button" count-layout="' . $opts['pin_count_layout'] . '" 
				title="Share on Pinterest"><img border="0" 
				src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>
			<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
		';
	}
	return $button;	
}

/* tumblr button */

function ngfb_tumblr_button( &$options, &$opts = array() ) {

	global $post;
	$button = '';
	if ( ! $opts['tumblr_button_style'] ) $opts['tumblr_button_style'] = $options['tumblr_button_style'];
	if ( ! $opts['url'] ) $opts['url'] = get_permalink( $post->ID );
	if ( ! $opts['size'] ) $opts['size'] = $options['tumblr_img_size'];
	if ( ! $opts['embed'] ) $opts['embed'] = ngfb_get_video_embed( );
	if ( ! $opts['title'] ) $opts['title'] = ngfb_get_title( );
	if ( ! $opts['caption'] ) $opts['caption'] = ngfb_get_caption( $options['tumblr_caption'], $options['tumblr_cap_len'] );
	if ( ! $opts['description'] ) $opts['description'] = ngfb_get_description( $options['tumblr_desc_len'], '...' );
	if ( ! $opts['photo'] ) {
		// only use featured image if $options['tumblr_photo'] allows it
		if ( ! $opts['pid'] && $options['tumblr_photo'] && function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
			$opts['pid'] = get_post_thumbnail_id( $post->ID );
		}
		if ( $opts['pid'] ) {
			// if the post thumbnail id has the form ngg- then it's a NextGEN image
			if ( is_string( $opts['pid'] ) && substr( $opts['pid'], 0, 4 ) == 'ngg-' ) {
				$opts['photo'] = ngfb_get_ngg_thumb_url( $opts['pid'], $opts['size'] );
			} else {
				$out = wp_get_attachment_image_src( $opts['pid'], $opts['size'] );
				$opts['photo'] = $out[0];
			}
		}
	}
	if ( ! $opts['quote'] && get_post_format( $post->ID ) == 'quote' ) {
		$opts['quote'] = ngfb_get_quote();
	}
	// define the button, based on what we have
	if ( $opts['photo'] ) {
		$button .= 'photo?source='. urlencode( ngfb_cdn_linker( $opts['photo'] ) );
		$button .= '&caption=' . urlencode( ngfb_str_decode( $opts['caption'] ) );
		$button .= '&clickthru=' . urlencode( $opts['url'] );
	} elseif ( $opts['embed'] ) {
		$button .= 'video?embed=' . urlencode( $opts['embed'] );
		$button .= '&caption=' . urlencode( ngfb_str_decode( $opts['caption'] ) );
	} elseif ( $opts['quote'] ) {
		$button .= 'quote?quote=' . urlencode( $opts['quote'] );
		$button .= '&source=' . urlencode( ngfb_str_decode( $opts['title'] ) );
	} elseif ( $opts['url'] ) {
		$button .= 'link?url=' . urlencode( $opts['url'] );
		$button .= '&name=' . urlencode( ngfb_str_decode( $opts['title'] ) );
		$button .= '&description=' . urlencode( ngfb_str_decode( $opts['description'] ) );
	}
	// if we have something, then complete the button code
	if ( $button ) {
		$button = '
			<!-- url = ' . $opts['url'] . ' -->
			<!-- photo = ' . $opts['photo'] . ' -->
			<div class="tumblr-button"><a href="http://www.tumblr.com/share/'. $button . '" 
				title="Share on tumblr"><img border="0"
				src="http://platform.tumblr.com/v1/' . $opts['tumblr_button_style'] . '.png"></a></div>
			<script src="http://platform.tumblr.com/v1/share.js"></script>
		';
	}
	return $button;
}

/* Facebook button */

function ngfb_facebook_button( &$options, &$opts = array() ) {

	if ( ! $opts['url'] ) { 
		global $post; 
		$opts['url'] = get_permalink($post->ID);
	}

	$fb_send = $options['fb_send'];
	if ( $fb_send ) $fb_send = 'true';
	else $fb_send = 'false';
	
	$fb_layout = $options['fb_layout'];
	if ( ! $fb_layout ) $fb_layout = 'button_count';
	
	$fb_show_faces = $options['fb_show_faces'];
	if ( $fb_show_faces ) $fb_show_faces = 'true';
	else $fb_show_faces = 'false';
	
	$fb_colorscheme = $options['fb_colorscheme'];
	if ( ! $fb_colorscheme ) $fb_colorscheme = 'light';
	
	$fb_action = $options['fb_action'];
	if ( ! $fb_action ) $fb_action = 'like';
	
	$fb_font = $options['fb_font'];
	if ( ! $fb_font ) $fb_font = 'arial';

	$button .= '<div class="facebook-button"><span class="fb-root"><fb:like 
		href="' . $opts['url'] . '"
		send="' . $fb_send . '" layout="' . $fb_layout . '" width="400"
		show_faces="' . $fb_show_faces . '" font="' . $fb_font . '" action="' . $fb_action . '"
		colorscheme="' . $fb_colorscheme . '"></fb:like></span></div>' . "\n";

	$button .= '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>'."\n";

	return $button;
}

/* Google+ button */

function ngfb_gplus_button( &$options, &$opts = array() ) {

	if ( ! $opts['url'] ) { 
		global $post; 
		$opts['url'] = get_permalink( $post->ID );
	}

	$gp_action = $options['gp_action'];
	if ( ! $gp_action ) $gp_action = 'plusone';

	$gp_size = $options['gp_size'];
	if ( ! $gp_size ) $gp_size = 'medium';
	
	$gp_annotation = $options['gp_annotation'];
	if ( ! $gp_annotation ) $gp_annotation = 'bubble';

	// html-5 syntax
	$button .= '<div class="gplus-button g-plusone-button"><span ';

	if ( $gp_action == 'share' )
		$button .= 'class="g-plus" data-action="share"';
	else
		$button .= 'class="g-plusone"';

	$button .= ' data-size="'.$gp_size.'" 
		data-annotation="'.$gp_annotation.'" 
		data-href="' . $opts['url'] . '"></span></div>'."\n";
	
	$button .= '
		<script type="text/javascript"> ( 
			function() {
				var po = document.createElement("script");
				po.type = "text/javascript"; 
				po.async = true;
				po.src = "https://apis.google.com/js/plusone.js";
				var s = document.getElementsByTagName("script")[0]; 
				s.parentNode.insertBefore(po, s);
			}
		)(); </script>
	';
	return $button;
}

/* Twitter button */

function ngfb_twitter_button( &$options, &$opts = array() ) {

	if ( ! $opts['url'] ) { 
		global $post; 
		$opts['url'] = get_permalink( $post->ID );
	}

	$twitter_count = $options['twitter_count'];
	if ( ! $twitter_count ) $twitter_count = 'horizontal';
	
	$twitter_size = $options['twitter_size'];
	if ( ! $twitter_size ) $twitter_size = 'medium';
	
	$twitter_dnt = $options['twitter_dnt'];
	if ( $twitter_dnt ) $twitter_dnt = 'true';
	else $twitter_dnt = 'false';
	
	$button .= '<a href="https://twitter.com/share" 
		class="twitter-button twitter-share-button" 
		data-url="' . $opts['url'] . '" 
		data-count="'.$twitter_count.'" 
		data-size="'.$twitter_size.'" 
		data-dnt="'.$twitter_dnt.'">Tweet</a>'."\n";

	$button .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="http://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

	return $button;
}

/* LinkedIn button */

function ngfb_linkedin_button( &$options, &$opts = array() ) {

	if ( ! $opts['url'] ) { 
		global $post; 
		$opts['url'] = get_permalink( $post->ID );
	}

	$linkedin_counter = $options['linkedin_counter'];
	if ( ! $linkedin_counter ) $linkedin_counter = 'right';

	$button .= "\n".'<div class="linkedin-button">';	
	$button .= '<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
		<script type="IN/Share" data-url="' . $opts['url'] . '"';
	if ($linkedin_counter) $button .= ' data-counter="'.$linkedin_counter.'"';
	$button .= '></script></div>'."\n";

	return $button;
}

function ngfb_get_ngg_thumb_tags( $thumb_id ) {

	if ( ! method_exists( 'nggdb', 'find_image' ) ) return;
	if ( is_string($thumb_id) && substr($thumb_id, 0, 4) == 'ngg-') {
		$thumb_id = substr($thumb_id, 4);
		$img_tags = wp_get_object_terms($thumb_id, 'ngg_tag', 'fields=names');
	}
	return $img_tags;
}

// thumb_id must be 'ngg-#'
function ngfb_get_ngg_thumb_url( $thumb_id, $size_name = 'thumbnail' ) {

	if ( ! method_exists( 'nggdb', 'find_image' ) ) return;

	if ( is_string( $thumb_id ) && substr($thumb_id, 0, 4) == 'ngg-') {

		$thumb_id = substr($thumb_id, 4);
		$image = nggdb::find_image($thumb_id);	// returns an nggImage object

		if ( ! empty( $image ) ) {

			$size = ngfb_get_size_values( $size_name );
			$crop = ( $size['crop'] == 1 ? 'crop' : '' );

			// check to see if the image already exists
			$image_url = $image->cached_singlepic_file( $size['width'], $size['height'], $crop );

			// if not, then use the dynamic image url
			if ( empty( $image_url ) ) 
				$image_url = trailingslashit( site_url() ) . 
					'index.php?callback=image&amp;pid=' . $thumb_id .
					'&amp;width=' . $size['width'] . 
					'&amp;height=' . $size['height'] . 
					'&amp;mode='.$crop;
		}
    }
    return $image_url;
}

function ngfb_add_meta_tags() {

	if ( defined('DISABLE_NGFB_OPEN_GRAPH') && DISABLE_NGFB_OPEN_GRAPH ) {
		echo "\n<!-- NextGEN Facebook OG Meta Tags DISABLED -->\n\n";
		return;
	}

	global $post;
	$debug = array();
	$options = ngfb_get_options();

	$og['fb:admins'] = $options['og_admins'];
	$og['fb:app_id'] = $options['og_app_id'];
	$og['og:url'] = $_SERVER['HTTPS'] ? 'https://' : 'http://';
	$og['og:url'] .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

	// ========
	// og:image
	// ========

	if ( is_singular() && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {

		$thumb_id = get_post_thumbnail_id( $post->ID );

		array_push( $debug, "function_exists(has_post_thumbnail) = " . function_exists('has_post_thumbnail') );
		array_push( $debug, "has_post_thumbnail(" . $post->ID . ") = " . has_post_thumbnail( $post->ID ) );
		array_push( $debug, "get_post_thumbnail_id(" . $post->ID . ") = " . $thumb_id );
		$debug_pre = "image_source = has_post_thumbnail / ";
		$debug_post = '('.$thumb_id.','.$options['og_img_size'].')';

		// if the post thumbnail id has the form ngg- then it's a NextGEN image
		if ( is_string( $thumb_id ) && substr( $thumb_id, 0, 4 ) == 'ngg-' ) {
			array_push( $debug, $debug_pre . 'ngfb_get_ngg_thumb_url' . $debug_post );
			$og['og:image'] = ngfb_get_ngg_thumb_url( $thumb_id, $options['og_img_size'] );
		} else {
			array_push( $debug, $debug_pre.'wp_get_attachment_image_src'.$debug_post );
			$out = wp_get_attachment_image_src( $thumb_id, $options['og_img_size'] );
			$og['og:image'] = $out[0];
		}
	}

	// if there's no featured image, search post for images and display first one
	if ( ! $og['og:image'] ) {
		if ( is_singular() ||
			( is_search() && ! $options['og_def_on_search'] ) ||
			( ! is_singular() && ! is_search() && ! $options['og_def_on_home'] ) ) {

			$debug_pre = "image_source = preg_match / ";
			$content = $post->post_content;

			// check for singlepic before applying filter to content
			if ( preg_match( '/\[(singlepic)[^\]]+id=([0-9]+)/i', $content, $match ) ) {
				$src = $match[1];
				$id = $match[2];
				array_push( $debug, $debug_pre.$src." / ".$id );
				$og['og:image'] = ngfb_get_ngg_thumb_url( 'ngg-'.$id, $options['og_img_size'] );
			} else {
				// we're in wp_head, so we can apply the content filter without creating a recursive loop
				$content = ngfb_apply_content_filter( $content, $options['ngfb_filter_content'] );

				// img attributes in order of preference
				if ( preg_match( '/<img[^>]*? (share-'.$options['og_img_size'].'|share|src)=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $match ) ) {
					$img = $match[0];
					$src = $match[1];
					$og['og:image'] = $match[2];
					if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $img, $match) ) $width = $match[1];
					if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $img, $match) ) $height = $match[1];
					array_push( $debug , $debug_pre."img $src / ".$og['og:image']." / src width=$width x height=$height" );

					$size = ngfb_get_size_values( $options['og_img_size'] );

					// if we're picking up an img for src, make sure it's width and height is large enough
					if ( $src == 'share-'.$options['og_img_size'] || $src == 'share' || 
						( $src == 'src' && $width >= $size['width'] && $height >= $size['height'] ) ) {

						// fix relative URLs - just in case
						if ( ! preg_match( '/:\/\//', $og['og:image'] ) ) {
							// if URL starts with slash, then it's from the DocRoot, so add site_url()
							if ( preg_match( '/^\//', $og['og:image'] ) )
								$og['og:image'] = site_url() . $og['og:image'];
							// if it's relative to current page, then use current URL
							else {
								$og['og:image'] = $_SERVER['HTTPS'] ? 'https://' : 'http://';
								$og['og:image'] .= trailingslashit( $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"] ).$og['og:image'];
							}
							array_push( $debug , $debug_pre."relative URL fixed / ".$og['og:image'] );
						}
					} else {
						array_push( $debug , $debug_pre."img src size too small / og_img_size is width=".$size['width']." x height=".$size['height'] );
						$og['og:image'] = '';
					}
				}
			}
		}
	}

	// use the default image
	if ( ! $og['og:image'] ) {
		if ( is_singular() ||
			( is_search() && $options['og_def_on_search'] ) ||
			( ! is_singular() && ! is_search() && $options['og_def_on_home'] ) ) {

			if ( $options['og_def_img_id'] != '' ) {
				$debug_pre = "image_source = default / ";
				if ($options['og_def_img_id_pre'] == 'ngg') {
					$img_id = $options['og_def_img_id_pre'].'-'.$options['og_def_img_id'];
					array_push( $debug, $debug_pre."ngfb_get_ngg_thumb_url(".$img_id.','.$options['og_img_size'].')' );
					$og['og:image'] = ngfb_get_ngg_thumb_url( $img_id, $options['og_img_size'] );
				} else {
					array_push( $debug, $debug_pre."wp_get_attachment_image_src(".$options['og_def_img_id'].",".$options['og_img_size'].")" );
					$out = wp_get_attachment_image_src( $options['og_def_img_id'], $options['og_img_size'] );
					$og['og:image'] = $out[0];
				}
			}
			// if still empty, use the default url (if one is defined, empty string otherwise)
			if ( ! $og['og:image'] ) 
				$og['og:image'] = $options['og_def_img_url'];
		}
	}

	// ========
	// og:video
	// ========

	if ( preg_match( '/<iframe[^>]*? src=[\'"]([^\'"]+\/(embed|video)\/[^\'"]+)[\'"][^>]*>/i', $post->post_content, $match ) ) {

		$iframe_html = $match[0];
		$og['og:video'] = $match[1];
		$og['og:video:type'] = "application/x-shockwave-flash";

		if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $iframe_html, $match) ) $og['og:video:width'] = $match[1];
		if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $iframe_html, $match) ) $og['og:video:height'] = $match[1];

		$debug_pre = "video_source = preg_match / iframe / ";
		array_push( $debug, $debug_pre."embed|video / ".$og['og:video'] );
		array_push( $debug, $debug_pre."width x height / ".$og['og:video:width']." x ".$og['og:video:height'] );

		// make sure we have all fields before changing the og:image (to that of a video frame, for example)
		if ( $og['og:video'] && $og['og:video:width'] > 0 && $og['og:video:height'] > 0 ) {

			// check for youtube url
			if ( preg_match( '/^.*youtube\.com\/.*\/([^\/]+)$/i', $og['og:video'], $match ) ) {
				$og['og:image'] = "http://img.youtube.com/vi/".$match[1]."/0.jpg";
				array_push( $debug, $debug_pre."video img / ".$og['og:image'] );
			}
			// add more sites here as we find them...
		}
	}

	// ============
	// og:site_name
	// ============

	$og['og:site_name'] = get_bloginfo( 'name', 'display' );	

	// ========
	// og:title
	// ========

	$og['og:title'] = ngfb_get_title( $options['og_title_len'], '...' );

	// ==============
	// og:description
	// ==============

	// we're in wp_head, so we can use apply the content filter without creating a recursive loop
	$og['og:description'] = ngfb_get_description( $options['og_desc_len'], '...', $options['ngfb_filter_content'] );

	// =====================
	// og:type and article:*
	// =====================

	if ( is_singular() ) {

		$og['og:type'] = "article";
		$og['article:author'] = trailingslashit(site_url()).'author/'.get_the_author_meta( 'user_login', $post->post_author ).'/';
		$og['article:modified_time'] = get_the_modified_date('c');
		$og['article:published_time'] = get_the_date('c');
		$og['article:section'] = $options['og_art_section'];
		$og['article:tag'] = array();

		$page_tags = wp_get_post_tags( $post->ID );
		$tag_prefix = isset( $options['og_wiki_tag'] ) ? $options['og_wiki_tag'] : '';

		foreach ( $page_tags as $tag ) {
			$tag_name = $tag->name;
			if ( $tag_prefix )
				$tag_name = preg_replace( "/^$tag_prefix/", "", $tag_name );
			array_push( $og['article:tag'], $tag_name );
		}
		unset ( $tag );
			
		if ( $options['og_ngg_tags'] ) {
			if ( function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
				$thumb_id = get_post_thumbnail_id( $post->ID );
				if ( is_string( $thumb_id ) && substr( $thumb_id, 0, 4 ) == 'ngg-' )
					$image_tags = ngfb_get_ngg_thumb_tags( $thumb_id );

			} elseif ( $options['og_def_img_id'] != '' && $options['og_def_img_id_pre'] == 'ngg')
				$image_tags = ngfb_get_ngg_thumb_tags( $options['og_def_img_id_pre'].'-'.$options['og_def_img_id'] );
			
			if ( is_array( $image_tags ) ) 
				$og['article:tag'] = array_merge( $og['article:tag'], $image_tags );
		}

	} else $og['og:type'] = "blog";	// 'website' could also be another choice

	/* Add the Open Graph Meta Tags */

	echo "\n<!-- NextGEN Facebook OG Meta Tags BEGIN -->\n";
	if ( $options['ngfb_debug'] ) {
		echo "<!--\nOptions Array:\n";
		if ( ! empty( $options ) ) ksort( $options );
		foreach ( $options as $opt => $val ) echo "\t$opt = $val\n";
		unset ( $opt, $val );
		echo "Debug Array:\n";
		foreach ( $debug as $val ) echo "\t$val\n";
		unset ( $val );
		echo "-->\n";
	}
	if ( ! empty( $og ) ) ksort( $og );
	foreach ( $og as $name => $val ) {
		if ( $options['inc_'.$name] && $val ) {
			if ( is_array ( $og[$name] ) ) {
				foreach ( $og[$name] as $el ) echo ngfb_get_meta_tag( $name, $el );
				unset ( $el );
			} else echo ngfb_get_meta_tag( $name, $val );
		}
	}
	unset ( $name, $val );
	echo "<!-- NextGEN Facebook OG Meta Tags END -->\n\n";
}

function ngfb_get_meta_tag( $name, $val = '' ) {
	$charset = get_bloginfo( 'charset' );
	$val = htmlentities( ngfb_strip_tags( ngfb_str_decode( $val ) ), ENT_QUOTES, $charset, false );
	return '<meta property="' . $name . '" content="' . $val . '" />' . "\n";
}

function ngfb_str_decode( $str ) {
	$str = preg_replace('/&#8230;/', '...', $str );
	return preg_replace('/&#\d{2,5};/ue', "ngfb_utf8_entity_decode('$0')", $str );
}

function ngfb_utf8_entity_decode( $entity ) {
	$convmap = array( 0x0, 0x10000, 0, 0xfffff );
	return mb_decode_numericentity( $entity, $convmap, 'UTF-8' );
}

function ngfb_get_video_embed() {

	global $post;
	if ( preg_match( '/<iframe[^>]*? src=[\'"]([^\'"]+\/(embed|video)\/[^\'"]+)[\'"][^>]*>[^>]*<\/iframe>/i', 
		$post->post_content, $match ) ) {
		return $match[0];
	}
	return;
}

function ngfb_get_quote() {

	global $post;
	$page_text = '';

	if ( has_excerpt( $post->ID ) ) $page_text = get_the_excerpt( $post->ID );
	else $page_text = $post->post_content;		// fallback to regular content

	// don't run through ngfb_strip_tags() to keep formatting and HTML (if any)
	$page_text = strip_shortcodes( $page_text );	// remove any remaining shortcodes
	$page_text = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/i', ' ', $page_text);

	return $page_text;
}

function ngfb_get_caption( $type = 'title', $length = 300 ) {

	$caption = '';
	switch( strtolower( $type ) ) {
		case 'title':
			$caption = ngfb_get_title( $length, '...' );
			break;
		case 'excerpt':
			$caption = ngfb_get_description( $length, '...' );
			break;
		case 'both':
			$title = ngfb_get_title();
			$caption = $title . ' : ' . ngfb_get_description( $length - strlen( $title ) - 3, '...' );
			break;
	}
	return $caption;
}

function ngfb_get_title( $textlen = 100, $trailing = '' ) {

	global $post, $page, $paged;

	$title = trim( wp_title( '|', false, 'right' ), ' |');

	if ( is_singular() ) {

		$parent_id = $post->post_parent;
		if ($parent_id) $parent_title = get_the_title($parent_id);
		if ($parent_title) $title .= ' ('.$parent_title.')';

	} elseif ( is_category() ) { 

		// wordpress does not include parents - we want the parents too
		$title = ngfb_str_decode( single_cat_title( '', false ) );
		$title = trim( get_category_parents( get_cat_ID( $title ), false, ' | ', false ), ' |');
		$title = preg_replace('/\.\.\. \| /', '... ', $title);	// my own little quirk ;-)
	}

	if ( ! $title ) $title = get_bloginfo( 'name', 'display' );

	// add a page number if necessary
	if ( $paged >= 2 || $page >= 2 ) {
		$page_num = ' | ' . sprintf( 'Page %s', max( $paged, $page ) );
		$textlen = $textlen - strlen( $page_num );	// make room for the page number
	}

	return ngfb_limit_text_length( $title, $textlen, $trailing ) . $page_num;
}

// content can only be filtered when this function is called from wp_head(), so make it false by default
function ngfb_get_description( $textlen = 300, $trailing = '', $filter_content = false ) {

	global $post;
	$options = ngfb_get_options();
	$desc = '';

	if ( is_single() || is_page() ) {

		if ( has_excerpt( $post->ID ) ) {

			$desc = $post->post_excerpt;

		// use WP-WikiBox for page content, if option is true
		} elseif ( is_page() && $options['og_desc_wiki'] && function_exists( 'wikibox_summary' ) ) {

			$tags = wp_get_post_tags( $post->ID );

			if ( $tags ) {
				$tag_prefix = $options['og_wiki_tag'];
				foreach ( $tags as $tag ) {
					$tag_name = $tag->name;
					if ( $tag_prefix )
						if ( preg_match( "/^$tag_prefix/", $tag_name ) > 0 )
							$tag_name = preg_replace( "/^$tag_prefix/", "", $tag_name );
						else continue;
					$desc .= wikibox_summary( $tag_name, '', false ); 
				}
				unset ( $tag, $tag_name, $tag_prefix );
			} else $desc .= wikibox_summary( the_title( '', '', false ), '', false );
		} 

		if ( ! $desc ) $desc = $post->post_content;		// fallback to regular content

		// content can only be filtered when this function is called from wp_head()
		if ( $filter_content ) $content = ngfb_apply_content_filter( $content, $filter_content );

		// ignore everything until the first paragraph tag if $options['og_desc_strip'] is true
		if ( $options['og_desc_strip'] ) $desc = preg_replace( '/^.*?<p>/', '', $desc );	// question mark makes regex un-greedy

	} elseif ( is_author() ) { 

		the_post();
		$desc = sprintf( 'Authored by %s', get_the_author_meta( 'display_name' ) );
		$author_desc = preg_replace( '/[\r\n\t ]+/s', ' ', get_the_author_meta( 'description' ) );	// put everything on one line
		if ( $author_desc ) $desc .= ' : '.$author_desc;		// add the author's profile description, if there is one

	} elseif ( is_tag() ) {

		$desc = sprintf( 'Tagged with %s', single_tag_title( '', false ) );
		$tag_desc = preg_replace( '/[\r\n\t ]+/s', ' ', tag_description() );	// put everything on one line
		if ( $tag_desc ) $desc .= ' : '.$tag_desc;			// add the tag description, if there is one

	} elseif ( is_category() ) { 

		$desc = sprintf( '%s Category', single_cat_title( '', false ) ); 
		$cat_desc = preg_replace( '/[\r\n\t ]+/', ' ', category_description() );	// put everything on one line
		if ($cat_desc) $desc .= ' : '.$cat_desc;			// add the category description, if there is one
	}
	elseif ( is_day() ) $desc = sprintf( 'Daily Archives for %s', get_the_date() );
	elseif ( is_month() ) $desc = sprintf( 'Monthly Archives for %s', get_the_date('F Y') );
	elseif ( is_year() ) $desc = sprintf( 'Yearly Archives for %s', get_the_date('Y') );
	else $desc = get_bloginfo( 'description', 'display' );

	return ngfb_limit_text_length( $desc, $textlen, '...' );
}

// content can only be filtered when this function is called from wp_head(), so make it false by default
function ngfb_apply_content_filter( $content, $filter_content = false ) {

	// the_content filter breaks the ngg album shortcode, so skip it if that shortcode if found
	if ( ! preg_match( '/\[ *album[ =]/', $content ) && $filter_content )
		$content = apply_filters( 'the_content', $content );

	$content = preg_replace( '/[\r\n\t ]+/s', ' ', $content );	// put everything on one line
	$content = str_replace( ']]>', ']]&gt;', $content );
	$ngfb_msg = 'NextGEN Facebook OG Social Buttons';		// remove the social buttons that may have been added
	$content = preg_replace( "/<!-- $ngfb_msg BEGIN -->.*<!-- $ngfb_msg END -->/", ' ', $content );

	return $content;
}

function ngfb_limit_text_length( $text, $textlen = 300, $trailing = '' ) {

	$text = preg_replace( '/[\r\n\t ]+/s', ' ', $text );			// put everything on one line
	$text = preg_replace( '/<\/p>/i', ' ', $text);				// replace end of paragraph with a space
	$text = ngfb_strip_tags( $text );					// remove any remaining html tags
	if ( strlen( $text ) > $textlen ) {
		$text = substr( $text, 0, $textlen - strlen( $trailing ) );
		$text = trim( preg_replace( '/[^ ]*$/', '', $text ) );		// remove trailing bits of words
		$text = preg_replace( '/[,\.]*$/', '', $text );			// remove trailing puntuation
	} else $trailing = '';							// truncate trailing string if text is shorter than limit
	$text = esc_attr( $text ) . $trailing;					// trim and add trailing string (if provided)

	return $text;
}

function ngfb_strip_tags( $text ) {

	$text = strip_shortcodes( $text );					// remove any remaining shortcodes
	$text = preg_replace( '/<\?.*\?>/i', ' ', $text);			// remove php
	$text = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/i', ' ', $text);	// remove javascript
	$text = strip_tags( $text );						// remove html tags

	return trim( $text );
}

function ngfb_select_img_size( &$options, $option_name ) {

	global $_wp_additional_image_sizes;
	$size_names = get_intermediate_image_sizes();
	natsort( $size_names );

	echo '<select name="ngfb_options[', $option_name, ']">', "\n";
	
	foreach ( $size_names as $size_name ) {
		if ( is_integer( $size_name ) ) continue;
		$size = ngfb_get_size_values( $size_name );
		echo '<option value="', $size_name, '" ', 
			selected( $options[$option_name], $size_name, false ), '>', 
			$size_name, ' (', $size['width'], ' x ', $size['height'], 
			$size['crop'] ? " cropped" : "", ')</option>', "\n";
	}
	unset ( $size_name );

	echo '</select>', "\n";
}

function ngfb_get_size_values( $size_name ) {

	global $_wp_additional_image_sizes;

	if ( is_integer( $size_name ) ) return;
	
	if ( isset( $_wp_additional_image_sizes[$size_name]['width'] ) )
		$width = intval( $_wp_additional_image_sizes[$size_name]['width'] );
	else $width = get_option( "{$size_name}_size_w" );
		
	if ( isset( $_wp_additional_image_sizes[$size_name]['height'] ) )
		$height = intval( $_wp_additional_image_sizes[$size_name]['height'] );
	else $height = get_option( "{$size_name}_size_h" );
		
	if ( isset( $_wp_additional_image_sizes[$size_name]['crop'] ) )
		$crop = intval( $_wp_additional_image_sizes[$size_name]['crop'] );
	else $crop = get_option( "{$size_name}_crop" );

	return array( 'width' => $width, 'height' => $height, 'crop' => $crop );
}

function ngfb_is_excluded() {

	global $post;
	$options = ngfb_get_options();
	if ( is_page() && $post->ID && function_exists( 'ep_get_excluded_ids' ) && ! $options['buttons_on_ex_pages'] ) {
		$excluded_ids = ep_get_excluded_ids();
		$delete_ids = array_unique( $excluded_ids );
		if ( in_array( $post->ID, $delete_ids ) ) {
			return true;
		}
	}
	return false;
}

// if it's available, use CDN Linker to re-write URLs
function ngfb_cdn_linker( $url = '' ) {
	if ( class_exists( CDNLinksRewriterWordpress ) ) {
		$rewriter = new CDNLinksRewriterWordpress();
		$url = '"'.$url.'"';
		$url = trim( $rewriter->rewrite( $url ), "\"" );
	}
	return $url;
}

class ngfb_widget_buttons extends WP_Widget {

        function ngfb_widget_buttons() {
		$widget_ops = array( 'classname' => 'ngfb-widget-buttons',
			'description' => "The NextGEN Facebook OG social buttons widget
				is only visible on single posts, pages and attachments." );
                $this->WP_Widget( 'ngfb-widget-buttons', 'NGFB Social Buttons', $widget_ops );
        }

        function widget( $args, $instance ) {

		// only show widget on single posts, pages, and attachments
                if ( ! is_singular() ) return;

		// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
		if ( is_page() && ngfb_is_excluded() ) return;

                extract( $args );

                $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$buttons = array();
		foreach ( array( 
			'Facebook' => 'facebook', 
			'Google+' => 'gplus',
			'Twitter' => 'twitter',
			'LinkedIn' => 'linkedin',
			'Pinterest' => 'pinterest',
			'tumblr' => 'tumblr',
		) as $name => $id ) 
			if ( (int) $instance[$id] ) $buttons[] = $id;
		unset( $name, $id );

                echo $before_widget;
                if ( $title ) echo $before_title . $title . $after_title;
		echo ngfb_get_social_buttons( $buttons );
                echo $after_widget;
        }

        function update( $new_instance, $old_instance ) {
                $instance = $old_instance;
                $instance['title'] = strip_tags( $new_instance['title'] );
		foreach ( array( 
			'Facebook' => 'facebook', 
			'Google+' => 'gplus',
			'Twitter' => 'twitter',
			'LinkedIn' => 'linkedin',
			'Pinterest' => 'pinterest',
			'tumblr' => 'tumblr',
		) as $name => $id ) 
			$instance[$id] = (int) $new_instance[$id] ? 1 : 0;
		unset( $name, $id );
                return $instance;
        }

        function form( $instance ) {
                $title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Share It';

                echo "\n", '<p><label for="', $this->get_field_id( 'title' ), '">Title (Leave Blank for No Title):</label>',
			'<input class="widefat" id="', $this->get_field_id( 'title' ), 
				'" name="', $this->get_field_name( 'title' ), 
				'" type="text" value="', $title, '" /></p>', "\n";

		foreach ( array( 
			'Facebook' => 'facebook', 
			'Google+' => 'gplus',
			'Twitter' => 'twitter',
			'LinkedIn' => 'linkedin',
			'Pinterest' => 'pinterest',
			'tumblr' => 'tumblr',
		) as $name => $id )
			echo '<p><label for="', $this->get_field_id( $id ), '">', 
				'<input id="', $this->get_field_id( $id ), 
				'" name="', $this->get_field_name( $id ), 
				'" value="1" type="checkbox" ', checked( 1 , $instance[$id] ), 
				' /> ', $name, '</label></p>', "\n";
		unset( $name, $id );
        }
}

?>
