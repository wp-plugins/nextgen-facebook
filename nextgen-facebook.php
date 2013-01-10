<?php
/*
Plugin Name: NextGEN Facebook OG
Plugin URI: http://wordpress.org/extend/plugins/nextgen-facebook/
Description: Adds Open Graph meta tags for Facebook, G+, LinkedIn, etc., plus sharing buttons for FB, G+, Twitter, LinkedIn, Pinterest, tumblr.
Version: 2.4
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

if ( ! class_exists( 'NGFB' ) ) {

	class NGFB {

		var $minimum_wp_version = '3.0';
		var $social_options_prefix = array(
			'facebook' => 'fb', 
			'gplus' => 'gp',
			'twitter' => 'twitter',
			'linkedin' => 'linkedin',
			'pinterest' => 'pin',
			'stumbleupon' => 'stumble',
			'tumblr' => 'tumblr' );
		var $options = '';
		var $default_options = array(
			'og_art_section' => '',
			'og_img_size' => 'thumbnail',
			'og_def_img_id_pre' => '',
			'og_def_img_id' => '',
			'og_def_img_url' => '',
			'og_def_img_on_index' => '1',
			'og_def_img_on_search' => '1',
			'og_ngg_tags' => '',
			'og_author_url' => 'index',
			'og_def_author_id' => '',
			'og_title_len' => '100',
			'og_desc_len' => '300',
			'og_desc_strip' => '',
			'og_desc_wiki' => '',
			'og_wiki_tag' => 'Wiki-',
			'og_admins' => '',
			'og_app_id' => '',
			'buttons_on_index' => '',
			'buttons_on_ex_pages' => '',
			'buttons_location' => 'bottom',
			'fb_enable' => '',
			'fb_order' => '1',
			'fb_send' => '1',
			'fb_layout' => 'button_count',
			'fb_colorscheme' => 'light',
			'fb_font' => 'arial',
			'fb_show_faces' => '',
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
			'twitter_dnt' => '1',
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
			'tumblr_photo' => '1',
			'tumblr_img_size' => 'large',
			'tumblr_caption' => 'both',
			'tumblr_cap_len' => '500',
			'stumble_enable' => '',
			'stumble_order' => '6',
			'stumble_badge' => '1',
			'inc_fb:admins' => '1',
			'inc_fb:app_id' => '1',
			'inc_og:site_name' => '1',
			'inc_og:title' => '1',
			'inc_og:type' => '1',
			'inc_og:url' => '1',
			'inc_og:description' => '1',
			'inc_og:image' => '1',
			'inc_og:video' => '1',
			'inc_og:video:width' => '1',
			'inc_og:video:height' => '1',
			'inc_og:video:type' => '1',
			'inc_article:author' => '1',
			'inc_article:published_time' => '1',
			'inc_article:modified_time' => '1',
			'inc_article:section' => '1',
			'inc_article:tag' => '1',
			'ngfb_reset' => '',
			'ngfb_debug' => '',
			'ngfb_filter_content' => '1',
			'ngfb_skip_small_img' => '1' );

		function NGFB() {

			$this->define_constants();	// define constants first for option defaults
			$this->load_dependencies();
			$this->load_options();

			$this->plugin_name = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );

			register_activation_hook( $this->plugin_name, 'activate' );
			register_uninstall_hook( $this->plugin_name, array( 'NGFB', 'uninstall' ) );

			add_action( 'admin_init', array( &$this, 'require_wordpress_version' ) );
			add_filter( 'language_attributes', array( &$this, 'add_og_doctype' ) );
			add_filter( 'wp_head', 'ngfb_add_meta_tags', NGFB_HEAD_PRIORITY );
			add_filter( 'the_content', array( &$this, 'add_content_buttons' ), NGFB_CONTENT_PRIORITY );
			add_filter( 'wp_footer', array( &$this, 'add_content_footer' ), NGFB_FOOTER_PRIORITY );
			add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
		}
		
		function define_constants() { 
			global $wp_version;

			// NGFB_OPEN_GRAPH_DISABLE
			// NGFB_MIN_IMG_SIZE_DISABLE

			define( 'NGFB_FOLDER', basename( dirname( __FILE__ ) ) );
			define( 'NGFB_URLPATH', trailingslashit( plugins_url( NGFB_FOLDER ) ) );

			// allow constants to be pre-defined in wp-config.php
			if ( ! defined( 'NGFB_HEAD_PRIORITY' ) )
				define( 'NGFB_HEAD_PRIORITY', 20 );

			if ( ! defined( 'NGFB_CONTENT_PRIORITY' ) )
				define( 'NGFB_CONTENT_PRIORITY', 20 );
			
			if ( ! defined( 'NGFB_FOOTER_PRIORITY' ) )
				define( 'NGFB_FOOTER_PRIORITY', 10 );

			if ( ! defined( 'NGFB_MIN_DESC_LEN' ) )
				define( 'NGFB_MIN_DESC_LEN', 160 );

			if ( ! defined( 'NGFB_MIN_IMG_WIDTH' ) )
				define( 'NGFB_MIN_IMG_WIDTH', 200 );

			if ( ! defined( 'NGFB_MIN_IMG_HEIGHT' ) )
				define( 'NGFB_MIN_IMG_HEIGHT', 200 );
		}

		function load_dependencies() {
			require_once ( dirname ( __FILE__ ) . '/lib/widgets.php' );

			if ( is_admin() ) {
				require_once ( dirname ( __FILE__ ) . '/lib/admin.php' );
				$this->ngfbAdmin = new ngfbAdmin();
			}
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
			if ( ( $this->options['ngfb_reset'] == 1 ) || ( ! is_array( $this->options ) ) ) {
				delete_option( 'ngfb_options' );	// remove old options, if any
				update_option( 'ngfb_options', $this->default_options );
			}
		}

		// delete options table entries only when plugin deactivated and deleted
		function uninstall() {
			delete_option( 'ngfb_options' );
		}

		// display a settings link on the main plugins page
		function plugin_action_links( $links, $file ) {
			if ( $file == plugin_basename( __FILE__ ) ) {
				$links[] = '<a href="'.get_admin_url().'options-general.php?page=ngfb">'.__('Settings').'</a>';
			}
			return $links;
		}

		// get the options, upgrade the option names (if necessary), and validate their values
		function load_options() {

			$opts = get_option( 'ngfb_options' );

			if ( ! $opts['og_def_img_url'] && $opts['og_def_img'] ) {
				$opts['og_def_img_url'] = $opts['og_def_img'];
				delete_option( $opts['og_def_img'] );
			}
			if ( ! $opts['og_def_img_on_index'] && $opts['og_def_home']) {
				$opts['og_def_img_on_index'] = $opts['og_def_home'];
				delete_option( $opts['og_def_home'] );
			}
			if ( ! $opts['og_def_img_on_index'] && $opts['og_def_on_home']) {
				$opts['og_def_img_on_index'] = $opts['og_def_on_home'];
				delete_option( $opts['og_def_on_home'] );
			}
			if ( ! $opts['og_def_img_on_search'] && $opts['og_def_on_search']) {
				$opts['og_def_img_on_search'] = $opts['og_def_on_search'];
				delete_option( $opts['og_def_on_home'] );
			}
			if ( ! $opts['buttons_on_index'] && $opts['buttons_on_home']) {
				$opts['buttons_on_index'] = $opts['buttons_on_home'];
				delete_option( $opts['buttons_on_home'] );
			}
			// default values for new options
			foreach ( $this->default_options as $opt => $def )
				if ( $opt && ! array_key_exists( $opt, $opts ) ) 
					$opts[$opt] = $def;
			unset( $opt, $def );
			ksort( $opts );
			$this->options = $this->validate_options( $opts );
		}

		// sanitize and validate input
		function validate_options( $opts ) {

			$opts['og_def_img_url'] = wp_filter_nohtml_kses( $opts['og_def_img_url'] );
			$opts['og_app_id'] = wp_filter_nohtml_kses( $opts['og_app_id'] );

			// sanitize the option by stipping off any leading URLs (leaving just the account names)
			foreach ( array( 'og_admins' ) as $opt ) {
				$opts[$opt] = wp_filter_nohtml_kses( preg_replace( '/(http|https):\/\/[^\/]*?\//', '', $opts[$opt] ) );
			}
			unset( $opt );

			// options that must be numeric (blank or zero is ok)
			foreach ( array( 
				'og_def_img_id',
				'og_def_author_id' ) as $opt )
				if ( ! is_numeric( $opts[$opt] ) ) 
					$opts[$opt] = $this->default_options[$opt];

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
				'stumble_badge' ) as $opt )
				if ( ! $opts[$opt] || ! is_numeric( $opts[$opt] ) )
					$opts[$opt] = $this->default_options[$opt];
			unset( $opt );
			if ( $opts['og_desc_len'] < NGFB_MIN_DESC_LEN ) 
				$opts['og_desc_len'] = NGFB_MIN_DESC_LEN;

			// options that cannot be blank
			foreach ( array( 
				'og_img_size', 
				'og_author_url',
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
				'tumblr_caption' ) as $opt ) {
				$opts[$opt] = wp_filter_nohtml_kses( $opts[$opt] );
				if ( ! $opts[$opt] ) $opts[$opt] = $this->default_options[$opt];
			}
			unset( $opt );
		
			// true/false options
			foreach ( array( 
				'og_def_img_on_index',
				'og_def_img_on_search',
				'og_ngg_tags',
				'og_desc_strip',
				'og_desc_wiki',
				'buttons_on_index',
				'buttons_on_ex_pages',
				'fb_enable',
				'fb_send',
				'fb_show_faces',
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
				'ngfb_skip_small_img' ) as $opt )
				$opts[$opt] = ( $opts[$opt] ? 1 : 0 );
			unset( $opt );

			return $opts;
		}

		function add_content_buttons( $content ) {

			$buttons = '';

			// if using the Exclude Pages plugin, skip social buttons on those pages
			if ( is_page() && ngfb_is_excluded() ) return $content;

			if ( is_singular() || $this->options['buttons_on_index'] ) {
				$sorted_ids = array();
				foreach ( $this->social_options_prefix as $id => $prefix )
					if ( $this->options[$prefix.'_enable'] )
						// sort by number, then by name
						$sorted_ids[$this->options[$prefix.'_order'] . '-' . $id] = $id;
				ksort( $sorted_ids );
				foreach ( $sorted_ids as $id ) {
					$buttons .= eval ( "if ( function_exists( 'ngfb_${id}_button' ) ) 
						return ngfb_${id}_button();" );
				}
				if ( $buttons ) {
					$buttons = "
<!-- NextGEN Facebook OG Social Buttons BEGIN -->
<div class=\"ngfb-content-buttons ngfb-buttons\">\n$buttons\n</div>
<!-- NextGEN Facebook OG Social Buttons END -->\n\n";

					if ( $this->options['buttons_location'] == "top" ) $content = $buttons.$content;
					else $content = $content.$buttons;
				}
			}
			return $content;
		}

		function add_content_footer() {

			// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
			if ( is_page() && ngfb_is_excluded() ) return $content;

			if ( is_singular() || $this->options['buttons_on_index'] ) {
				echo "\n", '<!-- NextGEN Facebook OG Content Footer BEGIN -->', "\n";
				foreach ( $this->social_options_prefix as $id => $prefix )
					if ( $this->options[$prefix.'_enable'] ) 
						echo eval ( "if ( function_exists( 'ngfb_${id}_footer' ) ) 
							return ngfb_${id}_footer();" );
				unset ( $id, $prefix );
				echo "\n", '<!-- NextGEN Facebook OG Content Footer END -->', "\n\n";
			}
		}

		function debug_msg( $pos, $msg = '' ) {
			echo "\n", '<!-- NGFB Debug (', $pos, ')';
			if ( is_array( $msg ) ) {
				echo "\n";
				$is_assoc = is_numeric( implode(  array_keys( $msg ) ) ) ? 0 : 1;
				if ( $is_assoc ) ksort( $msg );	// associative array - sort by key
				foreach ( (array) $msg as $key => $val ) 
					echo $is_assoc ? "\t$key = $val\n" : "\t$val\n";
				unset ( $key, $val );
			} else echo $msg;
			echo ' -->', "\n";
		}

		function get_author_url( $author_id ) {
			switch ( $this->options['og_author_url'] ) {
				case 'website':
					$url = get_the_author_meta( 'url', $author_id );
					break;
				case 'gplus_link':
					$url = get_the_author_meta( 'gplus_link', $author_id ) . "?rel=author";
					break;
			}
			if ( ! $url ) $url = trailingslashit( site_url() ) . 'author/' . get_the_author_meta( 'user_login', $author_id ) . '/';
			return $url;
		}
	}
        global $ngfb;
	$ngfb = new NGFB();
}


/* You can enable social buttons in the content, use the social buttons widget,
 * and call the ngfb_get_social_buttons() function from your template(s) -- all
 * at the same time -- but all social buttons share the same settings from the
 * admin options page (the layout of each can differ by using the available CSS
 * class names - see the Other Notes tab at
 * http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/ for
 * additional information).
 */
if ( ! function_exists(  'ngfb_get_social_buttons' ) ) {
	function ngfb_get_social_buttons( $ids = array(), $attr = array() ) {
		global $post;
		$buttons = '';
		// make sure we have at least $post->ID or $attr['url'] defined
		if ( ! isset( $post->ID ) && empty( $attr['url' ] ) ) {
			$attr['url'] = $_SERVER['HTTPS'] ? 'https://' : 'http://';
			$attr['url'] .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		foreach ( $ids as $id ) {
			$id = preg_replace( '/[^a-z]/', '', $id );	// sanitize input before eval
			$buttons .= eval ( "if ( function_exists( 'ngfb_${id}_button' ) ) 
				return ngfb_${id}_button( \$attr );" );
			$buttons .= eval ( "if ( function_exists( 'ngfb_${id}_footer' ) ) 
				return ngfb_${id}_footer();" );
		}
		if ( $buttons ) $buttons = '
<!-- NextGEN Facebook OG Social Buttons BEGIN -->
<div class="ngfb-buttons">
' . $buttons . '
</div>
<!-- NextGEN Facebook OG Social Buttons END -->'."\n\n";
		return $buttons;
	}
}

function ngfb_stumbleupon_button( &$attr = array() ) {
	global $ngfb; global $post; $button = '';
	if ( ! $attr['stumble_badge'] ) $attr['stumble_badge'] = $ngfb->options['stumble_badge'];
	if ( ! $attr['url'] ) $attr['url'] = get_permalink( $post->ID );
	$button = '
		<!-- StumbleUpon Button -->
		<div class="stumble-button stumbleupon-button"><su:badge layout="' . $attr['stumble_badge'] . '" 
			location="' . $attr['url'] . '"></su:badge></div>
	';
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

function ngfb_pinterest_button( &$attr = array() ) {
	global $ngfb; global $post; $button = '';
	if ( ! $attr['pin_count_layout'] ) $attr['pin_count_layout'] = $ngfb->options['pin_count_layout'];
	if ( ! $attr['url'] ) $attr['url'] = get_permalink( $post->ID );
	if ( ! $attr['size'] ) $attr['size'] = $ngfb->options['pin_img_size'];
	if ( ! $attr['caption'] ) $attr['caption'] = ngfb_get_caption( $ngfb->options['pin_caption'], $ngfb->options['pin_cap_len'] );
	if ( ! $attr['photo'] ) {
		if ( ! $attr['pid'] && function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
			$attr['pid'] = get_post_thumbnail_id( $post->ID );
		}
		if ( $attr['pid'] ) {
			// if the post thumbnail id has the form ngg- then it's a NextGEN image
			if ( is_string( $attr['pid'] ) && substr( $attr['pid'], 0, 4 ) == 'ngg-' ) {
				$attr['photo'] = ngfb_get_ngg_image_url( $attr['pid'], $attr['size'] );
			} else {
				$out = wp_get_attachment_image_src( $attr['pid'], $attr['size'] );
				$attr['photo'] = $out[0];
			}
		}
	}
	// define the button, based on what we have
	if ( $attr['photo'] ) {
		$button .= '?url=' . urlencode( $attr['url'] );
		$button .= '&amp;media='. urlencode( ngfb_cdn_linker_rewrite( $attr['photo'] ) );
		$button .= '&amp;description=' . urlencode( ngfb_str_decode( $attr['caption'] ) );
	}
	// if we have something, then complete the button code
	if ( $button ) {
		$button = '
			<!-- Pinterest Button -->
			<div class="pinterest-button"><a href="http://pinterest.com/pin/create/button/' . $button . '" 
				class="pin-it-button" count-layout="' . $attr['pin_count_layout'] . '" 
				title="Share on Pinterest"><img border="0" alt="Pin It"
				src="http://assets.pinterest.com/images/PinExt.png" /></a></div>
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

function ngfb_tumblr_button( &$attr = array() ) {
	global $ngfb; global $post; $button = '';
	if ( ! $attr['tumblr_button_style'] ) $attr['tumblr_button_style'] = $ngfb->options['tumblr_button_style'];
	if ( ! $attr['url'] ) $attr['url'] = get_permalink( $post->ID );
	if ( ! $attr['size'] ) $attr['size'] = $ngfb->options['tumblr_img_size'];
	if ( ! $attr['embed'] ) $attr['embed'] = ngfb_get_video_embed( );
	if ( ! $attr['title'] ) $attr['title'] = ngfb_get_title( );
	if ( ! $attr['caption'] ) $attr['caption'] = ngfb_get_caption( $ngfb->options['tumblr_caption'], $ngfb->options['tumblr_cap_len'] );
	if ( ! $attr['description'] ) $attr['description'] = ngfb_get_description( $ngfb->options['tumblr_desc_len'], '...' );

	// only use an image if $ngfb->options['tumblr_photo'] allows it
	if ( ! $attr['photo'] && $ngfb->options['tumblr_photo'] ) {
		if ( ! $attr['pid'] && function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
			$attr['pid'] = get_post_thumbnail_id( $post->ID );
		}
		if ( $attr['pid'] ) {
			// if the post thumbnail id has the form ngg- then it's a NextGEN image
			if ( is_string( $attr['pid'] ) && substr( $attr['pid'], 0, 4 ) == 'ngg-' ) {
				$attr['photo'] = ngfb_get_ngg_image_url( $attr['pid'], $attr['size'] );
			} else {
				$out = wp_get_attachment_image_src( $attr['pid'], $attr['size'] );
				$attr['photo'] = $out[0];
			}
		}
	}
	if ( ! $attr['quote'] && get_post_format( $post->ID ) == 'quote' ) {
		$attr['quote'] = ngfb_get_quote();
	}
	// define the button, based on what we have
	if ( $attr['photo'] && $ngfb->options['tumblr_photo'] ) {
		$button .= 'photo?source='. urlencode( ngfb_cdn_linker_rewrite( $attr['photo'] ) );
		$button .= '&amp;caption=' . urlencode( ngfb_str_decode( $attr['caption'] ) );
		$button .= '&amp;clickthru=' . urlencode( $attr['url'] );
	} elseif ( $attr['embed'] ) {
		$button .= 'video?embed=' . urlencode( $attr['embed'] );
		$button .= '&amp;caption=' . urlencode( ngfb_str_decode( $attr['caption'] ) );
	} elseif ( $attr['quote'] ) {
		$button .= 'quote?quote=' . urlencode( $attr['quote'] );
		$button .= '&amp;source=' . urlencode( ngfb_str_decode( $attr['title'] ) );
	} elseif ( $attr['url'] ) {
		$button .= 'link?url=' . urlencode( $attr['url'] );
		$button .= '&amp;name=' . urlencode( ngfb_str_decode( $attr['title'] ) );
		$button .= '&amp;description=' . urlencode( ngfb_str_decode( $attr['description'] ) );
	}
	// if we have something, then complete the button code
	if ( $button ) {
		$button = '
			<!-- Tumblr Button -->
			<div class="tumblr-button"><a href="http://www.tumblr.com/share/'. $button . '" 
				title="Share on tumblr"><img border="0" alt="tumblr"
				src="http://platform.tumblr.com/v1/' . $attr['tumblr_button_style'] . '.png" /></a></div>
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

function ngfb_facebook_button( &$attr = array() ) {
	global $ngfb; global $post; $button = '';
	if ( ! $attr['url'] ) $attr['url'] = get_permalink( $post->ID );

	$fb_send = $ngfb->options['fb_send'] ? 'true' : 'false';
	$fb_layout = $ngfb->options['fb_layout'];
	$fb_show_faces = $ngfb->options['fb_show_faces'] ? 'true' : 'false';
	$fb_colorscheme = $ngfb->options['fb_colorscheme'];
	$fb_action = $ngfb->options['fb_action'];
	$fb_font = $ngfb->options['fb_font'];
	$button = '
		<!-- Facebook Button -->
		<div class="facebook-button"><span class="fb-root"><fb:like 
		href="' . $attr['url'] . '"
		send="' . $fb_send . '" layout="' . $fb_layout . '" width="400"
		show_faces="' . $fb_show_faces . '" font="' . $fb_font . '" action="' . $fb_action . '"
		colorscheme="' . $fb_colorscheme . '"></fb:like></span></div>
	';
	return $button;
}

function ngfb_facebook_footer() {
	return '
		<!-- Facebook Javascript -->
		<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
	';
}

function ngfb_gplus_button( &$attr = array() ) {
	global $ngfb; global $post; $button;
	if ( ! $attr['url'] ) $attr['url'] = get_permalink( $post->ID );

	$gp_action = $ngfb->options['gp_action'];
	$gp_size = $ngfb->options['gp_size'];
	$gp_annotation = $ngfb->options['gp_annotation'];
	$gp_class = $gp_action == 'share' ? 'class="g-plus" data-action="share"' : 'class="g-plusone"';

	// html-5 syntax
	$button = '
		<!-- Google+ Button -->
		<div class="gplus-button g-plusone-button"><span '. $gp_class . ' 
		data-size="'.$gp_size.'" 
		data-annotation="'.$gp_annotation.'" 
		data-href="' . $attr['url'] . '"></span></div>
	';
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

function ngfb_twitter_button( &$attr = array() ) {
	global $ngfb; global $post; $button;
	if ( ! $attr['url'] ) $attr['url'] = get_permalink( $post->ID );

	$twitter_count = $ngfb->options['twitter_count'];
	$twitter_size = $ngfb->options['twitter_size'];
	$twitter_dnt = $ngfb->options['twitter_dnt'] ? 'true' : 'false';
	$button = '
		<!-- Twitter Button -->
		<div class="twitter-button">
		<a href="https://twitter.com/share" 
			class="twitter-share-button"
			data-url="' . $attr['url'] . '" 
			data-count="'.$twitter_count.'" 
			data-size="'.$twitter_size.'" 
			data-dnt="'.$twitter_dnt.'">Tweet</a></div>
	';
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

function ngfb_linkedin_button( &$attr = array() ) {
	global $ngfb; global $post; $button;
	if ( ! $attr['url'] ) $attr['url'] = get_permalink( $post->ID );

	$linkedin_counter = $ngfb->options['linkedin_counter'];

	$button = "\n".'<div class="linkedin-button">
		<script type="IN/Share" 
			data-url="' . $attr['url'] . '"';
	if ( $ngfb->options['linkedin_counter'] ) 
		$button .= ' data-counter="' . $ngfb->options['linkedin_counter'] . '"';
	$button .= '></script></div>'."\n";
	return $button;
}

function ngfb_linkedin_footer() {
	return '
		<!-- LinkedIn Javascript -->
		<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script>
	';
}

// called from ngfb_add_meta_tags() to add NGG image tags to the $og['article:tag'] variable
function ngfb_get_ngg_thumb_tags( $thumb_id ) {

	if ( ! method_exists( 'nggdb', 'find_image' ) ) return;
	if ( is_string($thumb_id) && substr($thumb_id, 0, 4) == 'ngg-') {
		$thumb_id = substr($thumb_id, 4);
		$img_tags = wp_get_object_terms($thumb_id, 'ngg_tag', 'fields=names');
	}
	return $img_tags;
}

// called to get an image URL from an NGG picture ID and a media size name
// the thumb_id must be formatted as 'ngg-#'
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
					'&amp;mode=' . $crop;
		}
    }
    return $image_url;
}

/* filter for wp_head() */
function ngfb_add_meta_tags() {

	if ( ( defined( 'DISABLE_NGFB_OPEN_GRAPH' ) && DISABLE_NGFB_OPEN_GRAPH ) || 
		( defined( 'NGFB_OPEN_GRAPH_DISABLE' ) && NGFB_OPEN_GRAPH_DISABLE ) ) {
		echo "\n<!-- NextGEN Facebook OG Meta Tags DISABLED -->\n\n";
		return;
	}

	global $ngfb;
	global $post;
	$debug = array();

	if ( $ngfb->options['ngfb_debug'] ) $ngfb->debug_msg( __FUNCTION__ , $ngfb->options );

	$og['fb:admins'] = $ngfb->options['og_admins'];
	$og['fb:app_id'] = $ngfb->options['og_app_id'];
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
		$debug_post = '('.$thumb_id.','.$ngfb->options['og_img_size'].')';

		// if the post thumbnail id has the form ngg- then it's a NextGEN image
		if ( is_string( $thumb_id ) && substr( $thumb_id, 0, 4 ) == 'ngg-' ) {
			array_push( $debug, $debug_pre . 'ngfb_get_ngg_image_url' . $debug_post );
			$og['og:image'] = ngfb_get_ngg_image_url( $thumb_id, $ngfb->options['og_img_size'] );
		} else {
			array_push( $debug, $debug_pre.'wp_get_attachment_image_src'.$debug_post );
			$out = wp_get_attachment_image_src( $thumb_id, $ngfb->options['og_img_size'] );
			$og['og:image'] = $out[0];
		}
	}

	// if there's no featured image, search post for images and display first one
	if ( ! $og['og:image'] ) {
		if ( is_singular() ||
			( is_search() && ! $ngfb->options['og_def_img_on_search'] ) ||
			( ! is_singular() && ! is_search() && ! $ngfb->options['og_def_img_on_index'] ) ) {

			$debug_pre = "image_source = preg_match / ";
			$content = $post->post_content;

			// check for singlepic before applying filter to content
			if ( preg_match( '/\[(singlepic)[^\]]+id=([0-9]+)/i', $content, $match ) ) {
				$src = $match[1];
				$id = $match[2];
				array_push( $debug, $debug_pre.$src." / ".$id );
				$og['og:image'] = ngfb_get_ngg_image_url( 'ngg-'.$id, $ngfb->options['og_img_size'] );
			} else {
				$content = ngfb_apply_content_filter( $content, $ngfb->options['ngfb_filter_content'] );

				// img attributes in order of preference
				if ( preg_match( '/<img[^>]*? (share-'.$ngfb->options['og_img_size'].'|share|src)=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $match ) ) {
					$img = $match[0];
					$src = $match[1];
					$og['og:image'] = $match[2];

					if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $img, $match) ) $width = $match[1];
					if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $img, $match) ) $height = $match[1];

					$width = is_numeric( $width ) ? $width : 0;
					$height = is_numeric( $height ) ? $height : 0;

					array_push( $debug , $debug_pre."img $src / ".$og['og:image']." / src width=$width x height=$height" );

					$size = ngfb_get_size_values( $ngfb->options['og_img_size'] );

					// if we're picking up an img from src, make sure it's width and height is large enough
					if ( $src == 'share-'.$ngfb->options['og_img_size'] || $src == 'share' || 
						( $src == 'src' && defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) ||
						( $src == 'src' && $ngfb->options['ngfb_skip_small_img'] && $width >= $size['width'] && $height >= $size['height'] ) ) {

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
			( is_search() && $ngfb->options['og_def_img_on_search'] ) ||
			( ! is_singular() && ! is_search() && $ngfb->options['og_def_img_on_index'] ) ) {

			if ( $ngfb->options['og_def_img_id'] != '' ) {
				$debug_pre = "image_source = default / ";
				if ($ngfb->options['og_def_img_id_pre'] == 'ngg') {
					$img_id = $ngfb->options['og_def_img_id_pre'].'-'.$ngfb->options['og_def_img_id'];
					array_push( $debug, $debug_pre."ngfb_get_ngg_image_url(".$img_id.','.$ngfb->options['og_img_size'].')' );
					$og['og:image'] = ngfb_get_ngg_image_url( $img_id, $ngfb->options['og_img_size'] );
				} else {
					array_push( $debug, $debug_pre."wp_get_attachment_image_src(".$ngfb->options['og_def_img_id'].",".$ngfb->options['og_img_size'].")" );
					$out = wp_get_attachment_image_src( $ngfb->options['og_def_img_id'], $ngfb->options['og_img_size'] );
					$og['og:image'] = $out[0];
				}
			}
			// if still empty, use the default url (if one is defined, empty string otherwise)
			if ( ! $og['og:image'] ) 
				$og['og:image'] = $ngfb->options['og_def_img_url'];
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

	$og['og:title'] = ngfb_get_title( $ngfb->options['og_title_len'], '...' );

	// ==============
	// og:description
	// ==============

	$og['og:description'] = ngfb_get_description( $ngfb->options['og_desc_len'], '...' );

	// =====================
	// og:type and article:*
	// =====================

	if ( $post->post_author || $ngfb->options['og_def_author_id'] ) {
		$og['og:type'] = "article";
		$og['article:section'] = $ngfb->options['og_art_section'];
		$og['article:modified_time'] = get_the_modified_date('c');
		$og['article:published_time'] = get_the_date('c');
		if ( $post->post_author )
			$og['article:author'] = $ngfb->get_author_url( $post->post_author );
		elseif ( $ngfb->options['og_def_author_id'] )
			$og['article:author'] = $ngfb->get_author_url( $ngfb->options['og_def_author_id'] );
	} else $og['og:type'] = "website";

	if ( is_singular() ) {
		$og['article:tag'] = array();
		$page_tags = wp_get_post_tags( $post->ID );
		$tag_prefix = isset( $ngfb->options['og_wiki_tag'] ) ? $ngfb->options['og_wiki_tag'] : '';
		foreach ( $page_tags as $tag ) {
			$tag_name = $tag->name;
			if ( $tag_prefix ) $tag_name = preg_replace( "/^$tag_prefix/", "", $tag_name );
			array_push( $og['article:tag'], $tag_name );
		}
		unset ( $tag );
		if ( $ngfb->options['og_ngg_tags'] ) {
			if ( function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
				$thumb_id = get_post_thumbnail_id( $post->ID );
				if ( is_string( $thumb_id ) && substr( $thumb_id, 0, 4 ) == 'ngg-' )
					$image_tags = ngfb_get_ngg_thumb_tags( $thumb_id );
			} elseif ( $ngfb->options['og_def_img_id'] != '' && $ngfb->options['og_def_img_id_pre'] == 'ngg')
				$image_tags = ngfb_get_ngg_thumb_tags( $ngfb->options['og_def_img_id_pre'].'-'.$ngfb->options['og_def_img_id'] );
			if ( is_array( $image_tags ) ) 
				$og['article:tag'] = array_merge( $og['article:tag'], $image_tags );
		}
	}

	// output whatever debug info we have before printing the open graph meta tags
	if ( $ngfb->options['ngfb_debug'] ) $ngfb->debug_msg( __FUNCTION__ , $debug );

	// add the Open Graph meta tags
	ngfb_og_meta_tags( $og );
}

function ngfb_og_meta_tags( &$og, $all = false ) {
	global $ngfb;
	echo "\n<!-- NextGEN Facebook OG Meta Tags BEGIN -->\n";
	if ( ! empty( $og ) ) ksort( $og );
	foreach ( $og as $name => $val ) {
		if ( ( $ngfb->options['inc_'.$name] || $all ) && $val ) {
			if ( is_array ( $og[$name] ) ) {
				foreach ( $og[$name] as $el ) 
					echo ngfb_get_meta_tag( $name, $el );
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

	$title = apply_filters( 'the_title', $title );

	return ngfb_limit_text_length( $title, $textlen, $trailing ) . $page_num;
}

function ngfb_get_description( $textlen = 300, $trailing = '' ) {

	global $ngfb;
	global $post;
	$desc = '';

	if ( is_singular() ) {

		// use the excerpt, if we have one
		if ( has_excerpt( $post->ID ) ) {

			$desc = $post->post_excerpt;
			$desc = apply_filters( 'the_excerpt', $desc );

		// if there's no excerpt, then use WP-WikiBox for page content (if wikibox_summary() is available and og_desc_wiki option is true)
		} elseif ( is_page() && $ngfb->options['og_desc_wiki'] && function_exists( 'wikibox_summary' ) ) {

			$tags = wp_get_post_tags( $post->ID );

			if ( $tags ) {
				$tag_prefix = $ngfb->options['og_wiki_tag'];
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
		$desc = ngfb_apply_content_filter( $desc, $ngfb->options['ngfb_filter_content'] );

		// ignore everything until the first paragraph tag if $ngfb->options['og_desc_strip'] is true
		if ( $ngfb->options['og_desc_strip'] ) $desc = preg_replace( '/^.*?<p>/', '', $desc );	// question mark makes regex un-greedy

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

function ngfb_apply_content_filter( $content, $filter_content = true ) {

	// the_content filter breaks the ngg album shortcode, so skip it if that shortcode if found
	if ( ! preg_match( '/\[ *album[ =]/', $content ) && $filter_content ) {
		global $ngfb;
		// temporarily remove add_content_buttons() to prevent recursion
		$filter_removed = remove_filter( 'the_content', 
			array( $ngfb, 'add_content_buttons' ), NGFB_CONTENT_PRIORITY );

		$content = apply_filters( 'the_content', $content );

		if ( $filter_removed ) add_filter( 'the_content', 
			array( $ngfb, 'add_content_buttons' ), NGFB_CONTENT_PRIORITY );
	}

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

function ngfb_get_size_values( $size_name = 'thumbnail' ) {

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

	global $ngfb;
	global $post;
	if ( is_page() && $post->ID && function_exists( 'ep_get_excluded_ids' ) && ! $ngfb->options['buttons_on_ex_pages'] ) {
		$excluded_ids = ep_get_excluded_ids();
		$delete_ids = array_unique( $excluded_ids );
		if ( in_array( $post->ID, $delete_ids ) ) {
			return true;
		}
	}
	return false;
}

// if it's available, use CDN Linker to re-write a URL before it gets urlencoded
function ngfb_cdn_linker_rewrite( $url = '' ) {

	if ( class_exists( CDNLinksRewriterWordpress ) ) {
		$rewriter = new CDNLinksRewriterWordpress();
		$url = '"'.$url.'"';
		$url = trim( $rewriter->rewrite( $url ), "\"" );
	}
	return $url;
}

?>
