<?php
/*
Plugin Name: NextGEN Facebook OG
Plugin URI: http://wordpress.org/extend/plugins/nextgen-facebook/
Description: Adds Open Graph meta tags for Facebook, Google+, LinkedIn, etc., plus social sharing buttons for Facebook, Google+, and many more.
Version: 3.0
Author: Jean-Sebastien Morisset
Author URI: http://surniaulula.com/

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

		var $debug = array();
		var $version = '3.0';
		var $minimum_wp_version = '3.0';
		var $social_nice_names = array(
			'facebook' => 'Facebook', 
			'gplus' => 'Google+',
			'twitter' => 'Twitter',
			'linkedin' => 'Linkedin',
			'pinterest' => 'Pinterest',
			'stumbleupon' => 'StumbleUpon',
			'tumblr' => 'Tumblr',
		);
		var $social_options_prefix = array(
			'facebook' => 'fb', 
			'gplus' => 'gp',
			'twitter' => 'twitter',
			'linkedin' => 'linkedin',
			'pinterest' => 'pin',
			'stumbleupon' => 'stumble',
			'tumblr' => 'tumblr',
		);
		var $options = array();
		var $default_options = array(
			'link_author' => '',
			'link_pub_url' => '',
			'og_art_section' => '',
			'og_img_size' => 'thumbnail',
			'og_img_max' => '1',
			'og_vid_max' => '1',
			'og_def_img_id_pre' => '',
			'og_def_img_id' => '',
			'og_def_img_url' => '',
			'og_def_img_on_index' => '1',
			'og_def_img_on_search' => '1',
			'og_ngg_tags' => '1',
			'og_page_ances_tags' => '',
			'og_page_title_tag' => '',
			'og_author_field' => 'url',
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
			'inc_og:image:width' => '1',
			'inc_og:image:height' => '1',
			'inc_og:video' => '1',
			'inc_og:video:width' => '1',
			'inc_og:video:height' => '1',
			'inc_og:video:type' => '1',
			'inc_article:author' => '1',
			'inc_article:published_time' => '1',
			'inc_article:modified_time' => '1',
			'inc_article:section' => '1',
			'inc_article:tag' => '1',
			'ngfb_version' => '',
			'ngfb_reset' => '',
			'ngfb_debug' => '',
			'ngfb_filter_content' => '1',
			'ngfb_skip_small_img' => '1',
		);

		function NGFB() {

			$this->define_constants();	// define constants first for option defaults
			$this->load_dependencies();
			$this->load_options();

			$this->plugin_name = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );

			register_activation_hook( $this->plugin_name, 'activate' );
			register_uninstall_hook( $this->plugin_name, array( 'NGFB', 'uninstall') );

			add_action( 'admin_init', array( &$this, 'require_wordpress_version' ) );
			add_filter( 'language_attributes', array( &$this, 'add_og_doctype' ) );
			add_filter( 'wp_head', array( &$this, 'add_meta_tags' ), NGFB_HEAD_PRIORITY );
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

			if ( ! defined( 'NGFB_MAX_IMG_OG' ) )
				define( 'NGFB_MAX_IMG_OG', 20 );

			if ( ! defined( 'NGFB_MAX_VID_OG' ) )
				define( 'NGFB_MAX_VID_OG', 20 );
		}

		function load_dependencies() {
			require_once ( dirname ( __FILE__ ) . '/lib/widgets.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/buttons.php' );

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
			return $output . ' xmlns:og="http://ogp.me/ns" xmlns:fb="http://ogp.me/ns/fb"';
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
				array_push( $links, '<a href="'.get_admin_url().'options-general.php?page=ngfb">'.__('Settings').'</a>' );
			}
			return $links;
		}

		// get the options, upgrade the option names (if necessary), and validate their values
		function load_options() {
			$opts = get_option( 'ngfb_options' );
			if ( $opts['ngfb_version'] != $this->version ) {
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
					if ( ! array_key_exists( $opt, $opts ) ) $opts[$opt] = $def;
				unset( $opt, $def );
			}
			ksort( $opts );
			$this->options = $this->validate_options( $opts );
		}

		// sanitize and validate input
		function validate_options( $opts ) {

			$opts['og_def_img_url'] = wp_filter_nohtml_kses( $opts['og_def_img_url'] );
			$opts['og_app_id'] = wp_filter_nohtml_kses( $opts['og_app_id'] );

			// sanitize the option by stipping off any leading URLs (leaving just the account names)
			foreach ( array( 
				'og_admins', 
			) as $opt ) $opts[$opt] = wp_filter_nohtml_kses( preg_replace( '/(http|https):\/\/[^\/]*?\//', '', $opts[$opt] ) );

			// options that must be a URL
			foreach ( array( 
				'link_pub_url',
				'og_def_img_url',
			) as $opt ) if ( $opts[$opt] && ! preg_match( '/:\/\//', $opts[$opt] ) ) 
				$opts[$opt] = $this->default_options[$opt];

			// options that must be numeric (blank or zero is ok)
			foreach ( array( 
				'og_img_max', 
				'og_vid_max', 
				'og_def_img_id',
				'og_def_author_id',
			) as $opt ) if ( $opts[$opt] && ! is_numeric( $opts[$opt] ) ) 
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
				'stumble_badge',
			) as $opt ) if ( ! $opts[$opt] || ! is_numeric( $opts[$opt] ) )
				$opts[$opt] = $this->default_options[$opt];

			if ( $opts['og_desc_len'] < NGFB_MIN_DESC_LEN ) 
				$opts['og_desc_len'] = NGFB_MIN_DESC_LEN;

			// options that cannot be blank
			foreach ( array( 
				'og_img_size', 
				'og_author_field',
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
				$opts[$opt] = wp_filter_nohtml_kses( $opts[$opt] );
				if ( ! $opts[$opt] ) $opts[$opt] = $this->default_options[$opt];
			}
		
			// true/false options
			foreach ( array( 
				'link_author',
				'og_def_img_on_index',
				'og_def_img_on_search',
				'og_ngg_tags',
				'og_page_ances_tags',
				'og_page_title_tag',
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
				'inc_og:image:width',
				'inc_og:image:height',
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
				'ngfb_skip_small_img',
			) as $opt ) { $opts[$opt] = ( $opts[$opt] ? 1 : 0 ); }

			return $opts;
		}

		function add_content_buttons( $content ) {

			$ngfbButtons = new ngfbButtons();
			$button_html = '';

			// if using the Exclude Pages plugin, skip social buttons on those pages
			if ( is_page() && $this->is_excluded() ) return $content;

			if ( is_singular() || $this->options['buttons_on_index'] ) {
				$sorted_ids = array();
				foreach ( $this->social_options_prefix as $id => $prefix )
					if ( $this->options[$prefix.'_enable'] )
						$sorted_ids[$this->options[$prefix.'_order'] . '-' . $id] = $id;	// sort by number, then by name
				ksort( $sorted_ids );
				foreach ( $sorted_ids as $id ) {
					$button_html .= eval ( "if ( method_exists( \$ngfbButtons, '${id}_button' ) ) 
						return \$ngfbButtons->${id}_button();" );
				}
				if ( $button_html ) {
					$button_html = "
<!-- NextGEN Facebook OG Social Buttons BEGIN -->
<div class=\"ngfb-content-buttons ngfb-buttons\">\n$button_html\n</div>
<!-- NextGEN Facebook OG Social Buttons END -->\n\n";
					if ( $this->options['buttons_location'] == "top" ) 
						$content = $button_html . $content;
					else $content .= $button_html;
				}
			}
			return $content;
		}

		function add_content_footer() {

			// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
			if ( is_page() && $this->is_excluded() ) return $content;

			if ( is_singular() || $this->options['buttons_on_index'] ) {
				echo "\n", '<!-- NextGEN Facebook OG Content Footer BEGIN -->', "\n";
				foreach ( $this->social_options_prefix as $id => $prefix )
					if ( $this->options[$prefix.'_enable'] ) 
						echo eval ( "if ( method_exists( \$ngfbButtons, '${id}_footer' ) ) 
							return \$ngfbButtons->${id}_footer();" );
				unset ( $id, $prefix );
				echo "\n", '<!-- NextGEN Facebook OG Content Footer END -->', "\n\n";
			}
		}

		function is_assoc( &$arr ) {
			if ( ! is_array( $arr ) ) return 0;
			return is_numeric( implode( array_keys( $arr ) ) ) ? 0 : 1;
		}

		function debug_msg( $name, $msg = '' ) {
			echo "\n", '<!-- NGFB Debug (', $name, ') ';
			if ( is_array( $msg ) ) {
				echo "\n";
				$is_assoc = $this->is_assoc( $msg );
				if ( $is_assoc ) ksort( $msg );
				foreach ( (array) $msg as $key => $val ) 
					echo $is_assoc ? "\t$key = $val\n" : "\t$val\n";
				unset ( $key, $val );
			} else echo $msg;
			echo ' -->';
		}

		function get_author_url( $author_id ) {
			switch ( $this->options['og_author_field'] ) {
				case 'url':
				case 'gplus_link':
					$url = get_the_author_meta( $this->options['og_author_field'], $author_id );
					break;
			}
			if ( ! $url ) $url = trailingslashit( site_url() ) . 'author/' . get_the_author_meta( 'user_login', $author_id ) . '/';
			return $url;
		}

		function get_ngg_xmp( $pid ) {
			if ( ! method_exists( 'nggdb', 'find_image' ) ) return;
			$xmp = array();
			$image = nggdb::find_image( $pid );
			if ( ! empty( $image ) ) {
				$meta = new nggMeta( $image->pid );
				foreach ( array(
					'email'		=> '<Iptc4xmpCore:CreatorContactInfo[^>]+?CiEmailWork="([^"]*)"',
					'created'	=> '<rdf:Description[^>]+?xmp:CreateDate="([^"]*)"',
					'modified'	=> '<rdf:Description[^>]+?xmp:ModifyDate="([^"]*)"',
					'state'		=> '<rdf:Description[^>]+?photoshop:State="([^"]*)"',
					'country'	=> '<rdf:Description[^>]+?photoshop:Country="([^"]*)"',
					'owner'		=> '<rdf:Description[^>]+?aux:OwnerName="([^"]*)"',
					'creators'	=> '<dc:creator>\s*<rdf:Seq>\s*(.*?)\s*<\/rdf:Seq>\s*<\/dc:creator>',
					'keywords'	=> '<dc:subject>\s*<rdf:Bag>\s*(.*?)\s*<\/rdf:Bag>\s*<\/dc:subject>',
					'hierarchs'	=> '<lr:hierarchicalSubject>\s*<rdf:Bag>\s*(.*?)\s*<\/rdf:Bag>\s*<\/lr:hierarchicalSubject>',
				) as $key => $regex ) {

					// get a single text string
					$xmp[$key] = preg_match( "/$regex/is", $meta->xmp_data, $match ) ? $match[1] : '';

					// if string contains a list, then re-assign the variable as an array with the list elements
					$xmp[$key] = preg_match_all( "/<rdf:li>([^>]*)<\/rdf:li>/is", $xmp[$key], $match ) ? $match[1] : $xmp[$key];

					// hierarchical keywords need to be split into a second dimension
					if ( $key == 'hierarchs' ) {
						foreach ( $xmp[$key] as $li => $val ) $xmp[$key][$li] = explode( '|', $val );
						unset ( $li, $val );
					}
				}
			}
			return $xmp;
		}

		function get_size_values( $size_name = 'thumbnail' ) {

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

		function get_quote() {
			global $post;
			if ( has_excerpt( $post->ID ) ) $page_text = get_the_excerpt( $post->ID );
			else $page_text = $post->post_content;		// fallback to regular content
			// don't run through strip_all_tags() to keep formatting and HTML (if any)
			$page_text = strip_shortcodes( $page_text );	// remove any remaining shortcodes
			$page_text = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/i', ' ', $page_text);
			return $page_text;
		}

		function get_caption( $type = 'title', $length = 300 ) {
			$caption = '';
			switch( strtolower( $type ) ) {
				case 'title':
					$caption = $this->get_title( $length, '...' );
					break;
				case 'excerpt':
					$caption = $this->get_description( $length, '...' );
					break;
				case 'both':
					$title = $this->get_title();
					$caption = $title . ' : ' . $this->get_description( $length - strlen( $title ) - 3, '...' );
					break;
			}
			return $caption;
		}

		function get_title( $textlen = 100, $trailing = '' ) {
			global $post, $page, $paged;
			$title = trim( wp_title( '|', false, 'right' ), ' |');
			if ( is_singular() ) {
				$parent_id = $post->post_parent;
				if ($parent_id) $parent_title = get_the_title($parent_id);
				if ($parent_title) $title .= ' ('.$parent_title.')';
			} elseif ( is_category() ) { 
				// wordpress does not include parents - we want the parents too
				$title = $this->str_decode( single_cat_title( '', false ) );
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
			return $this->limit_text_length( $title, $textlen, $trailing ) . $page_num;
		}

		function get_description( $textlen = 300, $trailing = '' ) {
			global $post;
			if ( is_singular() ) {
				// use the excerpt, if we have one
				if ( has_excerpt( $post->ID ) ) {
					$desc = $post->post_excerpt;
					$desc = apply_filters( 'the_excerpt', $desc );
		
				// if there's no excerpt, then use WP-WikiBox for page content (if wikibox_summary() is available and og_desc_wiki option is true)
				} elseif ( is_page() && $this->options['og_desc_wiki'] && function_exists( 'wikibox_summary' ) ) {
		
					$tag_prefix = $this->options['og_wiki_tag'];
					$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'names') );
					if ( $tags ) {
						foreach ( $tags as $tag_name ) {
							if ( $tag_prefix ) {
								if ( preg_match( "/^$tag_prefix/", $tag_name ) )
									$tag_name = preg_replace( "/^$tag_prefix/", '', $tag_name );
								else continue;	// skip tags that don't have the prefix
							}
							$desc .= wikibox_summary( $tag_name, '', false ); 
						}
					} else $desc .= wikibox_summary( the_title( '', '', false ), '', false );
				} 
		
				if ( ! $desc ) $desc = $post->post_content;		// fallback to regular content
				$desc = $this->apply_content_filter( $desc, $this->options['ngfb_filter_content'] );
		
				// ignore everything until the first paragraph tag if $this->options['og_desc_strip'] is true
				if ( $this->options['og_desc_strip'] ) $desc = preg_replace( '/^.*?<p>/', '', $desc );	// question mark makes regex un-greedy
		
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
			return $this->limit_text_length( $desc, $textlen, '...' );
		}
		
		function get_videos( &$content, $num = 0 ) {
			$videos = array();
			if ( preg_match_all( '/<iframe[^>]*? src=[\'"]([^\'"]+\/(embed|video)\/[^\'"]+)[\'"][^>]*>/i', $content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $iframe ) {
					$og_video = array();
					$og_video['og:video'] = $iframe[1];
					if ( $og_video['og:video'] ) {
						$og_video['og:video:type'] = "application/x-shockwave-flash";
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $iframe[0], $match) ) $og_video['og:video:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $iframe[0], $match) ) $og_video['og:video:height'] = $match[1];
						// define images for known websites
						if ( preg_match( '/^.*youtube\.com\/.*\/([^\/]+)$/i', $og_video['og:video'], $match ) )
							$og_video['og:image'] = 'http://img.youtube.com/vi/'.$match[1].'/0.jpg';
						array_push( $videos, $og_video );
					}
				}
			}
			if ( $num > 0 ) $videos = array_slice( $videos, 0, $num );
			return $videos;
		}

		function get_images_all( &$content, $num = 0 ) {
			$images = array();
			$size_info = $this->get_size_values( $this->options['og_img_size'] );	// the width, height, crop for the og_img_size

			// check for a featured image
			$images = array_merge( $images, $this->get_featured() );
			// stop and slice here if we have enough images
			if ( $num > 0 && count( $images ) >= $num )
				return array_slice( $images, 0, $num );

			// get images from content if singular, or allowed by options for index default
			if ( is_singular() || ( is_search() && ! $this->options['og_def_img_on_search'] ) ||
				( ! is_singular() && ! is_search() && ! $this->options['og_def_img_on_index'] ) ) {
	
				// check for singlepics on raw content
				$images = array_merge( $images, $this->get_singlepics() );
				// stop and slice here if we have enough images
				if ( $num > 0 && count( $images ) >= $num )
					return array_slice( $images, 0, $num );

				// check for img html tags on rendered content
				$images = array_merge( $images, $this->get_imgs( $content ) );
			}
			// if we didn't find any images, then use the default image
			if ( ! $images ) {
				if ( is_singular() || ( is_search() && $this->options['og_def_img_on_search'] ) ||
					( ! is_singular() && ! is_search() && $this->options['og_def_img_on_index'] ) )
						$images = array_merge( $images, $this->get_default_image() );
			}
			if ( $num > 0 ) $images = array_slice( $images, 0, $num );
			return $images;
		}

		function get_nggsearch_og( $nggsearch = array() ) {
			$images = array();
			if ( is_search() && function_exists( 'ngg_images_results' ) && have_images() ) {
				$size_info = $this->get_size_values( $this->options['og_img_size'] );	// the width, height, crop for the og_img_size
				foreach ( $nggsearch as $image ) {
					$og_image = array();
					$og_image['og:image'] = $this->get_ngg_url( 'ngg-' . $image->pid, $this->options['og_img_size'] );
					$this->debug[] = 'image_source = nggsearch / image id=' . $image->pid . ' / ' . $og_image['og:image'];
					if ( $og_image['og:image'] ) {
						if ( $size_info['width'] > 0 && $size_info['height'] > 0 ) {
							$og_image['og:image:width'] = $size_info['width'];
							$og_image['og:image:height'] = $size_info['height'];
						}
						array_push( $images, $og_image );
					}
				}
			}
			return $images;
		}

		function get_imgs( &$content ) {
			$images = array();
			$size_info = $this->get_size_values( $this->options['og_img_size'] );	// the width, height, crop for the og_img_size
			// img attributes in order of preference
			if ( preg_match_all( '/<img[^>]*? (share-'.$this->options['og_img_size'].'|share|src)=[\'"]([^\'"]+)[\'"][^>]*>/i', 
				$content, $match, PREG_SET_ORDER ) ) {
				$debug_pre = 'image_source = preg_match_all / img ';
				foreach ( $match as $img ) {
					$og_image = array();
					$src_name = $img[1];
					$og_image['og:image'] = $img[2];

					// try to determine image size from filename for NextGEN Gallery images
					if ( preg_match( '/\/cache\/[0-9]+_(crop)?_([0-9]+)x([0-9]+)_[^\/]+$/', $og_image['og:image'], $match) ) {
						$og_image['og:image:width'] = $match[2];
						$og_image['og:image:height'] = $match[3];
					} else {
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) 
							$og_image['og:image:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) 
							$og_image['og:image:height'] = $match[1];
					}
					$this->debug[] = $debug_pre . $src_name . ' / ' . $og_image['og:image'] . 
						' / width=' . $og_image['og:image:width'] . ' x height=' . $og_image['og:image:height'];

					if ( ! is_numeric( $og_image['og:image:width'] ) ) $og_image['og:image:width'] = 0;
					if ( ! is_numeric( $og_image['og:image:height'] ) ) $og_image['og:image:height'] = 0;

					// if we're picking up an img from 'src', make sure it's width and height is large enough
					if ( $src_name == 'share-' . $this->options['og_img_size'] || $src_name == 'share' || 
						( $src_name == 'src' && defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) ||
						( $src_name == 'src' && $this->options['ngfb_skip_small_img'] && 
							$og_image['og:image:width'] >= $size_info['width'] && 
							$og_image['og:image:height'] >= $size_info['height'] ) ) {

						// fix relative URLs - just in case
						if ( ! preg_match( '/:\/\//', $og_image['og:image'] ) ) {
							$this->debug[] = $debug_pre . 'relative url found = ' . $og_image['og:image'];
							if ( preg_match( '/^\//', $og_image['og:image'] ) )
								$og_image['og:image'] = site_url() . $og_image['og:image'];
							else {
								$og_image['og:image'] = $_SERVER['HTTPS'] ? 'https://' : 'http://';
								$og_image['og:image'] .= trailingslashit( $_SERVER["SERVER_NAME"] . 
									$_SERVER["REQUEST_URI"] ) . $og_image['og:image'];
							}
							$this->debug[] = $debug_pre . 'relative url fixed = ' . $og_image['og:image'];
						}
						array_push( $images, $og_image );	// everything ok, so push the image
					} else $this->debug[] = $debug_pre . $src_name . ' rejected = width and height attributes missing or too small';
				}
			}
			return $images;
		}

		function get_featured() {
			global $post;
			$images = array();
			$og_image = array();
			$size_info = $this->get_size_values( $this->options['og_img_size'] );	// the width, height, crop for the og_img_size
			if ( is_singular() && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
				$pid = get_post_thumbnail_id( $post->ID );
				$this->debug[] = 'get_post_thumbnail_id(' . $post->ID . ') = ' . $pid;
				$debug_pre = 'image_source = has_post_thumbnail / ';
				$debug_post = '('.$pid.','.$this->options['og_img_size'].')';
				// if the post thumbnail id has the form ngg- then it's a NextGEN image
				if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
					$og_image['og:image'] = $this->get_ngg_url( $pid, $this->options['og_img_size'] );
					$this->debug[] = $debug_pre . 'get_ngg_url' . $debug_post . ' / ' . $og_image['og:image'];
				} else {
					$out = wp_get_attachment_image_src( $pid, $this->options['og_img_size'] );
					$og_image['og:image'] = $out[0];
					$this->debug[] = $debug_pre . 'wp_get_attachment_image_src' . $debug_post . ' / ' . $og_image['og:image'];
				}
				if ( $og_image['og:image'] && $size_info['width'] > 0 && $size_info['height'] > 0 ) {
					$og_image['og:image:width'] = $size_info['width'];
					$og_image['og:image:height'] = $size_info['height'];
				}
			}
			// returned array must be two-dimensional
			if ( $og_image ) array_push( $images, $og_image );
			return $images;
		}

		function get_singlepics() {
			global $post;
			$images = array();
			// check for singlepics on raw content
			if ( preg_match_all( '/\[singlepic[^\]]+id=([0-9]+)/i', 
				$post->post_content, $match, PREG_SET_ORDER ) ) {
				$size_info = $this->get_size_values( $this->options['og_img_size'] );	// the width, height, crop for the og_img_size
				foreach ( $match as $singlepic ) {
					$og_image = array();
					$pid = $singlepic[1];
					$og_image['og:image'] = $this->get_ngg_url( 'ngg-' . $pid, $this->options['og_img_size'] );
					$this->debug[] = 'image_source = preg_match_all / singlepic id=' . $pid . ' / ' . $og_image['og:image'];
					if ( $og_image['og:image'] ) {
						if ( $size_info['width'] > 0 && $size_info['height'] > 0 ) {
							$og_image['og:image:width'] = $size_info['width'];
							$og_image['og:image:height'] = $size_info['height'];
						}
						array_push( $images, $og_image );
					}
				}
			}
			return $images;
		}

		function get_default_image() {
			$images = array();
			$og_image = array();
			$debug_pre = "image_source = default / ";
			if ( $this->options['og_def_img_id'] > 0 ) {
				if ($this->options['og_def_img_id_pre'] == 'ngg') {
					$pid = $this->options['og_def_img_id_pre'].'-'.$this->options['og_def_img_id'];
					$this->debug[] = $debug_pre . 'get_ngg_url(' . $pid . ',' . $this->options['og_img_size'] .')';
					$og_image['og:image'] = $this->get_ngg_url( $pid, $this->options['og_img_size'] );
				} else {
					$this->debug[] = $debug_pre . 'wp_get_attachment_image_src(' . 
						$this->options['og_def_img_id'] . ',' . $this->options['og_img_size'] . ')';
					$out = wp_get_attachment_image_src( $this->options['og_def_img_id'], $this->options['og_img_size'] );
					$og_image['og:image'] = $out[0];
				}
			}
			// if still empty, use the default url (if one is defined, empty string otherwise)
			if ( ! $og_image['og:image'] ) {
				$this->debug[] = $debug_pre . 'og_def_img_url = ' . $this->options['og_def_img_url'];
				if ( $this->options['og_def_img_url'] ) $og_image['og:image'] = $this->options['og_def_img_url'];
			}
			// returned array must be two-dimensional
			if ( $og_image ) array_push( $images, $og_image );
			return $images;
		}

		function get_tags() {
			$og_tags = array();
			if ( is_singular() ) {
				global $post;
				$og_tags = array_merge( $og_tags, $this->get_wp_tags( $post->ID ) );
				if ( $this->options['og_ngg_tags'] && function_exists('has_post_thumbnail') && has_post_thumbnail( $post->ID ) ) {
					$pid = get_post_thumbnail_id( $post->ID );
					if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' )
						$og_tags = array_merge( $og_tags, $this->get_ngg_tags( $pid ) );
				}
			} elseif ( is_search() )
				$og_tags = preg_split( '/ *, */', get_search_query( false ) );
		
			return array_unique( $og_tags );	// filter for duplicate element values - just in case
		}

		function get_wp_tags( $post_id ) {
			$og_tags = array();
			$post_ids = array ( $post_id );	// array of one
			if ( $this->options['og_page_ances_tags'] && is_page( $post_id ) )
				$post_ids = array_merge( $post_ids, get_post_ancestors( $post_id ) );
			$tag_prefix = isset( $this->options['og_wiki_tag'] ) ? $this->options['og_wiki_tag'] : '';
			foreach ( $post_ids as $id ) {
				if ( $this->options['og_page_title_tag'] && is_page( $id ) )
					$og_tags[] = get_the_title( $id );
				foreach ( wp_get_post_tags( $id, array( 'fields' => 'names') ) as $tag_name ) {
					if ( $this->options['og_desc_wiki'] && $tag_prefix ) 
						$tag_name = preg_replace( "/^$tag_prefix/", '', $tag_name );
					$og_tags[] = $tag_name;
				}
			}
			return array_unique( array_map('strtolower', $og_tags ) );	// filter for duplicate (lowercase) element values - just in case
		}

		function get_ngg_tags( $pid ) {
			$tags = array();
			if ( method_exists( 'nggdb', 'find_image' ) && is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' )
				$tags = wp_get_object_terms( substr( $pid, 4 ), 'ngg_tag', 'fields=names' );
			return $tags;
		}

		function add_meta_tags() {
			if ( $this->options['ngfb_debug'] ) $this->debug_msg( __FUNCTION__ , $this->options );
			if ( ( defined( 'DISABLE_NGFB_OPEN_GRAPH' ) && DISABLE_NGFB_OPEN_GRAPH ) || 
				( defined( 'NGFB_OPEN_GRAPH_DISABLE' ) && NGFB_OPEN_GRAPH_DISABLE ) ) {
				echo "\n<!-- NextGEN Facebook OG Meta Tags DISABLED -->\n\n";
				return;
			}
			global $post;
			$content_filtered = $this->apply_content_filter( $post->post_content, $this->options['ngfb_filter_content'] );
			$og['fb:admins'] = $this->options['og_admins'];
			$og['fb:app_id'] = $this->options['og_app_id'];
			$og['og:url'] = $_SERVER['HTTPS'] ? 'https://' : 'http://';
			$og['og:url'] .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			$og['og:site_name'] = get_bloginfo( 'name', 'display' );	
			$og['og:title'] = $this->get_title( $this->options['og_title_len'], '...' );
			$og['og:description'] = $this->get_description( $this->options['og_desc_len'], '...' );
			if ( $this->options['og_img_max'] > 0 ) 
				$og['og:image'] = $this->get_images_all( $content_filtered, $this->options['og_img_max'] );
			if ( $this->options['og_vid_max'] > 0 ) 
				$og['og:video'] = $this->get_videos( $content_filtered, $this->options['og_vid_max'] );
			$og['article:tag'] = $this->get_tags();
		
			if ( $post->post_author || $this->options['og_def_author_id'] ) {
				$og['og:type'] = "article";
				$og['article:section'] = $this->options['og_art_section'];
				$og['article:modified_time'] = get_the_modified_date('c');
				$og['article:published_time'] = get_the_date('c');
				if ( $post->post_author )
					$og['article:author'] = $this->get_author_url( $post->post_author );
				elseif ( $this->options['og_def_author_id'] )
					$og['article:author'] = $this->get_author_url( $this->options['og_def_author_id'] );
			} else $og['og:type'] = "website";
		
			// add the Open Graph meta tags
			$this->og_meta_html( $og );
		}

		function og_meta_html( &$arr = array() ) {
			// output whatever debug info we have before printing the open graph meta tags
			if ( $this->options['ngfb_debug'] ) $this->debug_msg( __FUNCTION__ , $this->debug );
			if ( $this->options['ngfb_debug'] ) $this->debug_msg( __FUNCTION__ , "\n".print_r( $arr, true ) );
		
			echo "\n<!-- NextGEN Facebook OG Meta Tags BEGIN -->\n";
			if ( $this->options['link_pub_url'] )
				echo '<link rel="publisher" href="', $this->options['link_pub_url'], '" />', "\n";

			if ( $this->options['link_author'] && $arr['article:author'] )
				echo '<link rel="author" href="', $arr['article:author'], '" />', "\n";

			ksort( $arr );
			foreach ( $arr as $d_name => $d_val ) {						// first-dimension array (associative)
				if ( is_array ( $d_val ) ) {
					foreach ( $d_val as $dd_num => $dd_val ) {			// second-dimension array
						if ( $this->is_assoc( $dd_val ) ) {
							ksort( $dd_val );
							foreach ( $dd_val as $ddd_name => $ddd_val ) {	// third-dimension array (associative)
								echo $this->get_meta_html( $ddd_name, $ddd_val, $d_name . ':' . ( $dd_num + 1 ) );
							}
							unset ( $ddd_name, $ddd_val );
						} else echo $this->get_meta_html( $d_name, $dd_val, $d_name . ':' . ( $dd_num + 1 ) );
					}
					unset ( $dd_num, $dd_val );
				} else echo $this->get_meta_html( $d_name, $d_val );
			}
			unset ( $d_name, $d_val );
			echo "<!-- NextGEN Facebook OG Meta Tags END -->\n\n";
		}

		function get_meta_html( $name, $val = '', $cmt = '' ) {
			$meta = '';
			if ( $this->options['inc_'.$name] && $val ) {
				$charset = get_bloginfo( 'charset' );
				$val = htmlentities( $this->strip_all_tags( $this->str_decode( $val ) ), ENT_QUOTES, $charset, false );
				if ( $cmt ) $meta .= "<!-- $cmt -->";
				$meta .= '<meta property="' . $name . '" content="' . $val . '" />';
				$meta .= "\n";
			}
			return $meta;
		}

		function get_social_buttons( $ids = array(), $attr = array() ) {
			global $post;
			$ngfbButtons = new ngfbButtons();
			$button_html = '';
			// make sure we have at least $post->ID or $attr['url'] defined
			if ( ! isset( $post->ID ) && empty( $attr['url' ] ) ) {
				$attr['url'] = $_SERVER['HTTPS'] ? 'https://' : 'http://';
				$attr['url'] .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
			}
			foreach ( $ids as $id ) {
				$id = preg_replace( '/[^a-z]/', '', $id );	// sanitize input before eval
				$button_html .= eval ( "if ( method_exists( \$ngfbButtons, '${id}_button' ) ) 
					return \$ngfbButtons->${id}_button( \$attr );" );
				$button_html .= eval ( "if ( method_exists( \$ngfbButtons, '${id}_footer' ) ) 
					return \$ngfbButtons->${id}_footer();" );
			}
			if ( $button_html ) $button_html = '
<!-- NextGEN Facebook OG Social Buttons BEGIN -->
<div class="ngfb-buttons">
' . $button_html . '
</div>
<!-- NextGEN Facebook OG Social Buttons END -->'."\n\n";
			return $button_html;
		}

		function apply_content_filter( $content, $filter_content = true ) {
			// the_content filter breaks the ngg album shortcode, so skip it if that shortcode if found
			if ( ! preg_match( '/\[ *album[ =]/', $content ) && $filter_content ) {
				// temporarily remove add_content_buttons() to prevent recursion
				$filter_removed = remove_filter( 'the_content', 
					array( $this, 'add_content_buttons' ), NGFB_CONTENT_PRIORITY );
				$content = apply_filters( 'the_content', $content );
				if ( $filter_removed ) add_filter( 'the_content', 
					array( $this, 'add_content_buttons' ), NGFB_CONTENT_PRIORITY );
			}
			$content = preg_replace( '/[\r\n\t ]+/s', ' ', $content );	// put everything on one line
			$content = str_replace( ']]>', ']]&gt;', $content );
			$content = preg_replace( '/<a +rel="author" +href="" +style="display:none;">Google\+<\/a>/', ' ', $content );
			return $content;
		}

		// called to get an image URL from an NGG picture ID and a media size name (the pid must be formatted as 'ngg-#')
		function get_ngg_url( $pid, $size_name = 'thumbnail' ) {
			if ( ! method_exists( 'nggdb', 'find_image' ) ) return;
			if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
				$pid = substr( $pid, 4 );
				$image = nggdb::find_image( $pid );	// returns an nggImage object
				if ( ! empty( $image ) ) {
					$size_info = $this->get_size_values( $size_name );
					$crop = ( $size_info['crop'] == 1 ? 'crop' : '' );
					$image_url = $image->cached_singlepic_file( $size_info['width'], $size_info['height'], $crop );
					if ( empty( $image_url ) )	// if the image file doesn't exist, use the dynamic image url
						$image_url = trailingslashit( site_url() ) . 
							'index.php?callback=image&amp;pid=' . $pid .
							'&amp;width=' . $size_info['width'] . 
							'&amp;height=' . $size_info['height'] . 
							'&amp;mode=' . $crop;
				}
			}
			return $image_url;
		}

		function cdn_linker_rewrite( $url = '' ) {
			if ( class_exists( CDNLinksRewriterWordpress ) ) {
				$rewriter = new CDNLinksRewriterWordpress();
				$url = '"'.$url.'"';	// rewrite uses pointer
				$url = trim( $rewriter->rewrite( $url ), "\"" );
			}
			return $url;
		}

		function is_excluded() {
			global $post;
			if ( is_page() && $post->ID && function_exists( 'ep_get_excluded_ids' ) && ! $this->options['buttons_on_ex_pages'] ) {
				$excluded_ids = ep_get_excluded_ids();
				$delete_ids = array_unique( $excluded_ids );
				if ( in_array( $post->ID, $delete_ids ) ) return true;
			}
			return false;
		}

		function strip_all_tags( $text ) {
			$text = strip_shortcodes( $text );					// remove any remaining shortcodes
			$text = preg_replace( '/<\?.*\?>/i', ' ', $text);			// remove php
			$text = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/i', ' ', $text);	// remove javascript
			$text = strip_tags( $text );						// remove html tags
			return trim( $text );
		}

		function limit_text_length( $text, $textlen = 300, $trailing = '' ) {
			$text = preg_replace( '/[\r\n\t ]+/s', ' ', $text );			// put everything on one line
			$text = preg_replace( '/<\/p>/i', ' ', $text);				// replace end of paragraph with a space
			$text = $this->strip_all_tags( $text );					// remove any remaining html tags
			if ( strlen( $text ) > $textlen ) {
				$text = substr( $text, 0, $textlen - strlen( $trailing ) );
				$text = trim( preg_replace( '/[^ ]*$/', '', $text ) );		// remove trailing bits of words
				$text = preg_replace( '/[,\.]*$/', '', $text );			// remove trailing puntuation
			} else $trailing = '';							// truncate trailing string if text is shorter than limit
			$text = esc_attr( $text ) . $trailing;					// trim and add trailing string (if provided)
			return $text;
		}

		function str_decode( $str ) {
			$str = preg_replace('/&#8230;/', '...', $str );
			return preg_replace('/&#\d{2,5};/ue', "$this->utf8_entity_decode('$0')", $str );
		}

		function utf8_entity_decode( $entity ) {
			$convmap = array( 0x0, 0x10000, 0, 0xfffff );
			return mb_decode_numericentity( $entity, $convmap, 'UTF-8' );
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
if ( ! function_exists( 'ngfb_get_social_buttons' ) ) {
	function ngfb_get_social_buttons( $ids = array(), $attr = array() ) {
		global $ngfb;
		return $ngfb->get_social_buttons( $ids, $attr );
	}
}

?>
