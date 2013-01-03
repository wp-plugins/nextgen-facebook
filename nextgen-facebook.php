<?php
/*
Plugin Name: NextGEN Facebook OG
Plugin URI: http://wordpress.org/extend/plugins/nextgen-facebook/
Description: Adds Open Graph meta tags for Facebook, G+, LinkedIn, etc., plus sharing buttons for FB, G+, Twitter, LinkedIn, Pinterest, tumblr.
Version: 2.3
Author: Jean-Sebastien Morisset
Author URI: http://surniaulula.com/

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

if ( preg_match( '#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'] ) ) 
	die( 'You are not allowed to call this page directly.' );

if ( ! class_exists( 'ngfbLoader' ) ) {

	class ngfbLoader {

		var $minimum_wp_version = '3.0';

		var $social_options_prefix = array(
			'facebook' => 'fb', 
			'gplus' => 'gp',
			'twitter' => 'twitter',
			'linkedin' => 'linkedin',
			'pinterest' => 'pin',
			'stumbleupon' => 'stumble',
			'tumblr' => 'tumblr',
		);

		function ngfbLoader() {

			$this->define_constants();
			$this->load_dependencies();

			$this->plugin_name = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );

			register_activation_hook( $this->plugin_name, 'activate' );
			register_uninstall_hook( $this->plugin_name, array( 'ngfbLoader', 'uninstall') );

			add_action( 'admin_init', array( &$this, 'require_wordpress_version' ) );
			add_filter( 'language_attributes', array( &$this, 'add_og_doctype' ) );
			add_filter( 'wp_head', 'ngfb_add_meta_tags', 20 );
			add_filter( 'the_content', array( &$this, 'add_content_buttons' ), 20 );
			add_filter( 'wp_footer', array( &$this, 'add_content_footer' ), 10 );
			add_filter( 'plugin_action_links', 'ngfb_plugin_action_links', 10, 2 );
		}
		
		function define_constants() { 
			global $wp_version;

			// NGFB_OPEN_GRAPH_DISABLE
			// NGFB_MIN_IMG_SIZE_DISABLE

			if ( ! defined( 'NGFB_MIN_DESC_LEN' ) )
				define( 'NGFB_MIN_DESC_LEN', 160 );

			if ( ! defined( 'NGFB_MIN_IMG_WIDTH' ) )
				define( 'NGFB_MIN_IMG_WIDTH', 200 );

			if ( ! defined( 'NGFB_MIN_IMG_HEIGHT' ) )
				define( 'NGFB_MIN_IMG_HEIGHT', 200 );
		}

		function require_wordpress_version() {
			global $wp_version;
			$plugin = plugin_basename( __FILE__ );
			$plugin_data = get_plugin_data( __FILE__, false );
			if ( version_compare( $wp_version, $this->minimum_wp_version, "<" ) ) {
				if( is_plugin_active($plugin) ) {
					deactivate_plugins( $plugin );
					wp_die( '\'' . $plugin_data['Name'] . '\' requires WordPress ' . $this->minimum_wp_version . 
						' or higher and has been deactivated. Please upgrade WordPress and try again.
						<br /><br />Back to <a href="' . admin_url() . '">WordPress admin</a>.' );
				}
			}
		}

		// it would be better to use '<head prefix="">' but WP doesn't offer hooks into <head>
		function add_og_doctype( $output ) {
			return $output.' xmlns:og="http://ogp.me/ns" xmlns:fb="http://ogp.me/ns/fb"';
		}

		// define default option settings
		function activate() {
			$options = ngfb_get_options();
			if ( ( $options['ngfb_reset'] == 1 ) || ( ! is_array( $options ) ) ) {
				delete_option('ngfb_options');	// remove old options, if any
				$options = ngfb_get_default_options();
				update_option('ngfb_options', $options);
			}
		}

		// delete options table entries only when plugin deactivated and deleted
		function uninstall() {
			delete_option( 'ngfb_options' );
		}

		function load_dependencies() {
			require_once ( dirname ( __FILE__ ) . '/lib/widgets.php' );

			if ( is_admin() ) {
				require_once ( dirname ( __FILE__ ) . '/lib/admin.php' );
				$this->ngfbAdminPanel = new ngfbAdminPanel();
			}
		}

		function add_content_buttons( $content ) {

			// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
			if ( is_page() && ngfb_is_excluded() ) return $content;

			$options = ngfb_get_options();
			$buttons = '';

			if ( is_singular() || $options['buttons_on_home'] ) {

				$sorted_ids = array();
				foreach ( $this->social_options_prefix as $id => $prefix )
					if ( $options[$prefix.'_enable'] )
						// sort by number, then by name
						$sorted_ids[$options[$prefix.'_order'] . '-' . $id] = $id;
				ksort( $sorted_ids );

				foreach ( $sorted_ids as $id ) {
					$buttons .= eval ( "if ( function_exists( 'ngfb_${id}_button' ) ) 
						return ngfb_${id}_button( \$options );" );
				}

				if ( $buttons ) {
					$buttons = "
<!-- NextGEN Facebook OG Social Buttons BEGIN -->
<div class=\"ngfb-content-buttons ngfb-buttons\">\n$buttons\n</div>
<!-- NextGEN Facebook OG Social Buttons END -->\n\n";

					if ( $options['buttons_location'] == "top" ) $content = $buttons.$content;
					else $content = $content.$buttons;
				}
			}
			return $content;
		}

		function add_content_footer() {

			// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
			if ( is_page() && ngfb_is_excluded() ) return $content;

			$options = ngfb_get_options();

			if ( is_singular() || $options['buttons_on_home'] ) {
				echo "\n", '<!-- NextGEN Facebook OG Content Footer BEGIN -->', "\n";
				foreach ( $this->social_options_prefix as $id => $prefix )
					if ( $options[$prefix.'_enable'] ) 
						echo eval ( "if ( function_exists( 'ngfb_${id}_footer' ) ) 
							return ngfb_${id}_footer();" );
				unset ( $id, $prefix );
				echo "\n", '<!-- NextGEN Facebook OG Content Footer END -->', "\n\n";
			}
		}
	}

        global $ngfb;
        $ngfb = new ngfbLoader();
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
		'fb_order' => '1',
		'fb_send' => 1,
		'fb_layout' => 'button_count',
		'fb_colorscheme' => 'light',
		'fb_font' => 'arial',
		'fb_show_faces' => 'false',
		'fb_action' => 'like',
		'gp_enable' => '',
		'gp_order' => '2',
		'gp_action' => 'plusone',
		'gp_size' => 'medium',
		'gp_annotation' => 'bubble',
		'twitter_enable' => '',
		'twitter_order' => '3',
		'twitter_count' => 'horizontal',
		'twitter_size' => 'medium',
		'twitter_dnt' => 1,
		'linkedin_enable' => '',
		'linkedin_order' => '4',
		'linkedin_counter' => 'right',
		'pin_enable' => '',
		'pin_order' => '5',
		'pin_count_layout' => 'horizontal',
		'pin_img_size' => 'large',
		'pin_caption' => 'both',
		'pin_cap_len' => '500',
		'tumblr_enable' => '',
		'tumblr_order' => '7',
		'tumblr_button_style' => 'share_1',
		'tumblr_desc_len' => '300',
		'tumblr_photo' => 1,
		'tumblr_img_size' => 'large',
		'tumblr_caption' => 'both',
		'tumblr_cap_len' => '500',
		'stumble_enable' => '',
		'stumble_order' => '6',
		'stumble_badge' => '1',
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
		'fb_order', 
		'gp_order', 
		'twitter_order', 
		'linkedin_order', 
		'pin_order', 
		'pin_cap_len', 
		'tumblr_order', 
		'tumblr_desc_len', 
		'tumblr_cap_len',
		'stumble_order', 
		'stumble_badge',
	) as $opt ) {
		if ( ! $options[$opt] || ! is_numeric( $options[$opt] ) )
			$options[$opt] = $def_opts[$opt];
	}
	unset( $opt );
	if ( $options['og_desc_len'] < NGFB_MIN_DESC_LEN ) 
		$options['og_desc_len'] = NGFB_MIN_DESC_LEN;

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
		'pin_count_layout',
		'pin_img_size',
		'pin_caption',
		'tumblr_button_style',
		'tumblr_img_size',
		'tumblr_caption',
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
		'pin_enable',
		'tumblr_enable',
		'tumblr_photo',
		'stumble_enable',
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

// display a settings link on the main plugins page
function ngfb_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$ngfb_links = '<a href="'.get_admin_url().'options-general.php?page=ngfb">'.__('Settings').'</a>';
		//array_unshift( $links, $ngfb_links );	// make the settings link appear first
	}

	return $links;
}


/* You can enable social buttons in the content, use the social buttons widget,
 * and call the ngfb_get_social_buttons() function from your template(s) -- all
 * at the same time -- but all social buttons share the same settings from the
 * admin options page (the layout of each can differ by using the available CSS
 * class names - see the Other Notes tab
 * http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/ for
 * additional information).
 */
function ngfb_get_social_buttons( $ids = array(), $opts = array() ) {

	global $post;

	// make sure we have at least $post->ID or $opts['url'] defined
	if ( ! isset( $post->ID ) && empty( $opts['url' ] ) ) {
		$opts['url'] = $_SERVER['HTTPS'] ? 'https://' : 'http://';
		$opts['url'] .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}

	$options = ngfb_get_options();
	$buttons = '';

	foreach ( $ids as $id ) {

		$id = preg_replace( '/[^a-z]/', '', $id );	// sanitize input before eval

		$buttons .= eval ( "if ( function_exists( 'ngfb_${id}_button' ) ) 
			return ngfb_${id}_button( \$options, \$opts );" );

		$buttons .= eval ( "if ( function_exists( 'ngfb_${id}_footer' ) ) 
			return ngfb_${id}_footer();" );
	}

	if ( $buttons ) $buttons = "
<!-- NextGEN Facebook OG Social Buttons BEGIN -->
<div class=\"ngfb-buttons\">\n$buttons\n</div>
<!-- NextGEN Facebook OG Social Buttons END -->\n\n";

	return $buttons;
}

function ngfb_stumbleupon_button( &$options, &$opts = array() ) {

	global $post;
	$button = '';
	if ( ! $opts['stumble_badge'] ) $opts['stumble_badge'] = $options['stumble_badge'];
	if ( ! $opts['url'] ) $opts['url'] = get_permalink( $post->ID );

	$button = '<div class="stumble-button stumbleupon-button"><su:badge 
		layout="' . $opts['stumble_badge'] . '" 
		location="' . $opts['url'] . '"></su:badge></div>' . "\n";

	return $button;	
}
function ngfb_stumbleupon_footer() {
	return '
		<!-- StumbleUpon Javascript -->
		<script type="text/javascript">
		(
			function() { 
				var li = document.createElement("script");
				li.type = "text/javascript";
				li.async = true;
				li.src = ("https:" == document.location.protocol ? "https:" : "http:") + "//platform.stumbleupon.com/1/widgets.js";
				var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(li, s);
			}
		)();
		</script>
	';
}

function ngfb_pinterest_button( &$options, &$opts = array() ) {

	global $post;
	$button = '';
	if ( ! $opts['pin_count_layout'] ) $opts['pin_count_layout'] = $options['pin_count_layout'];
	if ( ! $opts['url'] ) $opts['url'] = get_permalink( $post->ID );
	if ( ! $opts['size'] ) $opts['size'] = $options['pin_img_size'];
	if ( ! $opts['caption'] ) $opts['caption'] = ngfb_get_caption( $options['pin_caption'], $options['pin_cap_len'] );
	if ( ! $opts['photo'] ) {
		if ( ! $opts['pid'] && function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
			$opts['pid'] = get_post_thumbnail_id( $post->ID );
		}
		if ( $opts['pid'] ) {
			// if the post thumbnail id has the form ngg- then it's a NextGEN image
			if ( is_string( $opts['pid'] ) && substr( $opts['pid'], 0, 4 ) == 'ngg-' ) {
				$opts['photo'] = ngfb_get_ngg_image_url( $opts['pid'], $opts['size'] );
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
			<div class="pinterest-button"><a href="http://pinterest.com/pin/create/button/' . $button . '" 
				class="pin-it-button" count-layout="' . $opts['pin_count_layout'] . '" 
				title="Share on Pinterest"><img border="0" 
				src="http://assets.pinterest.com/images/PinExt.png" title="Pin It" /></a></div>
		';
	}
	return $button;	
}
function ngfb_pinterest_footer() {
	return '
		<!-- Pinterest Javascript -->
		<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
	';
}

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

	// only use an image if $options['tumblr_photo'] allows it
	if ( ! $opts['photo'] && $options['tumblr_photo'] ) {
		if ( ! $opts['pid'] && function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
			$opts['pid'] = get_post_thumbnail_id( $post->ID );
		}
		if ( $opts['pid'] ) {
			// if the post thumbnail id has the form ngg- then it's a NextGEN image
			if ( is_string( $opts['pid'] ) && substr( $opts['pid'], 0, 4 ) == 'ngg-' ) {
				$opts['photo'] = ngfb_get_ngg_image_url( $opts['pid'], $opts['size'] );
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
	if ( $opts['photo'] && $options['tumblr_photo'] ) {
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
			<div class="tumblr-button"><a href="http://www.tumblr.com/share/'. $button . '" 
				title="Share on tumblr"><img border="0"
				src="http://platform.tumblr.com/v1/' . $opts['tumblr_button_style'] . '.png"></a></div>
		';
	}
	return $button;
}
function ngfb_tumblr_footer() {
	return '
		<!-- tumblr Javascript -->
		<script type="text/javascript" src="http://platform.tumblr.com/v1/share.js"></script>
	';
}

function ngfb_facebook_button( &$options, &$opts = array() ) {

	if ( ! $opts['url'] ) { 
		global $post; 
		$opts['url'] = get_permalink( $post->ID );
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

	return $button;
}
function ngfb_facebook_footer() {
	return '
		<!-- Facebook Javascript -->
		<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
	';
}

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
	
	return $button;
}
function ngfb_gplus_footer() {
	return '
		<!-- Google+ Javascript -->
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
}

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
	
	$button .= '<div class="twitter-button">
		<a href="https://twitter.com/share" 
			class="twitter-share-button"
			data-url="' . $opts['url'] . '" 
			data-count="'.$twitter_count.'" 
			data-size="'.$twitter_size.'" 
			data-dnt="'.$twitter_dnt.'">Tweet</a></div>'."\n";

	return $button;
}
function ngfb_twitter_footer() {
	return '
		<!-- Twitter Javascript -->
		<script type="text/javascript">
			! function( d, s, id ) {
				var js, fjs = d.getElementsByTagName( s )[0];
				if ( ! d.getElementById( id ) ){
					js = d.createElement( s );
					js.id = id;
					js.src = "http://platform.twitter.com/widgets.js";
					fjs.parentNode.insertBefore( js, fjs );
				}
			} ( document, "script", "twitter-wjs" );
		</script>
	';
}

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

/* Called from the ngfb_add_meta_tags() function to add NGG image tags to the
 * $og['article:tag'] variable.
 */
function ngfb_get_ngg_thumb_tags( $thumb_id ) {

	if ( ! method_exists( 'nggdb', 'find_image' ) ) return;
	if ( is_string($thumb_id) && substr($thumb_id, 0, 4) == 'ngg-') {
		$thumb_id = substr($thumb_id, 4);
		$img_tags = wp_get_object_terms($thumb_id, 'ngg_tag', 'fields=names');
	}
	return $img_tags;
}

/* Called from a variety of locations to get an image URL for an NGG picture ID
 * and a media size name. The thumb_id must be formatted as 'ngg-#'.
 */
function ngfb_get_ngg_image_url( $thumb_id, $size_name = 'thumbnail' ) {

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

/* Filter for wp_head().
 */
function ngfb_add_meta_tags() {

	if ( ( defined( 'DISABLE_NGFB_OPEN_GRAPH' ) && DISABLE_NGFB_OPEN_GRAPH ) || 
		( defined( 'NGFB_OPEN_GRAPH_DISABLE' ) && NGFB_OPEN_GRAPH_DISABLE ) ) {
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
			array_push( $debug, $debug_pre . 'ngfb_get_ngg_image_url' . $debug_post );
			$og['og:image'] = ngfb_get_ngg_image_url( $thumb_id, $options['og_img_size'] );
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
				$og['og:image'] = ngfb_get_ngg_image_url( 'ngg-'.$id, $options['og_img_size'] );
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

					$width = is_numeric( $width ) ? $width : 0;
					$height = is_numeric( $height ) ? $height : 0;

					array_push( $debug , $debug_pre."img $src / ".$og['og:image']." / src width=$width x height=$height" );

					$size = ngfb_get_size_values( $options['og_img_size'] );

					// if we're picking up an img from src, make sure it's width and height is large enough
					if ( $src == 'share-'.$options['og_img_size'] || $src == 'share' || 
						( $src == 'src' && defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) ||
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
					array_push( $debug, $debug_pre."ngfb_get_ngg_image_url(".$img_id.','.$options['og_img_size'].')' );
					$og['og:image'] = ngfb_get_ngg_image_url( $img_id, $options['og_img_size'] );
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

/* Function called from ngfb_add_meta_tags() to return an Open Graph tag based
 * on the property name and it's value.
 */
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

/* The content can only be filtered when this function is called from
 * wp_head(), so make the $filter_content false by default.
 */
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

/* The content can only be filtered when this function is called from
 * wp_head(), so make the $filter_content false by default.
 */
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
			$size_name, ' (', $size['width'], 'x', $size['height'],
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

/* If it's available, use CDN Linker to re-write a URL before it gets
 * urlencoded.
 */
function ngfb_cdn_linker( $url = '' ) {
	if ( class_exists( CDNLinksRewriterWordpress ) ) {
		$rewriter = new CDNLinksRewriterWordpress();
		$url = '"'.$url.'"';
		$url = trim( $rewriter->rewrite( $url ), "\"" );
	}
	return $url;
}

?>
